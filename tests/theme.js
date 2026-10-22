function runThemeTests(initSource, siteSource) {
  let checks = 0;
  const expect = (condition, message) => {
    checks += 1;
    if (!condition) {
      throw new Error(message);
    }
  };

  const createPage = (options = {}) => {
    const storage = options.storage || new Map();
    const dataset = {};
    const attributes = {};
    const metaAttributes = {};
    const events = {};
    let systemListener;
    const media = {
      matches: options.systemLight === true,
    };
    const listen = (callback) => { systemListener = callback; };
    if (options.legacyMedia) {
      media.addListener = listen;
    } else {
      media.addEventListener = (name, callback) => { listen(callback); };
    }
    const button = {
      hidden: true,
      setAttribute: (name, value) => { attributes[name] = value; },
      addEventListener: (name, callback) => { events[name] = callback; },
    };
    const document = {
      documentElement: { dataset },
      querySelector: (selector) => {
        if (selector === 'meta[name="theme-color"]') {
          return options.noThemeColor ? null : {
            setAttribute: (name, value) => { metaAttributes[name] = value; },
          };
        }
        return selector === '[data-theme-toggle]' && !options.noButton ? button : null;
      },
    };
    const window = {
      getComputedStyle: () => ({
        getPropertyValue: () => dataset.theme === 'light' ? '#f8f8fc' : '#17171c',
      }),
      localStorage: {
        getItem: (key) => {
          if (options.readBlocked) throw new Error('Armazenamento bloqueado');
          return storage.get(key) ?? null;
        },
        setItem: (key, value) => {
          if (options.writeBlocked) throw new Error('Armazenamento bloqueado');
          storage.set(key, value);
        },
      },
      matchMedia: () => {
        if (options.mediaThrows) throw new Error('Preferência indisponível');
        return media;
      },
    };
    if (options.noMedia) delete window.matchMedia;

    if (!options.skipInit) new Function('window', 'document', initSource)(window, document);
    const initialTheme = dataset.theme;
    new Function('window', 'document', siteSource)(window, document);

    return {
      dataset, attributes, metaAttributes, storage, button, initialTheme,
      click: () => events.click(),
      changeSystem: (matches) => {
        media.matches = matches;
        if (systemListener) systemListener(media);
      },
    };
  };

  for (const saved of [null, 'dark', 'light', 'invalid', '']) {
    for (const systemLight of [false, true]) {
      const storage = new Map(saved === null ? [] : [['portfolio-theme', saved]]);
      const page = createPage({ storage, systemLight });
      const theme = saved === 'dark' || saved === 'light' ? saved : systemLight ? 'light' : 'dark';
      const target = theme === 'light' ? 'escuro' : 'claro';
      expect(page.initialTheme === theme, 'Tema correto antes das interações');
      expect(page.attributes['aria-label'] === `Alternar para tema ${target}`, 'Rótulo anuncia o destino');
      expect(page.button.hidden === false, 'Controle disponível com JS');
      expect(page.metaAttributes.content === (theme === 'light' ? '#f8f8fc' : '#17171c'), 'Cor sugerida ao navegador acompanha o tema inicial');
    }
  }

  const automatic = createPage();
  automatic.changeSystem(true);
  expect(automatic.dataset.theme === 'light', 'Acompanha sistema sem escolha manual');
  expect(automatic.attributes['aria-label'] === 'Alternar para tema escuro', 'Rótulo acompanha sistema');
  expect(automatic.metaAttributes.content === '#f8f8fc', 'Cor sugerida acompanha preferência do sistema');
  automatic.changeSystem(false);
  expect(automatic.dataset.theme === 'dark', 'Sistema retorna ao escuro');
  automatic.click();
  expect(automatic.dataset.theme === 'light', 'Clique aplica tema claro');
  expect(automatic.storage.get('portfolio-theme') === 'light', 'Escolha persistida');
  automatic.changeSystem(false);
  expect(automatic.dataset.theme === 'light', 'Escolha manual prevalece sobre sistema');
  const nextPage = createPage({ storage: automatic.storage });
  expect(nextPage.initialTheme === 'light', 'Nova página respeita escolha salva');
  nextPage.click();
  expect(nextPage.dataset.theme === 'dark', 'Segundo clique aplica tema escuro');
  expect(nextPage.metaAttributes.content === '#17171c', 'Cor sugerida acompanha alternância manual');
  expect(nextPage.attributes.title === 'Alternar para tema claro', 'Tooltip acompanha destino');
  expect(nextPage.storage.get('portfolio-theme') === 'dark', 'Tema escuro persistido');

  const savedPage = createPage({ storage: new Map([['portfolio-theme', 'dark']]), systemLight: true });
  savedPage.changeSystem(true);
  expect(savedPage.dataset.theme === 'dark', 'Preferência salva ignora alteração do sistema');

  for (const options of [{ readBlocked: true }, { writeBlocked: true }, { readBlocked: true, writeBlocked: true }]) {
    const page = createPage({ ...options, systemLight: true });
    expect(page.initialTheme === 'light', 'Falha do armazenamento preserva preferência do sistema');
    page.click();
    expect(page.dataset.theme === 'dark', 'Alternância funciona com armazenamento bloqueado');
    page.changeSystem(true);
    expect(page.dataset.theme === 'dark', 'Escolha manual permanece ativa mesmo sem persistência');
  }

  for (const options of [{ noMedia: true }, { mediaThrows: true }]) {
    const page = createPage(options);
    expect(page.initialTheme === 'dark', 'Sem matchMedia, fallback escuro');
    page.click();
    expect(page.dataset.theme === 'light', 'Sem matchMedia, alternância manual funciona');
    const saved = createPage({ ...options, storage: new Map([['portfolio-theme', 'light']]) });
    expect(saved.initialTheme === 'light', 'Sem matchMedia, preferência salva funciona');
  }

  const legacy = createPage({ legacyMedia: true });
  legacy.changeSystem(true);
  expect(legacy.dataset.theme === 'light', 'Mudanças de sistema com API legada');
  const missingButton = createPage({ noButton: true });
  expect(missingButton.dataset.theme === 'dark', 'Ausência do botão não quebra inicialização');
  const missingInit = createPage({ skipInit: true });
  missingInit.click();
  expect(missingInit.dataset.theme === 'light', 'Controle funciona mesmo se inicialização não carregar');
  const missingMeta = createPage({ noThemeColor: true });
  missingMeta.click();
  expect(missingMeta.dataset.theme === 'light', 'Alternância independe da meta de cor do navegador');

  return `${checks}/${checks} verificações de tema aprovadas.`;
}

if (typeof module !== 'undefined' && require.main === module) {
  const fs = require('node:fs');
  const path = require('node:path');
  const scripts = path.join(__dirname, '../public/assets/js');
  console.log(runThemeTests(
    fs.readFileSync(path.join(scripts, 'theme-init.js'), 'utf8'),
    fs.readFileSync(path.join(scripts, 'site.js'), 'utf8')
  ));
}
