(() => {
  const hero = document.querySelector('.home-hero');
  const canvas = hero === null ? null : hero.querySelector('[data-hero-particles]');
  if (canvas === null || typeof window.requestAnimationFrame !== 'function') {
    return;
  }

  let context;
  let desktop;
  let reducedMotion;
  try {
    context = canvas.getContext('2d');
    desktop = window.matchMedia('(min-width: 56.25rem) and (pointer: fine)');
    reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  } catch {
    return;
  }
  if (context === null) {
    return;
  }

  let width = 0;
  let height = 0;
  let particles = [];
  let frame = null;
  let previousFrame = null;
  let pointer = null;
  let color = '';
  let sizeScale = 1;
  let visible = false;
  let motionAllowed = false;

  const enabled = () => motionAllowed;
  const canAnimate = () => enabled() && visible && !document.hidden && width > 0 && height > 0;
  const updateAppearance = () => {
    const styles = window.getComputedStyle(document.documentElement);
    color = styles.getPropertyValue('--color-particle').trim();
    sizeScale = Number.parseFloat(styles.getPropertyValue('--particle-size-scale')) || 1;
  };
  const stop = () => {
    if (frame !== null) {
      window.cancelAnimationFrame(frame);
      frame = null;
    }
    previousFrame = null;
    pointer = null;
  };

  const draw = (seconds) => {
    context.clearRect(0, 0, width, height);
    context.fillStyle = color;
    context.strokeStyle = color;
    context.lineWidth = 0.65;

    for (const particle of particles) {
      particle.x = (particle.x + particle.vx * seconds + width) % width;
      particle.y = (particle.y + particle.vy * seconds + height) % height;
      let offsetX = 0;
      let offsetY = 0;
      if (pointer !== null) {
        const dx = particle.x - pointer.x;
        const dy = particle.y - pointer.y;
        const distance = Math.hypot(dx, dy);
        if (distance > 0 && distance < 140) {
          const strength = (1 - distance / 140) * 8;
          offsetX = dx / distance * strength;
          offsetY = dy / distance * strength;
        }
      }
      const smoothing = Math.min(seconds * 4, 1);
      particle.offsetX += (offsetX - particle.offsetX) * smoothing;
      particle.offsetY += (offsetY - particle.offsetY) * smoothing;
      particle.drawX = particle.x + particle.offsetX;
      particle.drawY = particle.y + particle.offsetY;
      context.globalAlpha = 0.45;
      context.beginPath();
      context.arc(particle.drawX, particle.drawY, particle.radius * sizeScale, 0, Math.PI * 2);
      context.fill();
    }

    for (let i = 0; i < particles.length; i += 1) {
      for (let j = i + 1; j < particles.length; j += 1) {
        const first = particles[i];
        const second = particles[j];
        const distance = Math.hypot(first.drawX - second.drawX, first.drawY - second.drawY);
        if (distance < 115) {
          context.globalAlpha = (1 - distance / 115) * 0.12;
          context.beginPath();
          context.moveTo(first.drawX, first.drawY);
          context.lineTo(second.drawX, second.drawY);
          context.stroke();
        }
      }
    }
    context.globalAlpha = 1;
  };

  const animate = (timestamp) => {
    frame = null;
    if (!canAnimate()) {
      return;
    }
    const elapsed = previousFrame === null ? 1000 / 30 : timestamp - previousFrame;
    if (elapsed >= 1000 / 30) {
      draw(Math.min(elapsed / 1000, 0.05));
      previousFrame = timestamp;
    }
    frame = window.requestAnimationFrame(animate);
  };
  const synchronize = () => {
    if (!canAnimate()) {
      stop();
    } else if (frame === null) {
      frame = window.requestAnimationFrame(animate);
    }
  };

  const resize = () => {
    motionAllowed = desktop.matches && !reducedMotion.matches;
    if (!enabled()) {
      stop();
      canvas.width = 0;
      canvas.height = 0;
      width = 0;
      height = 0;
      particles = [];
      return;
    }
    const bounds = hero.getBoundingClientRect();
    const nextWidth = Math.max(0, bounds.width);
    const nextHeight = Math.max(0, bounds.height);
    const area = nextWidth * nextHeight;
    const ratio = area > 0 ? Math.min(window.devicePixelRatio || 1, 2, Math.sqrt(6000000 / area)) : 1;
    if (width !== nextWidth || height !== nextHeight || canvas.width !== Math.round(nextWidth * ratio)) {
      width = nextWidth;
      height = nextHeight;
      canvas.width = Math.round(width * ratio);
      canvas.height = Math.round(height * ratio);
      context.setTransform(ratio, 0, 0, ratio, 0, 0);
      const count = area > 0 ? Math.min(64, Math.max(16, Math.round(area / 24000))) : 0;
      particles = Array.from({ length: count }, () => ({
        x: Math.random() * width,
        y: Math.random() * height,
        vx: (Math.random() - 0.5) * 6,
        vy: (Math.random() - 0.5) * 6,
        radius: 0.8 + Math.random() * 0.8,
        offsetX: 0,
        offsetY: 0,
      }));
    }
    visible = bounds.bottom > 0 && bounds.top < window.innerHeight;
    updateAppearance();
    synchronize();
  };

  hero.addEventListener('pointermove', (event) => {
    if (!canAnimate() || event.pointerType !== 'mouse') {
      return;
    }
    const bounds = hero.getBoundingClientRect();
    pointer = { x: event.clientX - bounds.left, y: event.clientY - bounds.top };
  }, { passive: true });
  hero.addEventListener('pointerleave', () => { pointer = null; });
  document.addEventListener('visibilitychange', synchronize);
  window.addEventListener('resize', resize);

  for (const preference of [desktop, reducedMotion]) {
    if (typeof preference.addEventListener === 'function') {
      preference.addEventListener('change', resize);
    } else if (typeof preference.addListener === 'function') {
      preference.addListener(resize);
    }
  }
  if (typeof window.IntersectionObserver === 'function') {
    new window.IntersectionObserver((entries) => {
      visible = entries[0].isIntersecting;
      synchronize();
    }).observe(hero);
  } else {
    window.addEventListener('scroll', () => {
      const bounds = hero.getBoundingClientRect();
      visible = bounds.bottom > 0 && bounds.top < window.innerHeight;
      synchronize();
    }, { passive: true });
  }
  if (typeof window.ResizeObserver === 'function') {
    new window.ResizeObserver(resize).observe(hero);
  }
  if (typeof window.MutationObserver === 'function') {
    new window.MutationObserver(updateAppearance).observe(document.documentElement, {
      attributes: true,
      attributeFilter: ['data-theme'],
    });
  }
  resize();
})();
