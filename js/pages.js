/* Wildlife Interactions — page scripts for Talents, Exposition & média, Contact.
   Each block only runs if its markup is on the page. */
(() => {
  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* =====================================================================
     TALENTS — hero categories + profile grid
     ===================================================================== */
  const TALENTS = window.WI_TALENTS || [];
  const CATS = window.WI_TALENT_CATEGORIES || [];
  const byPhoto = new Map(TALENTS.map((t) => [t.photo, t]));
  // One profile card (Figma: Talent / profiles) — used in the list and inside the hero cards
  const profileInner = (t) => `
        ${t.photo ? `<img class="profile__photo" src="assets/img/talents/${t.photo}.jpg" alt="" loading="lazy">` : ''}
        <div class="profile__content">
          <div class="profile__bio">
            <p class="profile__bio-title">Bio</p>
            <p class="profile__bio-text">${esc(t.bio)}</p>
          </div>
          <div class="profile__title">
            <div class="profile__name-row">
              <h3 class="profile__name">${esc(t.name)}</h3>
              <a class="profile__linkedin" href="${t.linkedin || 'https://www.linkedin.com/'}" target="_blank" rel="noopener" aria-label="LinkedIn de ${esc(t.name)}">
                <span class="icon icon--linkedin" aria-hidden="true"></span>
              </a>
            </div>
            <p class="profile__role">${esc(t.role)}</p>
          </div>
        </div>`;

  /* Hero cards: category label (photo on hover). Once the visitor starts scrolling,
     each one turns, in place, into the same profile card as the people that follow. */
  const catRow = document.querySelector('[data-talent-categories]');
  if (catRow && CATS.length) {
    catRow.innerHTML = CATS.map((c) => {
      const t = byPhoto.get(c.photo);
      return `
      <li class="talent-cat${c.label ? '' : ' talent-cat--photo'}" tabindex="0">
        <img class="talent-cat__photo" src="assets/img/talents/${c.photo}.jpg" alt="${esc(c.alt || '')}" loading="lazy">
        ${c.label ? `<span class="talent-cat__label">${esc(c.label)}</span>` : ''}
        ${t ? `<div class="profile talent-cat__card${t.photo ? '' : ' profile--no-photo'}">${profileInner(t)}</div>` : ''}
      </li>`;
    }).join('');
  }

  const grid = document.querySelector('[data-profiles]');
  if (grid && TALENTS.length) {
    // On wide screens the hero people are shown by the hero cards themselves;
    // on small screens (no horizontal scroll) they open the list instead.
    const inHero = new Set(CATS.map((c) => c.photo));
    const heroPeople = CATS.map((c) => byPhoto.get(c.photo)).filter(Boolean);
    const others = TALENTS.filter((t) => !inHero.has(t.photo));
    grid.innerHTML = [...heroPeople.map((t) => [t, true]), ...others.map((t) => [t, false])].map(([t, dup]) => `
      <li class="profile${t.photo ? '' : ' profile--no-photo'}${dup ? ' profile--hero-dup' : ''}" tabindex="0">${profileInner(t)}
      </li>`).join('');
  }

  /* Horizontal scroll: the stage gets the height of the track's width,
     and the sticky track slides left as the visitor scrolls down. */
  const hStage = document.querySelector('[data-h-stage]');
  if (hStage) {
    const track = hStage.querySelector('[data-h-track]');
    const wide = window.matchMedia('(min-width: 761px)');
    let distance = 0;
    let ticking = false;
    const measure = () => {
      if (!wide.matches) {
        hStage.style.height = '';
        track.style.transform = '';
        distance = 0; hold = 0;
        sizeCards();
        hStage.classList.remove('is-cards', 'is-cards-notitle');
        paintHeader();
        return;
      }
      const t = track.style.transform;
      track.style.transform = '';
      sizeCards();
      track.style.transform = t;
      distance = Math.max(0, track.scrollWidth - window.innerWidth);
      hold = Math.round(window.innerHeight * 0.6);
      hStage.style.height = `${window.innerHeight + hold + distance}px`;
      update();
    };
    // header items that flip to the light-surface style when the light panel is under them
    const header = hStage.querySelector('[data-h-header]');
    const lightPanel = track.querySelector('.profiles');
    const flips = header ? [...header.querySelectorAll('.logo, .menu-item, .cta, .site-header__toggle')] : [];
    const paintHeader = () => {
      const edge = distance && lightPanel ? lightPanel.getBoundingClientRect().left : Infinity;
      flips.forEach((el) => {
        const r = el.getBoundingClientRect();
        el.classList.toggle('is-light', r.width > 0 && edge < r.left + r.width / 2);
      });
    };
    // Hero cards → profile cards. The 5 cards share the row width (between the side
    // margins, 32 px gaps) and take exactly the height of the list cards that follow.
    // Olivier's card (photo, no label) already has that height before scrolling.
    const GAP = 32;
    const cats = hStage.querySelector('.talent-cats');
    const subtitle = hStage.querySelector('.page-hero__subtitle');
    const listCard = hStage.querySelector('.profiles .profile:not(.profile--hero-dup)');
    const sizeCards = () => {
      if (!cats) return;
      const on = hStage.classList.contains('is-cards');
      hStage.classList.remove('is-cards', 'is-cards-notitle');
      ['--cat-h', '--card-w', '--card-h', '--photo-h'].forEach((v) => cats.style.removeProperty(v));
      if (!wide.matches) return;
      const first = cats.querySelector('.talent-cat');
      if (!first) return;
      cats.style.setProperty('--cat-h', `${Math.round(first.getBoundingClientRect().width * 259 / 320)}px`);
      const listH = Math.round(listCard ? listCard.offsetHeight : 393.5);   // same height as the list cards
      const n = cats.children.length;
      const row = cats.getBoundingClientRect();
      const w = Math.floor((row.width - (n - 1) * GAP) / n);
      const box = cats.parentElement;
      const content = box.getBoundingClientRect();
      const top = content.top + parseFloat(getComputedStyle(box).paddingTop);
      const under = content.bottom - subtitle.getBoundingClientRect().bottom - 32;
      const noTitle = under < listH;                                        // short screen: title folds away
      const h = Math.min(listH, content.bottom - top);
      cats.style.setProperty('--card-w', `${w}px`);
      cats.style.setProperty('--card-h', `${Math.floor(h)}px`);
      cats.style.setProperty('--photo-h', `${Math.floor(Math.min(listH, under))}px`);   // Olivier, before scrolling
      hStage.dataset.noTitle = noTitle ? 'true' : '';
      if (on) hStage.classList.add('is-cards');
      hStage.classList.toggle('is-cards-notitle', on && noTitle);
    };
    // Vertical scroll budget: first a pause where the hero cards turn into profiles,
    // then the sideways travel through the rest of the track.
    let hold = 0;
    const update = () => {
      ticking = false;
      let x = 0; let cards = false;
      if (distance) {
        const y = Math.max(-hStage.getBoundingClientRect().top, 0);
        cards = y > 40;
        x = Math.min(Math.max(y - hold, 0), distance);
        track.style.transform = `translate3d(${-x}px, 0, 0)`;
      }
      hStage.classList.toggle('is-cards', cards);
      hStage.classList.toggle('is-cards-notitle', cards && hStage.dataset.noTitle === 'true');
      paintHeader();
    };
    window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(update); } }, { passive: true });
    window.addEventListener('resize', measure);
    wide.addEventListener('change', measure);
    // keyboard focus inside a card that is off-screen: scroll the page so it comes into view
    track.addEventListener('focusin', (e) => {
      if (!distance) return;
      const card = e.target.closest('.profile, .talent-cat');
      if (!card) return;
      const r = card.getBoundingClientRect();
      if (r.left >= 0 && r.right <= window.innerWidth) return;
      const current = Math.min(Math.max(-hStage.getBoundingClientRect().top - hold, 0), distance);
      const target = Math.min(Math.max(current + r.left - window.innerWidth / 2 + r.width / 2, 0), distance);
      const top = hStage.offsetTop + hold + target;
      if (window.WI_LENIS) window.WI_LENIS.scrollTo(top, { duration: 1 });
      else window.scrollTo({ top, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
    measure();
    window.addEventListener('load', measure);
  }

  /* =====================================================================
     EXPOSITION & MÉDIA — magazine carousels
     ===================================================================== */
  document.querySelectorAll('[data-carousel]').forEach((root) => {
    const track = root.querySelector('[data-carousel-track]');
    const slides = [...track.children];
    const dots = root.querySelector('[data-carousel-dots]');
    let index = 0;
    dots.innerHTML = slides.map((_, i) => `<button class="carousel__dot" type="button" aria-label="Page ${i + 1}"></button>`).join('');
    const dotEls = [...dots.children];
    const go = (i) => {
      index = (i + slides.length) % slides.length;
      track.style.transform = `translateX(${-100 * index}%)`;
      slides.forEach((s, n) => s.setAttribute('aria-hidden', String(n !== index)));
      dotEls.forEach((d, n) => d.setAttribute('aria-current', n === index ? 'true' : 'false'));
    };
    root.querySelector('[data-carousel-prev]').addEventListener('click', () => go(index - 1));
    root.querySelector('[data-carousel-next]').addEventListener('click', () => go(index + 1));
    dotEls.forEach((d, n) => d.addEventListener('click', () => go(n)));
    root.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') go(index - 1);
      if (e.key === 'ArrowRight') go(index + 1);
    });
    // swipe
    let x0 = null;
    track.addEventListener('pointerdown', (e) => { x0 = e.clientX; });
    track.addEventListener('pointerup', (e) => {
      if (x0 === null) return;
      const dx = e.clientX - x0; x0 = null;
      if (Math.abs(dx) > 40) go(index + (dx < 0 ? 1 : -1));
    });
    go(0);
  });

  /* =====================================================================
     CONTACT — image cycle (Figma: image_anim) + option chips + form
     ===================================================================== */
  const anim = document.querySelector('[data-image-anim]');
  if (anim) {
    // Sequence per photo: full photo → miniature of the next one appears in the
    // centre → it zooms to the full frame → becomes the full photo → …
    const imgs = [...anim.querySelectorAll('.image-anim__img')];
    const FULL_ALONE = 3200;  // full photo on its own
    const THUMB = 2200;       // miniature visible before zooming
    const ZOOM = 2600;        // matches the CSS transition (.is-growing)
    let current = 0;
    imgs[0].classList.add('is-full');
    if (!reduceMotion && imgs.length > 1) {
      const step = () => {
        const next = (current + 1) % imgs.length;
        setTimeout(() => {
          imgs[next].classList.add('is-next');                      // 1 · miniature
          requestAnimationFrame(() => requestAnimationFrame(() => imgs[next].classList.add('is-shown')));
          setTimeout(() => {
            imgs[current].classList.add('is-leaving');
            imgs[next].classList.add('is-growing');                 // 2 · zoom
            setTimeout(() => {
              imgs[current].classList.remove('is-full', 'is-leaving');
              imgs[next].classList.remove('is-next', 'is-shown', 'is-growing');
              imgs[next].classList.add('is-full');                  // 3 · full
              current = next;
              step();
            }, ZOOM);
          }, THUMB);
        }, FULL_ALONE);
      };
      step();
    }
  }

  const form = document.querySelector('[data-contact-form]');
  if (form) {
    const chips = [...document.querySelectorAll('[data-contact-option]')];
    const intro = document.querySelector('[data-contact-intro]');
    const subject = form.querySelector('[name="subject"]');
    const select = (key) => {
      const chip = chips.find((c) => c.dataset.contactOption === key) || chips[0];
      chips.forEach((c) => {
        const on = c === chip;
        c.classList.toggle('cta--secondary', on);          // light panel: secondary (dark) chips
        c.classList.toggle('cta--secondary-outline', !on);
        c.setAttribute('aria-pressed', String(on));
      });
      intro.textContent = chip.dataset.intro;
      subject.value = chip.textContent.trim();
    };
    chips.forEach((c) => c.addEventListener('click', () => {
      select(c.dataset.contactOption);
      history.replaceState(null, '', `#${c.dataset.contactOption}`);
    }));
    const aliases = { soutenir: 'partenariat' };
    const fromHash = location.hash.slice(1);
    select(aliases[fromHash] || fromHash || chips[0].dataset.contactOption);

    const status = form.querySelector('[data-form-status]');
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      if (!form.checkValidity()) {
        form.classList.add('was-validated');
        form.querySelector(':invalid')?.focus();
        status.textContent = 'Merci de compléter les champs obligatoires.';
        return;
      }
      // No backend yet: open the visitor's mail client with the message pre-filled.
      const d = Object.fromEntries(new FormData(form));
      const body = [
        `Prénom : ${d.firstname}`, `Nom : ${d.lastname}`, `E-mail : ${d.email}`,
        d.phone && `Téléphone : ${d.phone}`, d.company && `Entreprise : ${d.company}`,
        '', d.message,
      ].filter((l) => l !== undefined && l !== '').join('\n');
      location.href = `mailto:contact@wildlife-interactions.com?subject=${encodeURIComponent(`[${d.subject}] ${d.firstname} ${d.lastname}`)}&body=${encodeURIComponent(body)}`;
      status.textContent = 'Merci ! Votre messagerie va s’ouvrir pour envoyer le message.';
    });
  }
})();
