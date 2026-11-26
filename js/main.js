/* Wildlife Interactions — shared behaviour */
(() => {
  // 1. Mark the current page in the main nav (item.menu State=active)
  const current = location.pathname.split('/').pop() || 'home.html';
  document.querySelectorAll('.site-nav .menu-item').forEach((link) => {
    if (link.getAttribute('href') === current) link.setAttribute('aria-current', 'page');
  });

  // 2. Logo → first section of the Home (intro).
  //    On the Home itself: close any open detail and scroll back to the top instead of reloading.
  //    From other pages the link loads the Home, which always opens on the intro (js/activities.js).
  document.addEventListener('click', (e) => {
    const logo = e.target.closest('a.logo');
    // only the Home has the intro (Missions / Éducation also have a pinned stage)
    if (!logo || !document.querySelector('[data-hero-layer="intro"]')) return;
    e.preventDefault();
    document.querySelector('.detail:not([hidden]) [data-detail-close]')?.click();
    document.querySelector('[data-site-header].is-open [data-nav-toggle]')?.click();
    if (location.hash) history.replaceState(null, '', location.pathname + location.search);
    const smooth = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (window.WI_LENIS) window.WI_LENIS.scrollTo(0, { duration: 1.4, force: true });
    else window.scrollTo({ top: 0, behavior: smooth ? 'smooth' : 'auto' });
  });

  // 3. Mobile navigation toggle
  const header = document.querySelector('[data-site-header]');
  const toggle = header?.querySelector('[data-nav-toggle]');
  if (!header || !toggle) return;

  const setOpen = (open) => {
    header.classList.toggle('is-open', open);
    toggle.setAttribute('aria-expanded', String(open));
    document.documentElement.style.overflow = open ? 'hidden' : '';
  };

  toggle.addEventListener('click', () => setOpen(!header.classList.contains('is-open')));
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setOpen(false); });
  header.querySelectorAll('.site-nav a').forEach((a) => a.addEventListener('click', () => setOpen(false)));
  window.matchMedia('(min-width: 1101px)').addEventListener('change', (e) => { if (e.matches) setOpen(false); });

  // 4. Home: the menu slides away while scrolling down and comes back on the way up
  if (document.querySelector('[data-hero-stage]')) {
    let lastY = window.scrollY;
    let ticking = false;
    const onScroll = () => {
      ticking = false;
      const y = window.scrollY;
      if (header.classList.contains('is-open')) { lastY = y; return; }
      if (y < 40) header.classList.remove('is-away');
      else if (y > lastY + 4) header.classList.add('is-away');
      else if (y < lastY - 4) header.classList.remove('is-away');
      lastY = y;
    };
    window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(onScroll); } }, { passive: true });
    header.addEventListener('focusin', () => header.classList.remove('is-away')); // keyboard users always reach it
  }
})();
