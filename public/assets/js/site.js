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
