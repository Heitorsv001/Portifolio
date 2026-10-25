const assert = require('node:assert/strict');
const fs = require('node:fs/promises');
const http = require('node:http');
const net = require('node:net');
const path = require('node:path');
const { spawn } = require('node:child_process');
const { chromium } = require('playwright');

const root = path.resolve(__dirname, '..');
const dist = path.join(root, 'dist');
const base = (process.env.APP_BASE_PATH || '').replace(/\/$/, '');
const routes = ['/', '/projetos/', '/projetos/crowns/', '/inexistente/profundo/'];
const mime = { '.html': 'text/html; charset=utf-8', '.css': 'text/css; charset=utf-8', '.js': 'text/javascript; charset=utf-8', '.png': 'image/png', '.svg': 'image/svg+xml', '.pdf': 'application/pdf', '.xml': 'application/xml', '.txt': 'text/plain' };
let checks = 0;
let browser;
let php;
let server;
let phpLog = '';

function expect(condition, message) {
  checks += 1;
  assert.ok(condition, message);
}

async function availablePort() {
  const socket = net.createServer();
  await new Promise((resolve) => socket.listen(0, '127.0.0.1', resolve));
  const port = socket.address().port;
  await new Promise((resolve) => socket.close(resolve));
  return port;
}

async function main() {
  await fs.access(path.join(dist, 'index.html'));
  server = http.createServer(async (request, response) => {
    try {
      const pathname = decodeURIComponent(new URL(request.url, 'http://localhost').pathname);
      const insideBase = base === '' || pathname === base || pathname.startsWith(`${base}/`);
      const relative = insideBase ? pathname.slice(base.length) : '/__outside_base__';
      let file = path.resolve(dist, `.${relative}`);
      let status = 200;
      if (file !== dist && !file.startsWith(`${dist}${path.sep}`)) throw new Error('Caminho fora de dist');
      try {
        const stat = await fs.stat(file);
        if (stat.isDirectory()) {
          if (!pathname.endsWith('/')) {
            response.writeHead(301, { Location: `${pathname}/${new URL(request.url, 'http://localhost').search}` });
            response.end();
            return;
          }
          file = path.join(file, 'index.html');
        }
        await fs.access(file);
      } catch {
        file = path.join(dist, '404.html');
        status = 404;
      }
      const body = await fs.readFile(file);
      response.writeHead(status, { 'Content-Type': mime[path.extname(file)] || 'application/octet-stream', 'Content-Length': body.length });
      response.end(request.method === 'HEAD' ? undefined : body);
    } catch {
      response.writeHead(500);
      response.end();
    }
  });
  await new Promise((resolve) => server.listen(0, '127.0.0.1', resolve));
  const staticOrigin = `http://127.0.0.1:${server.address().port}`;
  const phpPort = await availablePort();
  const phpOrigin = `http://127.0.0.1:${phpPort}`;
  php = spawn(process.env.PHP_BINARY || 'php', ['-S', `127.0.0.1:${phpPort}`, '-t', 'public'], {
    cwd: root,
    env: { ...process.env, APP_BASE_PATH: base, APP_STATIC_EXPORT: '1' },
    stdio: ['ignore', 'pipe', 'pipe'],
  });
  php.stdout.on('data', (chunk) => { phpLog += chunk; });
  php.stderr.on('data', (chunk) => { phpLog += chunk; });
  for (let attempt = 0; ; attempt += 1) {
    try {
      const response = await fetch(`${phpOrigin}${base}/`);
      if (response.status !== 200) throw new Error(`PHP retornou ${response.status}`);
      break;
    } catch (error) {
      if (attempt >= 50 || php.exitCode !== null) throw new Error(`${error.message}\n${phpLog}`);
      await new Promise((resolve) => setTimeout(resolve, 100));
    }
  }

  for (const origin of [staticOrigin, phpOrigin]) {
    for (const route of ['/', '/projetos', '/projetos/', '/projetos/gerenciador-de-permanencias/', '/projetos/crowns/', '/projetos/festividades-de-fim-de-ano/', '/projetos/noticias/', '/projetos/portfolio-mvc-php/', '/inexistente/profundo/']) {
      const response = await fetch(`${origin}${base}${route}`);
      const expected = /portfolio-mvc-php|inexistente/.test(route) ? 404 : 200;
      expect(response.status === expected, `Status ${expected}: ${origin}${base}${route}`);
      const html = await response.text();
      expect(html.includes('<html lang="pt-BR"'), 'Resposta contém a view real');
      if (expected === 404) expect(html.includes('Página não encontrada') && html.includes('noindex, follow'), '404 real com conteúdo e noindex');
    }
  }
  async function assetPaths(directory) {
    const files = [];
    for (const entry of await fs.readdir(directory, { withFileTypes: true })) {
      if (entry.isDirectory()) files.push(...await assetPaths(path.join(directory, entry.name)));
      else files.push(path.join(directory, entry.name));
    }
    return files;
  }
  for (const file of await assetPaths(path.join(dist, 'assets'))) {
    const relative = path.relative(dist, file).split(path.sep).join('/');
    const response = await fetch(`${staticOrigin}${base}/${relative}`);
    expect(response.status === 200, `Asset HTTP 200: ${relative}`);
    expect(response.headers.get('content-type').startsWith(mime[path.extname(file)].split(';')[0]), `MIME do asset: ${relative}`);
    expect(Buffer.from(await response.arrayBuffer()).equals(await fs.readFile(path.join(root, 'public', relative))), `Bytes do asset: ${relative}`);
  }

  browser = await chromium.launch({ executablePath: process.env.CHROME_BIN || undefined, headless: true });
  const errors = [];
  const observe = (page) => {
    page.on('pageerror', (error) => errors.push(error.message));
    page.on('requestfailed', (request) => errors.push(`${request.url()}: ${request.failure()?.errorText}`));
    page.on('console', (message) => {
      if (message.type() === 'error' && !message.text().includes('404')) errors.push(message.text());
    });
    page.on('response', (response) => {
      if (response.status() >= 400 && !response.url().includes('/inexistente/')) errors.push(`${response.status()}: ${response.url()}`);
    });
  };
  for (const width of [320, 390, 768, 1440]) {
    for (const theme of ['light', 'dark']) {
      const context = await browser.newContext({ viewport: { width, height: 900 }, reducedMotion: 'reduce' });
      await context.addInitScript((value) => localStorage.setItem('portfolio-theme', value), theme);
      const page = await context.newPage();
      observe(page);
      for (const route of routes) {
        const screenshots = [];
        for (const origin of [phpOrigin, staticOrigin]) {
          await page.goto(`${origin}${base}${route}`, { waitUntil: 'networkidle' });
          await page.evaluate(async () => {
            await document.fonts.ready;
            await Promise.all(Array.from(document.images, (image) => image.decode()));
          });
          const state = await page.evaluate(() => {
            const track = document.querySelector('.skills-track');
            const gap = track ? parseFloat(getComputedStyle(track).columnGap) : 0;
            return {
              theme: document.documentElement.dataset.theme,
              overflow: document.documentElement.scrollWidth > window.innerWidth,
              headings: document.querySelectorAll('h1').length,
              skillColumns: track
                ? Math.round((track.clientWidth + gap) / (track.firstElementChild.getBoundingClientRect().width + gap))
                : 0,
            };
          });
          expect(state.theme === theme && !state.overflow && state.headings === 1, `Layout ${width}px/${theme}/${route}`);
          if (route === '/') {
            const visibleCards = width >= 1024 ? 3 : width >= 768 ? 2 : 1;
            expect(state.skillColumns === visibleCards, `Carrossel com ${visibleCards} cards visíveis: ${width}px/${theme}`);
          }
          screenshots.push(await page.screenshot({ fullPage: true, animations: 'disabled' }));
        }
        expect(screenshots[0].equals(screenshots[1]), `Imagem PHP/estático idêntica: ${width}px/${theme}/${route}`);
        if (process.env.STATIC_SCREENSHOT_DIR && (width === 390 || width === 1440) && route === '/') {
          await fs.mkdir(process.env.STATIC_SCREENSHOT_DIR, { recursive: true });
          await fs.writeFile(path.join(process.env.STATIC_SCREENSHOT_DIR, `home-${width}-${theme}.png`), screenshots[1]);
        }
      }
      await context.close();
    }
  }

  const context = await browser.newContext({ viewport: { width: 390, height: 900 }, colorScheme: 'dark', reducedMotion: 'reduce' });
  const page = await context.newPage();
  observe(page);
  await page.goto(`${staticOrigin}${base}/`, { waitUntil: 'networkidle' });
  await page.keyboard.press('Tab');
  expect(await page.locator('.skip-link').evaluate((element) => element === document.activeElement), 'Tab alcança skip link');
  await page.keyboard.press('Enter');
  expect(await page.locator('#conteudo').evaluate((element) => element === document.activeElement), 'Enter move foco ao conteúdo');
  const menu = page.locator('[data-menu-toggle]');
  await menu.focus();
  await page.keyboard.press('Enter');
  expect(await menu.getAttribute('aria-expanded') === 'true', 'Menu abre pelo teclado');
  await page.keyboard.press('Escape');
  expect(await menu.getAttribute('aria-expanded') === 'false' && await menu.evaluate((element) => element === document.activeElement), 'Escape fecha menu e restaura foco');
  await menu.click();
  await page.locator('#navegacao-principal a[href$="#contato"]').click();
  expect(await menu.getAttribute('aria-expanded') === 'false', 'Âncora fecha menu');
  expect(await page.locator('#contato').evaluate((element) => element === document.activeElement), 'Âncora mantém foco acessível');
  await page.locator('[data-theme-toggle]').click();
  expect(await page.locator('html').getAttribute('data-theme') === 'light', 'Botão altera tema');
  await page.reload({ waitUntil: 'networkidle' });
  expect(await page.locator('html').getAttribute('data-theme') === 'light', 'Tema persiste ao recarregar');
  const [download] = await Promise.all([page.waitForEvent('download'), page.locator('a[download]').click()]);
  expect(download.suggestedFilename() === 'CurriculoLinkedin.pdf', 'Download mantém nome do currículo');
  expect((await fs.readFile(await download.path())).equals(await fs.readFile(path.join(root, 'public/assets/documents/CurriculoLinkedin.pdf'))), 'Download mantém bytes do PDF');
  const track = page.locator('[data-carousel-track]');
  await track.scrollIntoViewIfNeeded();
  expect(await page.locator('#habilidades button').count() === 0, 'Carrossel utiliza somente a barra de rolagem, sem botões');
  expect(await page.locator('[data-carousel-clone]').count() === 0, 'Movimento reduzido mantém somente os cards originais');
  expect(await page.locator('.skills-viewport').evaluate(element =>
    ['::before', '::after'].every(side => {
      const style = getComputedStyle(element, side);
      return style.backgroundImage.includes('linear-gradient') && style.pointerEvents === 'none';
    })), 'Gradientes nas duas laterais preservam a interação com a barra');
  await track.hover();
  await page.mouse.wheel(320, 0);
  await page.waitForFunction(() => document.querySelector('[data-carousel-track]').scrollLeft > 0);
  expect(true, 'Rolagem horizontal nativa permanece disponível com movimento reduzido');
  await track.focus();
  await page.keyboard.press('End');
  await page.waitForFunction(() => {
    const element = document.querySelector('[data-carousel-track]');
    return element.scrollLeft >= element.scrollWidth - element.clientWidth - 1;
  });
  expect(true, 'Tecla End alcança os últimos cards');
  await page.keyboard.press('Home');
  await page.waitForFunction(() => document.querySelector('[data-carousel-track]').scrollLeft < 1);
  await page.keyboard.press('ArrowRight');
  await page.waitForFunction(() => document.querySelector('[data-carousel-track]').scrollLeft > 0);
  await page.keyboard.press('ArrowLeft');
  await page.waitForFunction(() => document.querySelector('[data-carousel-track]').scrollLeft < 1);
  expect(true, 'Home e setas permitem navegação pelo teclado');
  await page.keyboard.press('ArrowLeft');
  await page.waitForFunction(() => {
    const element = document.querySelector('[data-carousel-track]');
    return element.scrollLeft >= element.scrollWidth - element.clientWidth - 1;
  });
  await page.keyboard.press('ArrowRight');
  await page.waitForFunction(() => document.querySelector('[data-carousel-track]').scrollLeft < 1);
  expect(true, 'Teclado permite circular entre o início e o fim');
  const scrollbar = page.getByRole('scrollbar', { name: 'Rolagem de habilidades e tecnologias' });
  expect(await scrollbar.isVisible() && await scrollbar.getAttribute('aria-controls') === 'lista-habilidades',
    'Barra personalizada acessível controla os cards');
  const scrollbarBounds = await scrollbar.boundingBox();
  await page.mouse.move(scrollbarBounds.x + 10, scrollbarBounds.y + scrollbarBounds.height / 2);
  await page.mouse.down();
  await page.mouse.move(scrollbarBounds.x + scrollbarBounds.width * 0.6, scrollbarBounds.y + scrollbarBounds.height / 2, { steps: 10 });
  await page.mouse.up();
  expect(await track.evaluate(element => element.scrollLeft) > 10 && Number(await scrollbar.getAttribute('aria-valuenow')) > 0,
    'Arraste da barra move os cards e atualiza o valor acessível');
  await scrollbar.focus();
  await page.keyboard.press('End');
  await page.waitForFunction(() => document.querySelector('[data-carousel-scrollbar]').getAttribute('aria-valuenow') === '100');
  await page.keyboard.press('Home');
  await page.waitForFunction(() => document.querySelector('[data-carousel-track]').scrollLeft < 1);
  expect(true, 'Barra também permite navegação pelo teclado');
  await context.close();

  const particles = await browser.newContext({ viewport: { width: 1440, height: 900 }, reducedMotion: 'no-preference' });
  const particlesPage = await particles.newPage();
  observe(particlesPage);
  await particlesPage.goto(`${staticOrigin}${base}/`, { waitUntil: 'networkidle' });
  await particlesPage.waitForFunction(() => {
    const canvas = document.querySelector('[data-hero-particles]');
    return canvas.width > 0 && canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height).data.some((value, index) => index % 4 === 3 && value > 0);
  });
  expect(true, 'Partículas desenham no Canvas real');
  await particlesPage.emulateMedia({ reducedMotion: 'reduce' });
  await particlesPage.waitForFunction(() => document.querySelector('[data-hero-particles]').width === 0);
  expect(true, 'Movimento reduzido libera o Canvas');
  await particlesPage.emulateMedia({ reducedMotion: 'no-preference' });
  await particlesPage.waitForFunction(() => document.querySelector('[data-hero-particles]').width > 0);
  expect(true, 'Partículas retomam após mudança de preferência');
  const automaticTrack = particlesPage.locator('[data-carousel-track]');
  await automaticTrack.scrollIntoViewIfNeeded();
  await automaticTrack.hover();
  await automaticTrack.focus();
  const initialPosition = await automaticTrack.evaluate(element => element.scrollLeft);
  await particlesPage.waitForFunction(position => document.querySelector('[data-carousel-track]').scrollLeft > position + 10, initialPosition);
  expect(true, 'Rolagem automática avança mesmo com ponteiro e foco no carrossel');
  expect(await automaticTrack.evaluate(element => {
    const copies = [...element.querySelectorAll('[data-carousel-clone]')];
    return copies.length > 0 && copies.length * 2 === element.children.length
      && copies.every(copy => copy.getAttribute('aria-hidden') === 'true' && copy.hasAttribute('inert'));
  }), 'Repetição visual não duplica conteúdo para leitores de tela ou foco');
  await particlesPage.mouse.down();
  const heldPosition = await automaticTrack.evaluate(element => element.scrollLeft);
  await particlesPage.waitForTimeout(400);
  expect(await automaticTrack.evaluate(element => element.scrollLeft) === heldPosition, 'Interação manual suspende a rolagem automática');
  await particlesPage.mouse.up();
  await particlesPage.waitForFunction(position => document.querySelector('[data-carousel-track]').scrollLeft > position + 10, heldPosition);
  expect(true, 'Rolagem automática retoma após a interação manual');
  await particlesPage.mouse.down();
  await automaticTrack.evaluate(element => {
    const copy = element.querySelector('[data-carousel-clone]');
    const cycleWidth = copy.getBoundingClientRect().left - element.firstElementChild.getBoundingClientRect().left;
    element.scrollLeft = cycleWidth - 2;
  });
  await particlesPage.waitForFunction(() =>
    parseFloat(document.querySelector('[data-scrollbar-rail]').style.getPropertyValue('--scroll-thumb-position')) > 99);
  const leadingCardState = (element) => {
    const bounds = element.getBoundingClientRect();
    const center = bounds.left + element.firstElementChild.getBoundingClientRect().width / 2;
    const card = [...element.children].find(item => {
      const rect = item.getBoundingClientRect();
      return rect.left <= center && rect.right > center;
    });
    return { title: card.querySelector('h3').textContent, left: card.getBoundingClientRect().left - bounds.left };
  };
  const beforeLoop = await automaticTrack.evaluate(leadingCardState);
  const scrollbarSegments = (element) => {
    const bounds = element.querySelector('[data-scrollbar-rail]').getBoundingClientRect();
    return [...element.querySelectorAll('.skills-scrollbar-thumb')].map(thumb => {
      const rect = thumb.getBoundingClientRect();
      return { left: Math.max(bounds.left, rect.left) - bounds.left, right: Math.min(bounds.right, rect.right) - bounds.left };
    }).filter(segment => segment.right > segment.left).sort((a, b) => (b.right - b.left) - (a.right - a.left));
  };
  const automaticScrollbar = particlesPage.locator('[data-carousel-scrollbar]');
  const beforeScrollbar = await automaticScrollbar.evaluate(scrollbarSegments);
  await particlesPage.mouse.up();
  await particlesPage.waitForFunction(() => document.querySelector('[data-carousel-track]').scrollLeft < 10);
  const afterLoop = await automaticTrack.evaluate(leadingCardState);
  expect(beforeLoop.title === afterLoop.title && Math.abs(beforeLoop.left - afterLoop.left) < 12,
    'Loop infinito preserva os cards visíveis e a continuidade do movimento');
  const afterScrollbar = await automaticScrollbar.evaluate(scrollbarSegments);
  expect(beforeScrollbar.length === 2 && afterScrollbar.length === 1
    && Math.abs(beforeScrollbar[0].left - afterScrollbar[0].left) < 12
    && Math.abs(beforeScrollbar[0].right - afterScrollbar[0].right) < 12,
    'Indicador atravessa as duas extremidades sem salto ao reiniciar o ciclo');
  await particlesPage.emulateMedia({ reducedMotion: 'reduce' });
  await particlesPage.waitForTimeout(100);
  const reducedPosition = await automaticTrack.evaluate(element => element.scrollLeft);
  await particlesPage.waitForTimeout(400);
  expect(await automaticTrack.evaluate(element => element.scrollLeft) === reducedPosition, 'Movimento reduzido interrompe a rolagem automática');
  expect(await particlesPage.locator('[data-carousel-clone]').count() === 0, 'Alteração para movimento reduzido remove as cópias visuais');
  await particles.close();
  const noScript = await browser.newContext({ javaScriptEnabled: false, viewport: { width: 390, height: 900 } });
  const fallback = await noScript.newPage();
  await fallback.goto(`${staticOrigin}${base}/`);
  expect(await fallback.locator('h1').isVisible() && await fallback.locator('a[download]').isVisible(), 'Conteúdo e currículo disponíveis sem JavaScript');
  expect(await fallback.locator('.skill-category').count() > 0
    && await fallback.locator('#habilidades button').count() === 0, 'Cards e rolagem nativa continuam disponíveis sem JavaScript e sem botões');
  expect(await fallback.locator('[data-carousel-scrollbar]').isHidden()
    && await fallback.locator('.skills-track').evaluate(element => getComputedStyle(element).scrollbarWidth !== 'none'),
    'Sem JavaScript, a barra nativa substitui a barra personalizada');
  await noScript.close();
  expect(errors.length === 0, `Sem erros inesperados de console/rede: ${errors.join('; ')}`);
  console.log(`${checks}/${checks} verificações HTTP/navegador aprovadas; 32 comparações visuais PHP/estático idênticas.`);
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
}).finally(async () => {
  if (browser) await browser.close();
  if (php) {
    php.kill();
    if (php.exitCode === null) await new Promise((resolve) => php.once('exit', resolve));
  }
  if (server) await new Promise((resolve) => server.close(resolve));
});
