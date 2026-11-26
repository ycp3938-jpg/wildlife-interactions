/* Wildlife Interactions — 3D map view (Figma reference 6002:2113: light globe, flag markers)
   - Toggle « Liste ⟷ Carte 3D » at the bottom of the hero (Home list state, Missions, Éducation)
   - Orthographic globe drawn on canvas with d3-geo + world-atlas (js/vendor), draggable
     (mouse / touch / arrow keys), with inertia and a slow drift when idle
   - Orange flags = places of the missions / programmes; click → detail page
     (a place with several items opens a small chooser first)
*/
(() => {
  const hero = document.querySelector('[data-hero]');
  const d3 = window.d3;
  if (!hero || !d3 || !d3.geoOrthographic || !window.topojson || !window.WI_WORLD || !window.WI_ZONES) return;

  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const category = hero.dataset.category || 'all';
  const bySlug = new Map(window.WI_ZONES.map((z) => [z.slug, z]));
  const CATS = window.WI_CATEGORIES || {};
  const T = (key, fallback) => (window.WI_I18N && window.WI_I18N[key]) || fallback;

  /* Places (lon, lat). stem = flag height in px, side = label side — so close places don't overlap.
     Éducation programmes are placed in Monaco (assumption: run from the association's base). */
  const PLACES = (window.WI_PLACES || [
    { name: 'Sanctuaire Pelagos', lon: 8.6, lat: 43.3, stem: 34, slugs: ['mer-mediterranee', 'citizen-pelagos'] },
    { name: 'Monaco', lon: 7.42, lat: 43.74, stem: 78, slugs: ['primaire', 'college', 'handicap'] },
    { name: 'Camargue', lon: 4.6, lat: 43.5, stem: 52, side: 'left', slugs: ['camargue'] },
    { name: 'Mer Rouge', lon: 35.2, lat: 27.0, stem: 48, slugs: ['mer-rouge'] },
    { name: 'Polynésie française', lon: -149.8, lat: -17.5, stem: 48, slugs: ['polynesie-francaise'] },
    { name: 'Spitzberg', lon: 15.6, lat: 78.2, stem: 44, slugs: ['spitzberg'] },
    { name: 'Groenland', lon: -24, lat: 70.5, stem: 44, side: 'left', slugs: ['groenland'] },
    { name: 'Péninsule Antarctique', lon: -62, lat: -64.5, stem: 48, slugs: ['peninsule-antarctique'] },
  ]).map((p) => ({ ...p, items: p.slugs.map((s) => bySlug.get(s)).filter((z) => z && (category === 'all' || z.category === category)) }))
   .filter((p) => p.items.length);

  /* ---------- markup: toggle + globe ---------- */
  const toggle = document.createElement('div');
  toggle.className = 'view-toggle';
  toggle.setAttribute('role', 'group');
  toggle.setAttribute('aria-label', T('view', 'Affichage'));
  toggle.innerHTML = `
    <button class="view-toggle__label" type="button" data-view="list" aria-pressed="true">${esc(T('list', 'Liste'))}</button>
    <button class="view-toggle__switch" type="button" aria-label="${esc(T('toggleView', 'Basculer entre la liste et la carte 3D'))}"><span class="view-toggle__knob"></span></button>
    <button class="view-toggle__label" type="button" data-view="map" aria-pressed="false">${esc(T('map', 'Carte 3D'))}</button>`;
  const wrap = document.createElement('div');
  wrap.className = 'hero__globe';
  wrap.innerHTML = `
    <canvas class="globe__canvas" tabindex="0" aria-label="${esc(T('globeLabel', 'Carte 3D interactive : glisser pour faire tourner le globe, flèches du clavier pour le faire pivoter'))}"></canvas>
    <div class="globe__markers"></div>
    <div class="globe__chooser" hidden></div>
    <p class="globe__hint">${esc(T('globeHint', 'Glisser pour faire tourner'))}</p>`;
  hero.querySelector('[data-hero-bgs]').after(wrap);
  // Home: the switch sits small under the active item's name; elsewhere at the bottom of the hero
  const slot = hero.querySelector('[data-view-toggle-slot]');
  if (slot) toggle.classList.add('view-toggle--inline');
  (slot || hero).appendChild(toggle);

  const canvas = wrap.querySelector('canvas');
  const ctx = canvas.getContext('2d');
  const layer = wrap.querySelector('.globe__markers');
  const chooser = wrap.querySelector('.globe__chooser');

  const markers = PLACES.map((p) => {
    const b = document.createElement('button');
    b.type = 'button';
    b.className = `globe-flag${p.side === 'left' ? ' globe-flag--left' : ''}`;
    b.style.setProperty('--stem', `${p.stem}px`);
    const n = String(p.items.length).padStart(2, '0');
    b.setAttribute('aria-label', `${p.name} — ${p.items.length > 1 ? `${p.items.length} ${T('projects', 'projets')}` : p.items[0].name}`);
    b.innerHTML = `<span class="globe-flag__label"><span class="globe-flag__count">${n}</span><span class="globe-flag__name">${esc(p.name)}</span></span><span class="globe-flag__stem"></span><span class="globe-flag__dot"></span>`;
    layer.appendChild(b);
    return { p, el: b };
  });

  /* ---------- geo ---------- */
  const world = window.WI_WORLD;
  const land = window.topojson.feature(world, world.objects.countries);
  const borders = window.topojson.mesh(world, world.objects.countries, (a, b) => a !== b);
  const graticule = d3.geoGraticule10();
  const sphere = { type: 'Sphere' };
  const projection = d3.geoOrthographic().clipAngle(90).precision(0.6);
  const path = d3.geoPath(projection, ctx);

  // start centred on the Mediterranean (Éducation: on Monaco), slightly tilted
  const rot = category === 'education' ? [-8, -40] : [-12, -32];
  let W = 0; let H = 0; let R = 0; let dpr = 1;

  const resize = () => {
    W = wrap.clientWidth; H = wrap.clientHeight;   // layout size (not affected by the scale-in)
    dpr = Math.min(window.devicePixelRatio || 1, 2);
    canvas.width = Math.round(W * dpr); canvas.height = Math.round(H * dpr);
    canvas.style.width = `${W}px`; canvas.style.height = `${H}px`;
    // whole globe visible between the header (top) and the switch (bottom)
    const top = 120; const bottom = 96;
    R = Math.max(80, Math.min(W * 0.36, (H - top - bottom) / 2));
    projection.scale(R).translate([W / 2, top + (H - top - bottom) / 2]);
    draw();
  };

  const draw = () => {
    if (!W) return;
    projection.rotate(rot);
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    ctx.clearRect(0, 0, W, H);
    const [cx, cy] = projection.translate();
    // sphere (Figma: light grey globe)
    ctx.beginPath(); path(sphere); ctx.fillStyle = '#d6dbde'; ctx.fill();
    ctx.beginPath(); path(graticule); ctx.strokeStyle = 'rgba(4, 20, 24, 0.05)'; ctx.lineWidth = 0.6; ctx.stroke();
    ctx.beginPath(); path(land); ctx.fillStyle = '#eef1f3'; ctx.fill();
    ctx.beginPath(); path(borders); ctx.strokeStyle = 'rgba(4, 20, 24, 0.14)'; ctx.lineWidth = 0.5; ctx.stroke();
    // soft light from the top left → feels like a sphere
    const g = ctx.createRadialGradient(cx - R * 0.35, cy - R * 0.45, R * 0.1, cx, cy, R);
    g.addColorStop(0, 'rgba(255,255,255,0.35)');
    g.addColorStop(0.6, 'rgba(255,255,255,0)');
    g.addColorStop(1, 'rgba(4,20,24,0.18)');
    ctx.beginPath(); path(sphere); ctx.fillStyle = g; ctx.fill();
    ctx.beginPath(); path(sphere); ctx.strokeStyle = 'rgba(255,255,255,0.5)'; ctx.lineWidth = 1; ctx.stroke();
    // flags
    const centre = [-rot[0], -rot[1]];
    markers.forEach((m) => {
      const ll = [m.p.lon, m.p.lat];
      const dist = d3.geoDistance(ll, centre);              // 0 = facing us, π/2 = edge
      const [x, y] = projection(ll);
      const visible = dist < Math.PI / 2 - 0.04;
      m.el.style.transform = `translate3d(${x.toFixed(1)}px, ${y.toFixed(1)}px, 0)`;
      m.el.style.opacity = visible ? String(Math.min(1, (Math.PI / 2 - dist) / 0.25)) : '0';
      m.el.style.visibility = visible ? 'visible' : 'hidden';
      m.el.style.zIndex = String(Math.round((Math.PI - dist) * 100));
    });
    if (openMarker) placeChooser(openMarker);
  };

  /* ---------- interaction: drag with inertia, idle drift, keys ---------- */
  let dragging = false; let last = null; let vel = [0, 0]; let idleSince = performance.now(); let raf = 0;
  const shown = () => hero.dataset.view === 'map' && hero.dataset.state === 'list';
  const k = () => 70 / R; // degrees per pixel, so the surface follows the pointer roughly

  const loop = (t) => {
    raf = 0;
    if (!shown()) return;
    let moving = false;
    if (!dragging && (Math.abs(vel[0]) > 0.01 || Math.abs(vel[1]) > 0.01)) {
      rot[0] += vel[0]; rot[1] = Math.max(-80, Math.min(80, rot[1] + vel[1]));
      vel = [vel[0] * 0.93, vel[1] * 0.93]; moving = true;
    } else if (!dragging && !reduce && !openMarker && !wrap.matches(':hover') && t - idleSince > 2500) {
      rot[0] += 0.06; moving = true;                        // slow drift when nobody touches it
    }
    if (moving || dragging) draw();
    raf = requestAnimationFrame(loop);
  };
  const start = () => { if (!raf) raf = requestAnimationFrame(loop); };

  canvas.addEventListener('pointerdown', (e) => {
    dragging = true; last = [e.clientX, e.clientY]; vel = [0, 0];
    canvas.setPointerCapture(e.pointerId); wrap.classList.add('is-dragging'); closeChooser();
  });
  canvas.addEventListener('pointermove', (e) => {
    if (!dragging) return;
    const dx = e.clientX - last[0]; const dy = e.clientY - last[1];
    last = [e.clientX, e.clientY];
    vel = [dx * k(), -dy * k()];
    rot[0] += vel[0]; rot[1] = Math.max(-80, Math.min(80, rot[1] + vel[1]));
    draw();
  });
  const end = () => { if (!dragging) return; dragging = false; idleSince = performance.now(); wrap.classList.remove('is-dragging'); wrap.classList.add('was-dragged'); start(); };
  canvas.addEventListener('pointerup', end);
  canvas.addEventListener('pointercancel', end);
  canvas.addEventListener('keydown', (e) => {
    const step = { ArrowLeft: [-10, 0], ArrowRight: [10, 0], ArrowUp: [0, -8], ArrowDown: [0, 8] }[e.key];
    if (!step) return;
    e.preventDefault();
    vel = [step[0] * 0.08, step[1] * 0.08]; idleSince = performance.now(); start();
  });

  /* ---------- flags: hover = background of that place, click = detail / chooser ---------- */
  let openMarker = null;
  const placeChooser = (m) => {
    const r = m.el.getBoundingClientRect(); const w = wrap.getBoundingClientRect();
    chooser.style.left = `${r.left - w.left + (m.p.side === 'left' ? -8 : r.width + 8)}px`;
    chooser.style.top = `${r.top - w.top}px`;
    chooser.classList.toggle('globe__chooser--left', m.p.side === 'left');
  };
  const closeChooser = () => { openMarker = null; chooser.hidden = true; markers.forEach((m) => m.el.setAttribute('aria-expanded', 'false')); };
  markers.forEach((m) => {
    m.el.addEventListener('mouseenter', () => window.WI_HERO?.setActiveZone(m.p.items[0].slug));
    m.el.addEventListener('focus', () => window.WI_HERO?.setActiveZone(m.p.items[0].slug));
    m.el.addEventListener('click', (e) => {
      e.stopPropagation();
      if (m.p.items.length === 1) { window.WI_HERO?.openDetail(m.p.items[0].slug, m.el); return; }
      if (openMarker === m) { closeChooser(); return; }
      openMarker = m;
      chooser.innerHTML = `<p class="globe__chooser-title">${esc(m.p.name)}</p>` + m.p.items.map((z) => `
        <button type="button" class="globe__chooser-item" data-slug="${z.slug}">
          <span class="globe__chooser-cat">${esc(CATS[z.category]?.label || '')}</span>${esc(z.name)}
        </button>`).join('');
      chooser.hidden = false;
      m.el.setAttribute('aria-expanded', 'true');
      placeChooser(m);
      chooser.querySelector('button')?.focus({ preventScroll: true });
    });
  });
  chooser.addEventListener('click', (e) => {
    const b = e.target.closest('[data-slug]');
    if (!b) return;
    const from = openMarker?.el;
    closeChooser();
    window.WI_HERO?.openDetail(b.dataset.slug, from);
  });
  document.addEventListener('click', (e) => { if (openMarker && !chooser.contains(e.target)) closeChooser(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && openMarker) { const el = openMarker.el; closeChooser(); el.focus(); } });

  /* ---------- toggle ---------- */
  const setView = (view) => {
    hero.dataset.view = view;
    toggle.querySelectorAll('[data-view]').forEach((b) => b.setAttribute('aria-pressed', String(b.dataset.view === view)));
    toggle.dataset.view = view;
    closeChooser();
    if (view === 'map') { resize(); idleSince = performance.now(); start(); }
  };
  toggle.addEventListener('click', (e) => {
    const b = e.target.closest('button');
    if (!b) return;
    setView(b.dataset.view || (hero.dataset.view === 'map' ? 'list' : 'map'));
  });
  document.addEventListener('wi:hero-state', () => { closeChooser(); if (shown()) { resize(); start(); } });
  window.addEventListener('resize', () => { if (shown()) resize(); });
  // no 3D map on mobile: back to the list when the screen gets narrow
  const mobile = window.matchMedia('(max-width: 760px)');
  mobile.addEventListener('change', (e) => { if (e.matches) setView('list'); });
  setView('list');
})();
