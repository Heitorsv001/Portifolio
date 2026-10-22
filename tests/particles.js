function runParticleTests(source) {
  let checks = 0;
  const expect = (condition, message) => {
    checks += 1;
    if (!condition) throw new Error(message);
  };

  const createPage = (options = {}) => {
    const queue = new Map();
    const documentEvents = {};
    const windowEvents = {};
    const heroEvents = {};
    const state = { draws: 0, points: [], lines: 0, color: '#b6b2c1', sizeScale: 1, mediaReads: 0 };
    const bounds = { width: options.width ?? 1440, height: options.height ?? 900, top: options.offscreen ? -1000 : 0, left: 0 };
    const desktop = { matches: options.mobile !== true };
    const reduced = { matches: options.reduced === true };
    let observer;
    let resizeObserver;
    let mutationObserver;
    let serial = 0;
    let seed = 7;
    const math = Object.create(Math);
    math.random = () => {
      seed = (seed * 16807) % 2147483647;
      return (seed - 1) / 2147483646;
    };
    const context = {
      clearRect() { state.draws += 1; state.points = []; state.lines = 0; },
      setTransform(a) { state.ratio = a; },
      beginPath() {},
      arc(x, y, radius) { state.points.push({ x, y, radius }); },
      fill() { state.fillColor = this.fillStyle; },
      moveTo() {}, lineTo() {},
      stroke() { state.lines += 1; },
    };
    const canvas = {
      width: 300, height: 150,
      getContext() {
        if (options.contextThrows) throw new Error('Canvas indisponível');
        return options.noContext ? null : context;
      },
    };
    const hero = {
      querySelector: () => options.noCanvas ? null : canvas,
      getBoundingClientRect: () => ({ ...bounds, bottom: bounds.top + bounds.height }),
      addEventListener: (name, callback) => { heroEvents[name] = callback; },
    };
    const document = {
      hidden: options.hidden === true,
      documentElement: {},
      querySelector: () => options.noHero ? null : hero,
      addEventListener: (name, callback) => { documentEvents[name] = callback; },
    };
    for (const preference of [desktop, reduced]) {
      let matches = preference.matches;
      Object.defineProperty(preference, 'matches', {
        get() { state.mediaReads += 1; return matches; },
        set(value) { matches = value; },
      });
      if (options.legacy) {
        preference.addListener = (callback) => { preference.change = callback; };
      } else {
        preference.addEventListener = (name, callback) => { preference.change = callback; };
      }
    }
    const window = {
      devicePixelRatio: options.dpr ?? 1,
      innerHeight: 900,
      getComputedStyle: () => ({ getPropertyValue: (name) => name === '--particle-size-scale' ? String(state.sizeScale) : state.color }),
      addEventListener: (name, callback) => { windowEvents[name] = callback; },
      matchMedia: (query) => {
        if (options.noMedia) throw new Error('API indisponível');
        return query.includes('reduced-motion') ? reduced : desktop;
      },
      requestAnimationFrame: (callback) => { serial += 1; queue.set(serial, callback); return serial; },
      cancelAnimationFrame: (id) => queue.delete(id),
      IntersectionObserver: class {
        constructor(callback) { observer = callback; }
        observe() {}
      },
      ResizeObserver: class {
        constructor(callback) { resizeObserver = callback; }
        observe() {}
      },
      MutationObserver: class {
        constructor(callback) { mutationObserver = callback; }
        observe() {}
      },
    };
    if (options.noIntersection) delete window.IntersectionObserver;
    if (options.noAnimation) delete window.requestAnimationFrame;
    new Function('window', 'document', 'Math', source)(window, document, math);

    return {
      queue, state, canvas, window,
      step: (timestamp) => {
        const pending = [...queue.entries()];
        queue.clear();
        for (const [, callback] of pending) callback(timestamp);
      },
      visibility: (hidden) => { document.hidden = hidden; documentEvents.visibilitychange(); },
      intersection: (visible) => observer([{ isIntersecting: visible }]),
      resize: (width, height) => { Object.assign(bounds, { width, height }); windowEvents.resize(); },
      observedResize: () => resizeObserver(),
      media: (mobile, reduce) => { desktop.matches = !mobile; reduced.matches = reduce; reduced.change(); },
      theme: (color, sizeScale = 1) => { state.color = color; state.sizeScale = sizeScale; mutationObserver(); },
      pointer: (x, y, type = 'mouse') => heroEvents.pointermove({ clientX: x, clientY: y, pointerType: type }),
      leave: () => heroEvents.pointerleave(),
      scroll: (top) => { bounds.top = top; windowEvents.scroll(); },
    };
  };

  const page = createPage();
  const initialMediaReads = page.state.mediaReads;
  expect(page.queue.size === 1, 'Um único loop inicial');
  page.step(100);
  expect(page.state.draws === 1 && page.state.points.length > 0, 'Desktop visível desenha pontos');
  expect(page.state.points.length <= 64, 'Densidade limitada');
  page.step(116);
  expect(page.state.draws === 1, 'Quadro intermediário não redesenha acima do limite');
  page.step(134);
  expect(page.state.draws === 2, 'Animação retoma após intervalo');
  expect(page.state.mediaReads === initialMediaReads, 'Quadros de animação não consultam preferências de mídia repetidamente');
  expect(page.state.fillColor === '#b6b2c1', 'Cores iniciais vêm dos tokens');
  const originalRadii = page.state.points.map((point) => point.radius);
  page.theme('#5b21b6', 2);
  page.step(168);
  expect(page.state.fillColor === '#5b21b6', 'Troca de tema atualiza o desenho');
  expect(page.state.points.every((point, index) => point.radius === originalRadii[index] * 2), 'Tema claro dobra raio e diâmetro das bolinhas');
  page.theme('#b6b2c1');
  page.step(202);
  expect(page.state.points.every((point, index) => point.radius === originalRadii[index]), 'Tema escuro mantém tamanho original');
  page.visibility(true);
  expect(page.queue.size === 0, 'Aba oculta cancela animação');
  page.visibility(false);
  page.visibility(false);
  expect(page.queue.size === 1, 'Retomar não duplica loops');
  page.intersection(false);
  expect(page.queue.size === 0, 'Hero fora da viewport cancela animação');
  page.intersection(true);
  page.intersection(true);
  expect(page.queue.size === 1, 'Retorno à viewport mantém um loop');
  page.observedResize();
  expect(page.queue.size === 1, 'Mudança de tamanho não duplica loop');
  page.media(false, true);
  expect(page.queue.size === 0 && page.canvas.width === 0 && page.canvas.height === 0, 'Movimento reduzido cancela loop e libera buffer');
  page.media(false, false);
  expect(page.queue.size === 1, 'Mudança da preferência permite retomar');
  page.media(true, false);
  expect(page.queue.size === 0 && page.canvas.width === 0, 'Entrada no mobile desativa efeito');

  for (const options of [{ mobile: true }, { reduced: true }]) {
    const disabled = createPage(options);
    expect(disabled.queue.size === 0 && disabled.canvas.width === 0, 'Efeito inicia desligado conforme preferência/dispositivo');
  }
  const hidden = createPage({ hidden: true });
  expect(hidden.queue.size === 0, 'Aba inicialmente oculta não anima');
  const offscreen = createPage({ offscreen: true });
  expect(offscreen.queue.size === 0, 'Hero inicialmente fora da tela não anima');
  const empty = createPage({ width: 0, height: 0 });
  expect(empty.queue.size === 0, 'Área vazia não anima');
  const retina = createPage({ width: 1000, height: 600, dpr: 4 });
  expect(retina.state.ratio === 2, 'Pixel ratio limitado em tela de alta densidade');
  const large = createPage({ width: 7680, height: 4320, dpr: 2 });
  large.step(100);
  expect(large.canvas.width * large.canvas.height < 6010000, 'Buffer limitado em telas muito grandes');
  expect(large.state.points.length <= 64, 'Tela grande não aumenta pontos sem limite');
  const fallback = createPage({ noIntersection: true, legacy: true });
  fallback.scroll(-1000);
  expect(fallback.queue.size === 0, 'Fallback sem observer pausa fora da tela');
  fallback.scroll(0);
  expect(fallback.queue.size === 1, 'Fallback sem observer retoma');
  fallback.media(false, true);
  expect(fallback.queue.size === 0, 'Preferências com API legada respeitadas');

  const baseline = createPage();
  const interactive = createPage();
  baseline.step(100);
  interactive.step(100);
  const index = interactive.state.points.findIndex((point) => point.x > 140 && point.x < 1300 && point.y > 140 && point.y < 760);
  const point = interactive.state.points[index];
  interactive.pointer(point.x + 20, point.y);
  baseline.step(134);
  interactive.step(134);
  const offset = Math.hypot(interactive.state.points[index].x - baseline.state.points[index].x, interactive.state.points[index].y - baseline.state.points[index].y);
  expect(offset > 0 && offset < 8, 'Mouse produz afastamento suave e limitado');
  interactive.leave();
  baseline.step(168);
  interactive.step(168);
  const afterLeave = Math.hypot(interactive.state.points[index].x - baseline.state.points[index].x, interactive.state.points[index].y - baseline.state.points[index].y);
  expect(afterLeave < offset, 'Saída do mouse desfaz deslocamento gradualmente');
  const touch = createPage();
  const noTouch = createPage();
  touch.step(100);
  noTouch.step(100);
  touch.pointer(touch.state.points[0].x + 20, touch.state.points[0].y, 'touch');
  touch.step(134);
  noTouch.step(134);
  expect(touch.state.points[0].x === noTouch.state.points[0].x, 'Toque não ativa reação ao ponteiro');

  for (const options of [{ noHero: true }, { noCanvas: true }, { noContext: true }, { contextThrows: true }, { noMedia: true }, { noAnimation: true }]) {
    expect(createPage(options).queue.size === 0, 'Falha ou ausência de API deixa a página sem animação');
  }

  return `${checks}/${checks} verificações de partículas aprovadas.`;
}

if (typeof module !== 'undefined' && require.main === module) {
  const fs = require('node:fs');
  const path = require('node:path');
  console.log(runParticleTests(fs.readFileSync(path.join(__dirname, '../public/assets/js/hero-particles.js'), 'utf8')));
}
