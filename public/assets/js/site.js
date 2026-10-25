(() => {
  const root = document.documentElement;
  const toggle = document.querySelector('[data-theme-toggle]');
  const themeColor = document.querySelector('meta[name="theme-color"]');

  if (toggle === null) {
    return;
  }

  const updateToggle = () => {
    const target = root.dataset.theme === 'light' ? 'escuro' : 'claro';
    const label = `Alternar para tema ${target}`;
    toggle.setAttribute('aria-label', label);
    toggle.setAttribute('title', label);

    if (themeColor !== null) {
      const color = window.getComputedStyle(root).getPropertyValue('--color-background').trim();
      if (color !== '') {
        themeColor.setAttribute('content', color);
      }
    }
  };

  toggle.addEventListener('click', () => {
    const theme = root.dataset.theme === 'light' ? 'dark' : 'light';
    root.dataset.theme = theme;
    root.dataset.themePreference = theme;
    updateToggle();

    try {
      window.localStorage.setItem('portfolio-theme', theme);
    } catch {
      // A escolha manual permanece ativa nesta página mesmo sem persistência.
    }
  });

  updateToggle();
  toggle.hidden = false;

  try {
    const systemPreference = window.matchMedia('(prefers-color-scheme: light)');
    const updateSystemTheme = (event) => {
      if (!root.dataset.themePreference) {
        root.dataset.theme = event.matches ? 'light' : 'dark';
        updateToggle();
      }
    };

    if (typeof systemPreference.addEventListener === 'function') {
      systemPreference.addEventListener('change', updateSystemTheme);
    } else if (typeof systemPreference.addListener === 'function') {
      systemPreference.addListener(updateSystemTheme);
    }
  } catch {
    // A alternância manual funciona mesmo sem matchMedia.
  }
})();

(() => {
  const header = document.querySelector('.site-header');
  const menuToggle = document.querySelector('[data-menu-toggle]');
  const navigation = document.querySelector('#navegacao-principal');

  if (header === null || menuToggle === null || navigation === null) {
    return;
  }

  let desktop;
  try {
    desktop = window.matchMedia('(min-width: 75rem)');
  } catch {
    return;
  }

  let open = false;
  const updateHeight = () => {
    document.documentElement.style.setProperty('--header-height', `${header.getBoundingClientRect().height}px`);
  };
  const setOpen = (value, restoreFocus = false) => {
    open = value && !desktop.matches;
    navigation.hidden = !open && !desktop.matches;
    menuToggle.setAttribute('aria-expanded', String(open));
    menuToggle.setAttribute('aria-label', open ? 'Fechar menu' : 'Abrir menu');
    if (restoreFocus) {
      menuToggle.focus();
    }
  };
  const updateNavigation = () => {
    const focusInNavigation = navigation.contains(document.activeElement);
    const focusOnToggle = document.activeElement === menuToggle;
    setOpen(false);
    menuToggle.hidden = desktop.matches;
    if (!desktop.matches && focusInNavigation) {
      menuToggle.focus();
    } else if (desktop.matches && focusOnToggle) {
      navigation.querySelector('a').focus();
    }
    updateHeight();
  };
  const updateScroll = () => {
    header.classList.toggle('is-scrolled', window.scrollY > 12);
  };

  header.dataset.navigationReady = '';
  updateNavigation();
  updateScroll();

  menuToggle.addEventListener('click', () => setOpen(!open));
  document.addEventListener('keydown', (event) => {
    if (open && event.key === 'Escape') {
      event.preventDefault();
      setOpen(false, true);
    }
  });
  navigation.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');
    if (!open || link === null) {
      return;
    }
    setOpen(false);
    if (link.origin === window.location.origin && link.pathname === window.location.pathname && link.hash) {
      const target = document.getElementById(link.hash.slice(1));
      if (target !== null) {
        target.focus({ preventScroll: true });
      }
    }
  });
  header.addEventListener('focusout', (event) => {
    if (open && !header.contains(event.relatedTarget)) {
      setOpen(false);
    }
  });
  document.addEventListener('click', (event) => {
    if (open && !header.contains(event.target)) {
      setOpen(false);
    }
  });
  window.addEventListener('scroll', updateScroll, { passive: true });
  window.addEventListener('resize', updateNavigation);
  if (typeof desktop.addEventListener === 'function') {
    desktop.addEventListener('change', updateNavigation);
  } else if (typeof desktop.addListener === 'function') {
    desktop.addListener(updateNavigation);
  }
  if (typeof window.ResizeObserver === 'function') {
    new window.ResizeObserver(updateHeight).observe(header);
  }
})();

(() => {
  const carousel = document.querySelector('[data-skills-carousel]');
  if (carousel === null) {
    return;
  }

  const track = carousel.querySelector('[data-carousel-track]');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let frame = null;
  let previousTime = null;
  let position = track.scrollLeft;
  let interactionTimer = null;
  let interacting = false;
  let dragging = false;
  let visible = typeof window.IntersectionObserver !== 'function';

  const stopPlayback = () => {
    window.cancelAnimationFrame(frame);
    frame = null;
    previousTime = null;
  };

  const advance = (time) => {
    const elapsed = previousTime === null ? 0 : Math.min(time - previousTime, 64);
    previousTime = time;
    position += elapsed * 0.03;
    if (position >= track.scrollWidth - track.clientWidth) {
      position = 0;
    }
    track.scrollLeft = position;
    frame = window.requestAnimationFrame(advance);
  };

  const updatePlayback = () => {
    stopPlayback();
    const scrollable = track.scrollWidth > track.clientWidth + 1;
    track.tabIndex = scrollable ? 0 : -1;
    if (scrollable && visible && !interacting && !document.hidden && !reducedMotion.matches) {
      position = track.scrollLeft;
      frame = window.requestAnimationFrame(advance);
    }
  };

  const pauseForInteraction = () => {
    interacting = true;
    stopPlayback();
    window.clearTimeout(interactionTimer);
    if (!dragging) {
      interactionTimer = window.setTimeout(() => {
        interacting = false;
        updatePlayback();
      }, 1800);
    }
  };

  const move = (direction) => {
    const maximum = track.scrollWidth - track.clientWidth;
    const step = track.firstElementChild.getBoundingClientRect().width
      + parseFloat(window.getComputedStyle(track).columnGap);
    let left = track.scrollLeft + direction * step;
    if (direction > 0 && track.scrollLeft >= maximum - 1) {
      left = 0;
    } else if (direction < 0 && track.scrollLeft <= 1) {
      left = maximum;
    }
    track.scrollTo({
      left: Math.max(0, Math.min(maximum, left)),
      behavior: reducedMotion.matches ? 'auto' : 'smooth',
    });
  };

  track.addEventListener('keydown', (event) => {
    if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) {
      return;
    }
    event.preventDefault();
    pauseForInteraction();
    if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
      move(event.key === 'ArrowLeft' ? -1 : 1);
    } else {
      track.scrollTo({
        left: event.key === 'Home' ? 0 : track.scrollWidth - track.clientWidth,
        behavior: reducedMotion.matches ? 'auto' : 'smooth',
      });
    }
  });
  track.addEventListener('pointerdown', () => {
    dragging = true;
    pauseForInteraction();
  });
  const finishDragging = () => {
    if (dragging) {
      dragging = false;
      pauseForInteraction();
    }
  };
  document.addEventListener('pointerup', finishDragging);
  document.addEventListener('pointercancel', finishDragging);
  track.addEventListener('wheel', pauseForInteraction, { passive: true });
  document.addEventListener('visibilitychange', updatePlayback);
  if (typeof reducedMotion.addEventListener === 'function') {
    reducedMotion.addEventListener('change', updatePlayback);
  } else if (typeof reducedMotion.addListener === 'function') {
    reducedMotion.addListener(updatePlayback);
  }
  if (typeof window.ResizeObserver === 'function') {
    new window.ResizeObserver(updatePlayback).observe(track);
  } else {
    window.addEventListener('resize', updatePlayback);
  }
  if (typeof window.IntersectionObserver === 'function') {
    new window.IntersectionObserver(([entry]) => {
      visible = entry.isIntersecting;
      updatePlayback();
    }).observe(carousel);
  }
  updatePlayback();
})();
