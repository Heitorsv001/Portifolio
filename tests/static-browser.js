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
const mime = { '.html': 'text/html; charset=utf-8', '.css': 'text/css; charset=utf-8', '.js': 'text/javascript; charset=utf-8', '.png': 'image/png', '.pdf': 'application/pdf', '.xml': 'application/xml', '.txt': 'text/plain' };
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
          const state = await page.evaluate(() => ({
            theme: document.documentElement.dataset.theme,
            overflow: document.documentElement.scrollWidth > window.innerWidth,
            headings: document.querySelectorAll('h1').length,
          }));
          expect(state.theme === theme && !state.overflow && state.headings === 1, `Layout ${width}px/${theme}/${route}`);
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
  await particles.close();
  const noScript = await browser.newContext({ javaScriptEnabled: false, viewport: { width: 390, height: 900 } });
  const fallback = await noScript.newPage();
  await fallback.goto(`${staticOrigin}${base}/`);
  expect(await fallback.locator('h1').isVisible() && await fallback.locator('a[download]').isVisible(), 'Conteúdo e currículo disponíveis sem JavaScript');
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
