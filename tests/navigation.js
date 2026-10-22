function runNavigationTests(siteSource) {
  let checks = 0;
  const expect = (condition, message) => {
    checks += 1;
    if (!condition) throw new Error(message);
  };

  const createPage = (options = {}) => {
    const documentEvents = {};
    const windowEvents = {};
    const properties = {};
    const classes = new Set();
    const document = {
      activeElement: null,
      documentElement: { style: { setProperty: (name, value) => { properties[name] = value; } } },
      addEventListener: (name, callback) => { documentEvents[name] = callback; },
    };
    const element = () => ({
      events: {}, attributes: {}, dataset: {}, hidden: false,
      addEventListener(name, callback) { this.events[name] = callback; },
      setAttribute(name, value) { this.attributes[name] = value; },
      focus() { document.activeElement = this; },
    });
    const header = element();
    const toggle = element();
    toggle.hidden = true;
    const navigation = element();
    const link = element();
    Object.assign(link, {
      origin: 'http://localhost', pathname: '/portifolio/', hash: '#sobre',
      closest: () => link,
    });
    const section = element();
    header.contains = (target) => [header, toggle, navigation, link].includes(target);
    navigation.contains = (target) => [navigation, link].includes(target);
    navigation.querySelector = () => link;
    header.getBoundingClientRect = () => ({ height: 80 });
    header.classList = { toggle: (name, active) => { active ? classes.add(name) : classes.delete(name); } };
    document.getElementById = (id) => id === 'sobre' ? section : null;
    document.querySelector = (selector) => {
      if (selector === '.site-header') return options.noHeader ? null : header;
      if (selector === '[data-menu-toggle]') return toggle;
      if (selector === '#navegacao-principal') return navigation;
      return null;
    };
    let mediaListener;
    const media = { matches: options.desktop === true };
    if (options.legacy) {
      media.addListener = (callback) => { mediaListener = callback; };
    } else {
      media.addEventListener = (name, callback) => { mediaListener = callback; };
    }
    const window = {
      scrollY: options.scrollY || 0,
      location: { origin: 'http://localhost', pathname: '/portifolio/' },
      addEventListener: (name, callback) => { windowEvents[name] = callback; },
      matchMedia: () => {
        if (options.mediaBlocked) throw new Error('API indisponível');
        return media;
      },
    };
    new Function('window', 'document', siteSource)(window, document);

    return {
      header, toggle, navigation, link, section, document, properties, classes,
      clickToggle: () => toggle.events.click(),
      escape: () => {
        const event = { key: 'Escape', prevented: false, preventDefault() { this.prevented = true; } };
        documentEvents.keydown(event);
        return event.prevented;
      },
      clickLink: () => navigation.events.click({ target: link }),
      outsideClick: () => documentEvents.click({ target: {} }),
      leaveFocus: (target) => header.events.focusout({ relatedTarget: target }),
      resize: (desktop) => { media.matches = desktop; mediaListener(); },
      scroll: (value) => { window.scrollY = value; windowEvents.scroll(); },
    };
  };

  const mobile = createPage();
  expect(mobile.navigation.hidden, 'Menu mobile recolhido inicialmente');
  expect(!mobile.toggle.hidden, 'Acionador mobile habilitado');
  expect(mobile.toggle.attributes['aria-expanded'] === 'false', 'Estado recolhido anunciado');
  expect(mobile.properties['--header-height'] === '80px', 'Altura do header disponível para âncoras');
  mobile.clickToggle();
  expect(!mobile.navigation.hidden, 'Acionamento abre menu');
  expect(mobile.toggle.attributes['aria-expanded'] === 'true', 'Estado aberto anunciado');
  expect(mobile.toggle.attributes['aria-label'] === 'Fechar menu', 'Rótulo de fechamento');
  mobile.clickToggle();
  expect(mobile.navigation.hidden, 'Segundo acionamento fecha menu');
  mobile.clickToggle();
  expect(mobile.escape(), 'Escape tratado quando menu está aberto');
  expect(mobile.navigation.hidden, 'Escape fecha menu');
  expect(mobile.document.activeElement === mobile.toggle, 'Escape devolve foco ao acionador');
  expect(!mobile.escape(), 'Escape não é interceptado com menu fechado');
  mobile.clickToggle();
  mobile.document.activeElement = mobile.link;
  mobile.clickLink();
  expect(mobile.navigation.hidden, 'Seleção de destino fecha menu');
  expect(mobile.document.activeElement === mobile.section, 'Âncora local recebe foco');
  mobile.clickToggle();
  mobile.leaveFocus(mobile.link);
  expect(!mobile.navigation.hidden, 'Tabulação dentro do header mantém menu aberto');
  mobile.leaveFocus({});
  expect(mobile.navigation.hidden, 'Saída do foco fecha menu sem aprisioná-lo');
  mobile.clickToggle();
  mobile.outsideClick();
  expect(mobile.navigation.hidden, 'Clique externo fecha menu');

  mobile.clickToggle();
  mobile.toggle.focus();
  mobile.resize(true);
  expect(!mobile.navigation.hidden && mobile.toggle.hidden, 'Desktop mostra links e oculta hambúrguer');
  expect(mobile.toggle.attributes['aria-expanded'] === 'false', 'Troca de breakpoint elimina estado aberto');
  expect(mobile.document.activeElement === mobile.link, 'Foco não fica no botão oculto ao entrar no desktop');
  mobile.resize(false);
  expect(mobile.navigation.hidden && !mobile.toggle.hidden, 'Retorno ao mobile recolhe links');
  expect(mobile.document.activeElement === mobile.toggle, 'Foco sai dos links recolhidos');

  const desktop = createPage({ desktop: true });
  expect(!desktop.navigation.hidden && desktop.toggle.hidden, 'Desktop inicial sem menu compacto');
  expect(!desktop.classes.has('is-scrolled'), 'Topo sem superfície adicional');
  desktop.scroll(100);
  expect(desktop.classes.has('is-scrolled'), 'Rolagem ativa superfície do header');
  desktop.scroll(0);
  expect(!desktop.classes.has('is-scrolled'), 'Retorno ao topo restaura transparência');
  const legacy = createPage({ legacy: true });
  legacy.resize(true);
  expect(!legacy.navigation.hidden, 'API legada acompanha breakpoint');
  const unsupported = createPage({ mediaBlocked: true });
  expect(!unsupported.navigation.hidden && unsupported.toggle.hidden, 'Falha de API mantém links disponíveis');
  const missing = createPage({ noHeader: true });
  expect(!missing.navigation.hidden, 'Página sem header não quebra script');

  return `${checks}/${checks} verificações de navegação aprovadas.`;
}

if (typeof module !== 'undefined' && require.main === module) {
  const fs = require('node:fs');
  const path = require('node:path');
  console.log(runNavigationTests(fs.readFileSync(path.join(__dirname, '../public/assets/js/site.js'), 'utf8')));
}
