/* Wildlife Interactions — hero list, Q&A, detail page
   Figma: Content page (6251:3977)
   - list (item.activities): hover/focus → background of that variant, item active, others 20 %
   - Home: scrolling switches the hero from the list to the Q&A (Main / q&a)
   - click an item → Detail (nav 330 px + panel 1396 px)
*/
(() => {
  const ALL = window.WI_ZONES || [];
  const CATS = window.WI_CATEGORIES || {};
  const SHARED = window.WI_DETAIL_SHARED || {};
  const FAQ = window.WI_FAQ;
  const hero = document.querySelector('[data-hero]');
  if (!hero || !ALL.length) return;

  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  // WordPress theme hooks (all optional — the static site works without them):
  // WI_I18N = translated UI labels, WI_THEME_URL = theme folder for bundled assets.
  const T = (key, fallback) => (window.WI_I18N && window.WI_I18N[key]) || fallback;
  const ASSET = (path) => (window.WI_THEME_URL || '') + path;
  const talentPhoto = (p) => (p && p.includes('/') ? p : `assets/img/talents/${p}.jpg`);
  const bySlug = (slug) => ALL.find((z) => z.slug === slug);
  const reduceMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const category = hero.dataset.category || 'all';
  const ZONES = category === 'all' ? ALL : ALL.filter((z) => z.category === category);

  /* ---------------------------------------------------------------------
     1. Background layers — one <img> per image, cross-faded
     --------------------------------------------------------------------- */
  const bgWrap = hero.querySelector('[data-hero-bgs]');
  const layers = new Map();
  // Mission / programme photos are shown at their original brightness; only the Home
  // intro photo and the Q&A background stay dimmed (their own opacity), as before.
  const showBackground = (src, opacity = 1) => {
    if (!layers.has(src)) {
      const img = document.createElement('img');
      img.className = 'hero__bg';
      img.src = src;
      img.alt = '';
      img.decoding = 'async';
      img.setAttribute('aria-hidden', 'true');
      bgWrap.appendChild(img);
      layers.set(src, img);
    }
    layers.forEach((img, key) => { img.style.opacity = key === src ? String(opacity) : '0'; });
  };

  /* ---------------------------------------------------------------------
     2. List (Figma: item.activities)
     --------------------------------------------------------------------- */
  // Missions / Éducation pages: vertical list (hover = active).
  // Home: a row of thumbnails at the top, driven by the scroll (section 4).
  const list = hero.querySelector('[data-activities]');
  const rail = hero.querySelector('[data-rail]');
  if (list) {
    list.innerHTML = ZONES.map((z) => `
    <li>
      <button class="activity" type="button" data-zone="${z.slug}" aria-haspopup="dialog">
        <span class="activity__label">${esc(CATS[z.category]?.label || '')}</span>
        <span class="activity__title">${esc(z.name)}</span>
        <span class="activity__summary">${esc(z.summary)}</span>
        <span class="icon icon--redirect activity__arrow" aria-hidden="true"></span>
      </button>
    </li>`).join('');
  }
  if (rail) {
    rail.innerHTML = ZONES.map((z, i) => `
    <li class="rail__item${i % 2 ? ' rail__item--short' : ''}">
      <button class="rail__card" type="button" data-zone="${z.slug}" aria-label="${esc(z.name)}">
        <img class="rail__img" src="${esc(z.image)}" alt="" loading="lazy" decoding="async">
      </button>
    </li>`).join('');
  }
  const items = [...hero.querySelectorAll('.activity, .rail__card')];

  // Card row: the card under the mouse drifts a little after the pointer (with a
  // slight lag) and eases back when the pointer leaves it. Mouse / trackpad only.
  if (rail && window.matchMedia('(hover: hover) and (pointer: fine)').matches && !reduceMotion()) {
    const MAX = 22; const FOLLOW = 0.22; const EASE = 0.12;
    const state = new Map(); // card → { x, y, tx, ty }
    let raf = 0;
    const tick = () => {
      raf = 0;
      let moving = false;
      state.forEach((f, card) => {
        f.x += (f.tx - f.x) * EASE;
        f.y += (f.ty - f.y) * EASE;
        card.style.translate = `${f.x.toFixed(1)}px ${f.y.toFixed(1)}px`;
        if (Math.abs(f.tx - f.x) > 0.2 || Math.abs(f.ty - f.y) > 0.2) moving = true;
        else if (!f.tx && !f.ty) { card.style.translate = ''; state.delete(card); }
      });
      if (moving) raf = requestAnimationFrame(tick);
    };
    const kick = () => { if (!raf) raf = requestAnimationFrame(tick); };
    rail.addEventListener('pointermove', (e) => {
      const card = e.target.closest('.rail__card');
      if (!card) return;
      const r = card.getBoundingClientRect();
      const f = state.get(card) || { x: 0, y: 0, tx: 0, ty: 0 };
      const cx = r.left - f.x + r.width / 2; const cy = r.top - f.y + r.height / 2;
      f.tx = Math.max(-MAX, Math.min(MAX, (e.clientX - cx) * FOLLOW));
      f.ty = Math.max(-MAX, Math.min(MAX, (e.clientY - cy) * FOLLOW));
      state.set(card, f);
      kick();
    });
    rail.addEventListener('pointerout', (e) => {
      const card = e.target.closest('.rail__card');
      if (!card || card.contains(e.relatedTarget)) return;
      const f = state.get(card);
      if (f) { f.tx = 0; f.ty = 0; kick(); }
    });
  }
  let activeSlug = ZONES[0].slug;

  // Home: category, name and « En savoir plus » of the active item, under the row
  const info = hero.querySelector('[data-rail-info]');
  const infoTag = info?.querySelector('[data-rail-tag]');
  const infoTitle = info?.querySelector('[data-rail-title]');
  const infoMore = info?.querySelector('[data-rail-more]');
  const paintInfo = () => {
    if (!info) return;
    const z = bySlug(activeSlug);
    if (info.dataset.slug === z.slug) return;
    info.dataset.slug = z.slug;
    info.classList.remove('is-changing');
    void info.offsetWidth; // restart the little fade
    info.classList.add('is-changing');
    if (infoTag) infoTag.textContent = CATS[z.category]?.label || '';
    infoTitle.textContent = z.name;
  };
  infoMore?.addEventListener('click', () => openDetail(activeSlug, infoMore));

  const setActiveZone = (slug) => {
    const zone = bySlug(slug);
    if (!zone) return;
    if (ZONES.includes(zone)) activeSlug = slug;
    items.forEach((b) => {
      const on = b.dataset.zone === activeSlug;
      b.classList.toggle('is-active', on);
      if (b.classList.contains('rail__card')) b.setAttribute('aria-current', on ? 'true' : 'false');
    });
    paintInfo();
    if (hero.dataset.state === 'list') {
      const z = bySlug(activeSlug);
      showBackground(z.bg);
    }
  };
  // Missions / Éducation pages (data-mode="hover"): hovering a card shows its photo and
  // name, a click opens its detail page. On the Home the row follows the scroll (section 4).
  const hoverMode = rail?.dataset.mode === 'hover';
  items.forEach((btn) => {
    const isCard = btn.classList.contains('rail__card');
    if (isCard && !hoverMode) return;
    // cards: switched by the mouse itself moving (below), not by mouseenter — when the
    // column slides under a still pointer while scrolling, nothing should change
    if (!isCard) btn.addEventListener('mouseenter', () => setActiveZone(btn.dataset.zone));
    btn.addEventListener('focus', () => setActiveZone(btn.dataset.zone));
    btn.addEventListener('click', () => openDetail(btn.dataset.zone, btn));
  });
  if (rail && hoverMode) {
    // pointer position tracked on the whole page, so a card that slides under a still
    // pointer (scroll) never counts as a move
    let lastX = null; let lastY = null;
    document.addEventListener('pointermove', (e) => {
      const moved = lastX === null || Math.abs(e.clientX - lastX) > 1 || Math.abs(e.clientY - lastY) > 1;
      lastX = e.clientX; lastY = e.clientY;
      if (!moved) return;
      const card = e.target.closest && e.target.closest('.rail__card');
      if (card && rail.contains(card) && card.dataset.zone !== activeSlug) setActiveZone(card.dataset.zone);
    }, { passive: true });
  }

  /* ---------------------------------------------------------------------
     3. Q&A (Figma: detail + q&a icon, less = open / more = closed)
     --------------------------------------------------------------------- */
  const faqRoot = hero.querySelector('[data-faq]');
  if (faqRoot && FAQ) {
    // Only the first answer starts open; the others open on click.
    faqRoot.innerHTML = FAQ.items.map((it, i) => {
      const open = i === 0;
      return `
      <div class="faq__item${open ? ' is-open' : ''}">
        <h3 class="faq__heading">
          <button class="faq__toggle" type="button" aria-expanded="${open}" aria-controls="faq-a-${i}" id="faq-q-${i}">
            <span class="faq__question">${esc(it.q)}</span>
            <span class="icon ${open ? 'icon--less' : 'icon--more'} faq__icon" aria-hidden="true"></span>
          </button>
        </h3>
        <div class="faq__answer" id="faq-a-${i}" role="region" aria-labelledby="faq-q-${i}">
          <div><p>${esc(it.a)}</p></div>
        </div>
      </div>`;
    }).join('');
    faqRoot.addEventListener('click', (e) => {
      const btn = e.target.closest('.faq__toggle');
      if (!btn) return;
      const item = btn.closest('.faq__item');
      const open = !item.classList.contains('is-open');
      item.classList.toggle('is-open', open);
      btn.setAttribute('aria-expanded', String(open));
      const icon = btn.querySelector('.faq__icon');
      icon.classList.toggle('icon--less', open);
      icon.classList.toggle('icon--more', !open);
    });
  }

  /* ---------------------------------------------------------------------
     4. Hero state — Home: intro (Default) → list → faq (q&a); other pages: list
     --------------------------------------------------------------------- */
  const setHeroState = (state) => {
    if (hero.dataset.state === state) return;
    hero.dataset.state = state;
    hero.querySelectorAll('[data-hero-layer]').forEach((layer) => {
      const on = layer.dataset.heroLayer === state;
      layer.classList.toggle('is-current', on);
      layer.toggleAttribute('inert', !on);
      layer.setAttribute('aria-hidden', String(!on));
    });
    if (state === 'faq') showBackground(FAQ.bg, FAQ.bgOpacity);
    else if (state === 'intro') showBackground(hero.dataset.introBg || ALL[0].bg, Number(hero.dataset.introOpacity || 0.35));
    else setActiveZone(activeSlug);
    document.dispatchEvent(new CustomEvent('wi:hero-state', { detail: state }));
  };

  /* Home intro figures count up from 0 each time the intro is shown (on load, and when
     scrolling / clicking the logo back to it). Screen readers get the final value. */
  const counters = [...hero.querySelectorAll('.hero__data .data__value')].map((el) => {
    const m = el.textContent.trim().match(/^(\D*)([\d\s\u00a0\u202f]+)(.*)$/);
    if (!m) return null;
    const final = el.textContent.trim();
    el.setAttribute('aria-label', final);
    el.style.minWidth = `${Math.ceil(el.getBoundingClientRect().width)}px`; // no layout jump while counting
    return { el, final, prefix: m[1], target: parseInt(m[2].replace(/\D/g, ''), 10), suffix: m[3] };
  }).filter(Boolean);
  let countRaf = 0;
  const countUp = () => {
    cancelAnimationFrame(countRaf);
    if (reduceMotion()) return;
    const DURATION = 1600;
    const t0 = performance.now();
    const fmt = (n) => String(n).replace(/\B(?=(\d{3})+(?!\d))/g, '\u00a0'); // 3 000
    const tick = (now) => {
      const p = Math.min(1, (now - t0) / DURATION);
      const eased = 1 - (1 - p) ** 3; // ease-out
      counters.forEach((c) => {
        c.el.textContent = p < 1 ? `${c.prefix}${fmt(Math.round(c.target * eased))}${c.suffix}` : c.final;
      });
      if (p < 1) countRaf = requestAnimationFrame(tick);
    };
    countRaf = requestAnimationFrame(tick);
  };
  if (counters.length) document.addEventListener('wi:hero-state', (e) => { if (e.detail === 'intro') countUp(); });

  const stage = document.querySelector('[data-hero-stage]');
  hero.dataset.state = '';
  if (stage && rail) {
    /* Home with the thumbnail row. The pinned scroll is split into phases
       (in window heights):
         intro  — the intro text fades away
         reveal — the thumbnails appear one after the other at the top
         items  — the row slides left one card per step; the card at the left
                  is active (100 %), its photo is the background, its name below
         faq    — Q&A */
    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
    if (!location.hash) window.scrollTo(0, 0);
    // Same on every page: when the list shows, all the cards come in together (one
    // after the other, quickly), then the scroll only slides the column. The Home
    // has an intro before it (its text fades away) and a Q&A after it.
    const hasIntro = !!hero.querySelector('[data-hero-layer="intro"]');
    const PH = { intro: hasIntro ? 0.7 : 0, reveal: 0, item: 0.5, faq: 1.1 };
    const N = ZONES.length;
    const cards = [...rail.children];
    const hasFaq = !!hero.querySelector('[data-hero-layer="faq"]');
    cards.forEach((li, i) => li.style.setProperty('--i', i));
    let ticking = false;
    let vh = 0; let pos = []; let vertical = false;
    const measure = () => {
      vh = hero.offsetHeight;
      const total = PH.intro + PH.reveal + (N - 1) * PH.item + (hasFaq ? PH.faq : 0.6);
      stage.style.height = `${Math.round(vh * (1 + total))}px`;
      // column (tablet / desktop) or row (phones), and where each card sits in it
      // (cards alternate tall / short, so the distance between them varies)
      vertical = cards.length > 1 && cards[1].offsetTop > cards[0].offsetTop;
      pos = cards.map((li) => (vertical ? li.offsetTop : li.offsetLeft) - (vertical ? cards[0].offsetTop : cards[0].offsetLeft));
    };
    const bounds = () => {
      const a = PH.intro * vh;
      const b = a + PH.reveal * vh;
      const c = b + N * PH.item * vh;
      return { a, b, c };
    };
    const update = () => {
      ticking = false;
      const y = Math.max(-stage.getBoundingClientRect().top, 0);
      const { a, b, c } = bounds();
      hero.style.setProperty('--intro-p', a ? Math.min(y / a, 1).toFixed(3) : '1');
      const showCards = (on) => cards.forEach((li) => li.classList.toggle('is-shown', on));
      if (y < a) { setHeroState('intro'); showCards(false); return; }
      if (y >= c && hasFaq) { setHeroState('faq'); return; }
      setHeroState('list');
      showCards(true);
      // items: the column slides up (the row left, on phones); the nearest card is active
      const t = Math.min(Math.max((y - b) / (PH.item * vh), 0), N - 1);
      const i0 = Math.floor(t); const i1 = Math.min(i0 + 1, N - 1);
      const d = (-((pos[i0] || 0) + ((pos[i1] || 0) - (pos[i0] || 0)) * (t - i0))).toFixed(1);
      rail.style.transform = vertical ? `translate3d(0, ${d}px, 0)` : `translate3d(${d}px, 0, 0)`;
      const idx = Math.min(N - 1, Math.round(t));
      // the scroll switches too (the card at the top of the column becomes active);
      // moving the mouse onto a card also does
      if (ZONES[idx].slug !== activeSlug) setActiveZone(ZONES[idx].slug);
    };
    // a click on a card scrolls to the moment it becomes active
    rail.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-zone]');
      if (!btn || hoverMode) return; // hover mode: the card's own click opens the detail
      const i = ZONES.findIndex((z) => z.slug === btn.dataset.zone);
      const { b } = bounds();
      const top = stage.offsetTop + b + i * PH.item * vh + 2;
      if (window.WI_LENIS) window.WI_LENIS.scrollTo(top, { duration: 1.2 });
      else window.scrollTo({ top, behavior: reduceMotion() ? 'auto' : 'smooth' });
    });
    window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(update); } }, { passive: true });
    window.addEventListener('resize', () => { measure(); update(); });
    // card positions are only known once the column is laid out (styles, breakpoints):
    // measure again whenever its size changes
    if ('ResizeObserver' in window) new ResizeObserver(() => { measure(); update(); }).observe(rail);
    measure();
    update();
    window.addEventListener('load', () => { measure(); update(); });
  } else if (stage) {
    // Always open the Home on the intro: don't let the browser restore an old
    // scroll position (reload, back button), which would land on the list / Q&A.
    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
    if (!location.hash) window.scrollTo(0, 0);
    // States in scroll order; each one gets an equal share of the pinned scroll distance.
    const STATES = [...hero.querySelectorAll('[data-hero-layer]')].map((l) => l.dataset.heroLayer);
    let ticking = false;
    const update = () => {
      ticking = false;
      const distance = stage.offsetHeight - window.innerHeight;
      const progress = distance > 0 ? Math.min(Math.max(-stage.getBoundingClientRect().top / distance, 0), 1) : 0;
      // each state owns an equal share of the scroll: intro stays until ¼ of the way, etc.
      const index = Math.min(STATES.length - 1, Math.round(progress * (STATES.length - 1)));
      setHeroState(STATES[index]);
    };
    window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(update); } }, { passive: true });
    window.addEventListener('resize', update);
    update();
  } else {
    setHeroState('list');
  }

  /* ---------------------------------------------------------------------
     5. Detail page (Figma: Detail — nav 330 px + Mission/Education detail 1396 px)
     --------------------------------------------------------------------- */
  const detail = document.createElement('div');
  detail.className = 'detail';
  detail.setAttribute('data-lenis-prevent', ''); // its panel scrolls on its own (no smooth-scroll hijack)
  detail.hidden = true;
  const navGroups = Object.entries(CATS).map(([key, cat]) => `
    <div class="detail-nav__group">
      <p class="detail-nav__category">${esc(cat.navLabel)}</p>
      ${ALL.filter((z) => z.category === key).map((z) => `
        <button class="detail-nav__item" type="button" data-detail-go="${z.slug}">
          <span class="detail-nav__name">${esc(z.name)}</span>
          <span class="detail-nav__summary">${esc(z.summary)}</span>
        </button>`).join('')}
    </div>`).join('');
  // The detail panel repeats the page's own main menu (so it follows the WordPress menu too).
  const siteLogo = document.querySelector('.site-header a.logo');
  const homeHref = siteLogo?.getAttribute('href') || 'home.html';
  const homeLabel = siteLogo?.getAttribute('aria-label') || 'Wildlife Interactions — accueil';
  const pageCat = (href) => Object.keys(CATS).find((k) => CATS[k].page === href) || '';
  const menuLinks = [...document.querySelectorAll('.site-nav .site-nav__list a')].map((a) => {
    const href = a.getAttribute('href');
    const cat = a.dataset.menuCat || pageCat(href);
    return `<li><a class="menu-item menu-item--secondary" href="${esc(href)}"${cat ? ` data-menu-cat="${cat}"` : ''}>${esc(a.textContent.trim())}</a></li>`;
  }).join('');
  const navCta = document.querySelector('.site-nav .cta');
  const menuCta = navCta ? `<a class="cta cta--secondary" href="${esc(navCta.getAttribute('href'))}">${esc(navCta.textContent.trim())}</a>` : '';
  detail.innerHTML = `
    <aside class="detail-nav" aria-label="${esc(T('detailNav', 'Missions et programmes'))}">
      <div class="detail-nav__top">
        <a class="logo logo--sm" href="${esc(homeHref)}" aria-label="${esc(homeLabel)}"><img src="${ASSET('assets/logos/logo-inverse.svg')}" width="63" height="68" alt=""></a>
        <button class="detail-nav__close" type="button" data-detail-close aria-label="${esc(T('close', 'Fermer'))}">
          <span class="icon icon--close" aria-hidden="true"></span>
        </button>
      </div>
      <nav class="detail-nav__main">${navGroups}</nav>
    </aside>
    <article class="detail__panel" role="dialog" aria-modal="true" aria-labelledby="detail-title" tabindex="-1">
      <header class="detail__menu">
        <ul class="detail__menu-items">${menuLinks}</ul>
        ${menuCta}
      </header>
      <div class="detail__body" data-detail-body></div>
    </article>`;
  document.body.appendChild(detail);
  const panel = detail.querySelector('.detail__panel');
  const body = detail.querySelector('[data-detail-body]');
  let lastTrigger = null;

  const statHTML = (st, tone) => `
    <div class="stat stat--${tone}">
      <p class="stat__value">${esc(st.value)}</p>
      <div class="stat__des"><p class="stat__label">${esc(st.label)}</p><p class="stat__text">${esc(st.text)}</p></div>
    </div>`;
  /* Team cards: names come from js/detail-content.js, photo + LinkedIn from js/talents-data.js */
  const MORE = window.WI_DETAIL_MORE || {};
  const norm = (n) => String(n).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
  const TALENT_BY_NAME = new Map((window.WI_TALENTS || []).map((t) => [norm(t.name), t]));
  const missionsOf = (name) => ALL.filter((z) => (MORE[z.slug]?.team?.people || []).some(([n]) => norm(n) === norm(name)));
  const talentHTML = (t) => {
    const photo = t.photo || null;
    const acts = t.activities || [];
    return `
    <article class="talent-card${photo ? '' : ' talent-card--no-photo'}" tabindex="0">
      ${photo ? `<img class="talent-card__photo" src="${photo}" alt="" loading="lazy">` : ''}
      <div class="talent-card__content">
        ${acts.length ? `<div class="talent-card__bio"><div class="talent-card__bio-inner">
          <p class="talent-card__bio-title">${esc(T('talentActivities', 'Activités WI'))}</p>
          <p class="talent-card__activities">${acts.map((a) => typeof a === 'string'
            ? `<a href="#">${esc(a)}</a>`
            : `<a href="#${a.slug}" data-detail-go="${a.slug}">${esc(a.name)}</a>`).join('')}</p>
        </div></div>` : ''}
        <div class="talent-card__title">
          <div class="talent-card__name-row">
            <p class="talent-card__name">${esc(t.name)}</p>
            <a class="talent-card__linkedin" href="${esc(t.linkedin || 'https://www.linkedin.com/')}" target="_blank" rel="noopener" aria-label="${esc(T('linkedinOf', 'LinkedIn de'))} ${esc(t.name)}"><span class="icon icon--linkedin" aria-hidden="true"></span></a>
          </div>
          <p class="talent-card__role">${esc(t.role)}</p>
        </div>
      </div>
    </article>`;
  };
  const teamMember = ([name, role]) => {
    const known = TALENT_BY_NAME.get(norm(name));
    return {
      name: known ? known.name : name,
      role,
      photo: known?.photo ? talentPhoto(known.photo) : null,
      linkedin: known?.linkedin,
      activities: missionsOf(name),
    };
  };
  const enjeu = (it, mod, img) => `
    <div class="enjeu enjeu--${mod}">
      ${img ? `<img class="enjeu__bg" src="${img}" alt="" loading="lazy">` : ''}
      <p class="enjeu__title">${esc(it.title)}</p><p class="enjeu__text">${esc(it.text)}</p>
    </div>`;

  const renderDetail = (zone) => {
    const d = zone.detail;
    const i = ALL.indexOf(zone);
    const prev = ALL[(i - 1 + ALL.length) % ALL.length];
    const next = ALL[(i + 1) % ALL.length];
    const [e1, e2, e3, e4] = d.enjeux;
    const more = MORE[zone.slug] || {};
    // empty parts (nothing filled in WordPress) are left out
    const moreStats = more.stats || SHARED.stats || [];
    const team = more.team ? more.team.people.map(teamMember) : (SHARED.talents || []);
    const stats = d.stats || [];
    body.innerHTML = `
      <div class="detail__key">
        <section class="detail__overview">
          <div class="detail__intro">
            <span class="tag tag--light">${esc(T('overview', 'Aperçu'))}</span>
            <div class="detail__intro-text">
              <h2 class="detail__title" id="detail-title">${esc(zone.name)}</h2>
              <p class="detail__lead">${esc(d.lead)}</p>
            </div>
          </div>
          <img class="detail__image" src="${zone.image}" alt="${esc(zone.name)}">
        </section>
        <section class="detail__enjeux" aria-labelledby="detail-enjeux-title">
          <div class="detail__section-head">
            <span class="tag tag--light">${esc(T('issues', 'Enjeux'))}</span>
            <h3 class="detail__h4" id="detail-enjeux-title">${esc(d.enjeuxTitle || T('issuesTitle', 'Pourquoi agir maintenant ?'))}</h3>
          </div>
          <div class="enjeux">
            <div class="enjeux__line enjeux__line--a">${enjeu(e1, 'image', d.enjeuxImage)}${enjeu(e2, 'medium')}</div>
            <div class="enjeux__line enjeux__line--b">${enjeu(e3, 'lowest')}${enjeu(e4, 'high')}</div>
          </div>
          ${stats.length ? `<div class="stats">${stats.map((st) => statHTML(st, 'light')).join('')}</div>` : ''}
        </section>
      </div>
      <div class="detail__more">
        ${(more.sections || SHARED.sections).map((sec) => `
          <section class="detail__section">
            <span class="tag tag--black">${esc(sec.tag)}</span>
            <div class="detail__section-text"><h3 class="detail__h5">${esc(sec.title)}</h3><p class="detail__section-lead">${esc(sec.text).replace(/\*([^*]+)\*/g, '<em>$1</em>')}</p></div>
          </section>`).join('')}
        ${moreStats.length ? `<div class="stats">${moreStats.map((st) => statHTML(st, 'dark')).join('')}</div>` : ''}
        ${team.length ? `<section class="detail__section">
          <span class="tag tag--black">${esc(more.team?.tag || 'Tag')}</span>
          <div class="detail__section-text">
            <h3 class="detail__h5">${esc(more.team?.title || SHARED.talentsTitle)}</h3>
            <div class="talents">${team.map(talentHTML).join('')}</div>
          </div>
        </section>` : ''}
        <div class="detail__ctas">
          <button class="cta cta--primary" type="button" data-detail-go="${prev.slug}" aria-label="${esc(T('previous', 'Précédente'))} : ${esc(prev.name)}">
            <span class="icon icon--arrow-left" aria-hidden="true"></span>${esc(T('previous', 'Précédente'))}
          </button>
          <button class="cta cta--primary" type="button" data-detail-go="${next.slug}" aria-label="${esc(T('next', 'Prochaine'))} : ${esc(next.name)}">
            ${esc(T('next', 'Prochaine'))}<span class="icon icon--arrow-right" aria-hidden="true"></span>
          </button>
        </div>
      </div>`;

    // nav + menu state
    detail.querySelectorAll('.detail-nav__item').forEach((b) => {
      const on = b.dataset.detailGo === zone.slug;
      b.classList.toggle('is-active', on);
      if (on) b.setAttribute('aria-current', 'true'); else b.removeAttribute('aria-current');
    });
    detail.querySelectorAll('[data-menu-cat]').forEach((a) => {
      if (a.dataset.menuCat === zone.category) a.setAttribute('aria-current', 'page');
      else a.removeAttribute('aria-current');
    });
  };

  function openDetail(slug, trigger) {
    const zone = bySlug(slug);
    if (!zone) return;
    if (trigger) lastTrigger = trigger;
    renderDetail(zone);
    if (ZONES.includes(zone)) setActiveZone(slug);
    panel.scrollTop = 0;
    if (detail.hidden) {
      detail.hidden = false;
      document.documentElement.classList.add('has-detail-open');
      requestAnimationFrame(() => requestAnimationFrame(() => detail.classList.add('is-open')));
    }
    panel.focus({ preventScroll: true });
    if (location.hash.slice(1) !== slug) history.replaceState(null, '', `#${slug}`);
  }

  function closeDetail() {
    if (detail.hidden) return;
    detail.classList.remove('is-open');
    document.documentElement.classList.remove('has-detail-open');
    history.replaceState(null, '', location.pathname + location.search);
    const done = () => { detail.hidden = true; };
    reduceMotion() ? done() : setTimeout(done, 500);
    lastTrigger?.focus({ preventScroll: true });
  }

  detail.addEventListener('click', (e) => {
    if (e.target.closest('[data-detail-close]')) return closeDetail();
    const go = e.target.closest('[data-detail-go]');
    if (go) { e.preventDefault(); openDetail(go.dataset.detailGo); }
  });
  document.addEventListener('keydown', (e) => {
    if (detail.hidden) return;
    if (e.key === 'Escape') closeDetail();
    if (e.key === 'Tab') {
      const f = [...detail.querySelectorAll('a[href], button, [tabindex="0"]')].filter((el) => el.offsetParent !== null);
      if (!f.length) return;
      const first = f[0], last = f[f.length - 1];
      if (e.shiftKey && document.activeElement === first) { last.focus(); e.preventDefault(); }
      else if (!e.shiftKey && document.activeElement === last) { first.focus(); e.preventDefault(); }
    }
  });

  // used by the 3D map (js/globe.js)
  window.WI_HERO = { openDetail: (slug, trigger) => openDetail(slug, trigger), setActiveZone, hero };

  // Deep link, e.g. education.html#college
  const fromHash = location.hash.slice(1);
  if (fromHash && bySlug(fromHash)) openDetail(fromHash);
})();
