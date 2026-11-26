/* Wildlife Interactions — micro-animations (light, Spyker-like).
   1. Lenis smooth scroll        3. letter roll on buttons / menu items
   2. reveals on scroll          4. parallax on the Exposition hero photo
   Content added later (detail pages, talent cards) is picked up automatically.
   Nothing runs with prefers-reduced-motion. */
(() => {
  const root = document.documentElement;
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduce) return;
  root.classList.add('motion-ready');

  /* ------------------------------------------------------------------ 1 */
  let lenis = null;
  if (window.Lenis) {
    lenis = new window.Lenis({ lerp: 0.09, wheelMultiplier: 0.9, smoothWheel: true });
    window.WI_LENIS = lenis;
    const raf = (t) => { lenis.raf(t); requestAnimationFrame(raf); };
    requestAnimationFrame(raf);
    // pause while the page itself must not scroll (detail overlay, mobile menu)
    const sync = () => {
      const locked = root.classList.contains('has-detail-open') || root.style.overflow === 'hidden';
      if (locked) lenis.stop(); else lenis.start();
    };
    new MutationObserver(sync).observe(root, { attributes: true, attributeFilter: ['class', 'style'] });
    sync();
  }

  /* ------------------------------------------------------------------ 2 */
  const WORDS = [
    '.hero__title', '.page-hero__title', '.contact__title', '.expo__title', '.partners-band__title',
    '.detail__title', '.detail__h4', '.detail__h5', '.site-footer__claim',
  ].join(',');
  const UP = [
    '.hero__subtitle', '.page-hero__subtitle', '.hero__partners > li', '.hero__data .data',
    '.activities > li', '.profiles .profile',
    '.tag', '.expo__intro-text', '.expo__head .cta', '.media__caption', '.magazine__cover figcaption', '.carousel__nav', '.partners-band__logos img',
    '.detail__lead', '.enjeu', '.stat', '.detail__section-lead', '.talent-card', '.detail__ctas .cta',
    '.contact__chips .cta', '.contact__intro', '.contact-form .field', '.contact-form > .cta',
    '.site-footer__tagline', '.site-footer__ctas .cta', '.site-footer__bottom > *',
  ].join(',');
  const DOWN = '.site-header > .logo, .site-header__toggle, .site-nav__list > li, .site-nav > .cta';
  const IMG = '.media__img, .magazine__cover img, .detail__image';
  const ROLL = '.cta, .menu-item';

  const splitWords = (el) => {
    let i = 0;
    const walk = (node) => {
      [...node.childNodes].forEach((n) => {
        if (n.nodeType === 1) { walk(n); return; }
        if (n.nodeType !== 3 || !n.textContent.trim()) return;
        const frag = document.createDocumentFragment();
        n.textContent.split(/(\s+)/).forEach((part) => {
          if (!part) return;
          if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(part)); return; }
          const w = document.createElement('span'); w.className = 'w';
          const inner = document.createElement('span'); inner.className = 'w__i'; inner.textContent = part;
          inner.style.setProperty('--wi', i++);
          w.appendChild(inner); frag.appendChild(w);
        });
        n.replaceWith(frag);
      });
    };
    walk(el);
  };

  const roll = (el) => {
    [...el.childNodes].forEach((n) => {
      if (n.nodeType !== 3 || !n.textContent.trim()) return;
      const text = n.textContent.trim();
      const wrap = document.createElement('span');
      wrap.className = 'roll';
      [...text].forEach((ch, i) => {
        const c = document.createElement('span');
        c.className = 'roll__c'; c.textContent = ch; c.style.setProperty('--ci', i);
        wrap.appendChild(c);
      });
      n.replaceWith(wrap);
    });
  };

  // reveal when entering the viewport; items entering together are staggered
  const io = new IntersectionObserver((entries) => {
    let k = 0;
    entries.filter((e) => e.isIntersecting).forEach((e) => {
      const el = e.target;
      io.unobserve(el);
      if (!el.style.getPropertyValue('--d')) el.style.setProperty('--d', `${Math.min(k++, 8) * 70}ms`);
      el.classList.add('is-in');
      // hand the element back to its own transitions (hover etc.) once revealed
      const delay = parseFloat(el.style.getPropertyValue('--d')) || 0;
      if (el.dataset.reveal !== 'words') {
        setTimeout(() => { el.removeAttribute('data-reveal'); el.classList.remove('is-in'); el.style.removeProperty('--d'); }, delay + 1800);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });

  const mark = (el, kind) => {
    if (el.dataset.motion) return;
    el.dataset.motion = '1';
    if (kind === 'words') splitWords(el);
    el.dataset.reveal = kind;
    io.observe(el);
  };

  const scan = (scope) => {
    if (!scope || scope.nodeType !== 1) return;
    const all = (sel) => [...(scope.matches(sel) ? [scope] : []), ...scope.querySelectorAll(sel)];
    all(ROLL).forEach((el) => { if (!el.dataset.roll) { el.dataset.roll = '1'; roll(el); } });
    all(WORDS).forEach((el) => mark(el, 'words'));
    all(IMG).forEach((el) => mark(el, 'img'));
    all(UP).forEach((el) => mark(el, 'up'));
    all(DOWN).forEach((el, i) => { el.style.setProperty('--d', `${120 + i * 60}ms`); mark(el, 'down'); });
  };

  scan(document.body);
  // content rendered later (detail page, talent cards…)
  let pending = new Set();
  const mo = new MutationObserver((records) => {
    records.forEach((r) => r.addedNodes.forEach((n) => {
      if (n.nodeType === 1 && !n.classList.contains('w') && !n.classList.contains('roll')) pending.add(n);
    }));
    if (pending.size) requestAnimationFrame(() => {
      const nodes = pending; pending = new Set();
      nodes.forEach((n) => { if (n.isConnected) scan(n); });
      mo.takeRecords();
    });
  });
  mo.observe(document.body, { childList: true, subtree: true });

  /* ------------------------------------------------------------------ 5
     Hero shrink (like spykercars.com): while the hero leaves the screen it scales
     down a little around its centre, opening white margins on the sides. */
  const heroes = [...document.querySelectorAll('[data-hero], .expo-hero')];
  if (heroes.length) {
    const small = window.matchMedia('(max-width: 760px)');
    let ticking = false;
    const shrink = () => {
      ticking = false;
      const amount = small.matches ? 0.06 : 0.13;            // 1 → 0.87 on desktop
      heroes.forEach((el) => {
        const r = el.getBoundingClientRect();
        const p = Math.min(Math.max(-r.top / r.height, 0), 1);
        if (p <= 0) { el.style.removeProperty('scale'); return; }  // no transform at rest (fixed mobile menu inside)
        el.style.scale = (1 - amount * p).toFixed(4);
      });
    };
    window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(shrink); } }, { passive: true });
    window.addEventListener('resize', shrink);
    shrink();
  }

  /* ------------------------------------------------------------------ 4 */
  const expoBg = document.querySelector('.expo-hero .page-hero__bg');
  if (expoBg) {
    let ticking = false;
    const move = () => {
      ticking = false;
      const y = Math.min(window.scrollY, window.innerHeight * 1.2);
      expoBg.style.setProperty('--parallax', `${(y * 0.22).toFixed(1)}px`);
    };
    window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(move); } }, { passive: true });
    move();
  }
})();
