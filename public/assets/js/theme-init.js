(() => {
  const root = document.documentElement;
  let preference = null;
  let systemTheme = 'dark';

  try {
    const saved = window.localStorage.getItem('portfolio-theme');
    if (saved === 'dark' || saved === 'light') {
      preference = saved;
    }
  } catch {
    // O tema continua disponível quando o armazenamento está bloqueado.
  }

  try {
    if (window.matchMedia('(prefers-color-scheme: light)').matches) {
      systemTheme = 'light';
    }
  } catch {
    // Navegadores sem preferência de sistema usam o tema escuro.
  }

  root.dataset.theme = preference || systemTheme;
  if (preference !== null) {
    root.dataset.themePreference = preference;
  }
})();
