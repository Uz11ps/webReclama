/* global THREE, gsap */
// 3D фон удалён по требованию

(function initReveal() {
  if (!window.gsap) return;
  var reduce = false;
  try { reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches; } catch (e) {}
  var base = { ease: 'power2.out', duration: reduce ? 0.01 : 0.8 };
  window.gsap.from('.hero__title', { y: 20, opacity: 0, duration: base.duration, ease: base.ease });
  window.gsap.from('.hero__subtitle', { y: 14, opacity: 0, duration: base.duration, delay: reduce ? 0 : 0.1, ease: base.ease });
  window.gsap.from('.hero__cta .btn', { y: 10, opacity: 0, duration: reduce ? 0.01 : 0.7, delay: reduce ? 0 : 0.2, stagger: reduce ? 0 : 0.08, ease: 'power2.out' });

  if (window.ScrollTrigger && !reduce) {
    var cats = document.querySelectorAll('.category');
    Array.prototype.forEach.call(cats, function(cat) {
      window.gsap.from(cat, {
        y: 30, opacity: 0, duration: 0.7, ease: 'power2.out',
        scrollTrigger: { trigger: cat, start: 'top 80%' }
      });
      var cards = cat.querySelectorAll('.card');
      window.gsap.from(cards, {
        y: 16, opacity: 0, duration: 0.5, stagger: 0.05, ease: 'power2.out',
        scrollTrigger: { trigger: cat, start: 'top 78%' }
      });
    });
  }
})();

(function initFilters() {
  var search = document.getElementById('search');
  var select = document.getElementById('categorySelect');
  var categories = document.querySelectorAll('.category');
  var chipsWrap = document.getElementById('categoryChips');

  function applyFilters() {
    var q = (search && search.value ? search.value : '').trim().toLowerCase();
    var activeChip = chipsWrap ? chipsWrap.querySelector('.chip.is-active') : null;
    var chipCat = activeChip ? activeChip.getAttribute('data-cat') : '';
    var cat = chipCat || (select && select.value ? select.value : '');

    Array.prototype.forEach.call(categories, function(block) {
      var currentCat = block.getAttribute('data-category') || '';
      var visibleCards = 0;
      var cards = block.querySelectorAll('.card');
      Array.prototype.forEach.call(cards, function(card) {
        var title = card.getAttribute('data-title') || '';
        var byQuery = !q || title.indexOf(q) !== -1;
        var byCat = !cat || currentCat === cat;
        var show = byQuery && byCat;
        // minimize layout thrash on mobile: toggle class instead of inline style
        if (show) {
          card.classList.remove('is-hidden');
          visibleCards += 1;
        } else {
          card.classList.add('is-hidden');
        }
      });
      if (visibleCards > 0) {
        block.classList.remove('is-hidden');
      } else {
        block.classList.add('is-hidden');
      }
    });
  }

  ['input', 'change'].forEach(function(ev) {
    if (search) search.addEventListener(ev, applyFilters);
    if (select) select.addEventListener(ev, applyFilters);
  });
  // Debounce for input on mobile
  if (search) {
    var typingTimer;
    search.addEventListener('input', function(){
      clearTimeout(typingTimer);
      typingTimer = setTimeout(applyFilters, 120);
    });
  }

  if (chipsWrap) {
    chipsWrap.addEventListener('click', function(e) {
      var target = e.target || e.srcElement;
      if (!target || !target.classList || !target.classList.contains('chip')) return;
      var chips = chipsWrap.querySelectorAll('.chip');
      Array.prototype.forEach.call(chips, function(c) { c.classList.remove('is-active'); });
      target.classList.add('is-active');
      applyFilters();
    });
  }

  // Anchor smooth scroll and hash on load
  function scrollToHash() {
    var hash = window.location.hash;
    if (hash && hash.length > 1) {
      var el = document.querySelector(hash);
      if (el && el.scrollIntoView) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }
  var anchorLinks = document.querySelectorAll('a[href^="#"]');
  Array.prototype.forEach.call(anchorLinks, function(a) {
    a.addEventListener('click', function(ev) {
      var href = a.getAttribute('href');
      if (!href || href === '#') return;
      var target = document.querySelector(href);
      if (target) {
        ev.preventDefault();
        try {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (e) {
          // fallback: просто меняем hash, а браузер прокрутит сам
          location.hash = href;
        }
        if (history && history.replaceState) {
          try { history.replaceState(null, '', href); } catch (e) {}
        } else {
          location.hash = href;
        }
      }
    });
  });
  // Initial
  scrollToHash();
})();

(function initModalAndPopular() {
  var modal = document.getElementById('modal');
  var modalTitle = document.getElementById('modalTitle');
  var modalDesc = document.getElementById('modalDesc');
  var modalClose = document.getElementById('modalClose');
  var modalOrder = document.getElementById('modalOrder');
  if (!modal || !modalTitle || !modalDesc || !modalClose || !modalOrder) return;

  function openModal(title, desc, price) {
    modalTitle.textContent = title + (price ? ' — ' + price : '');
    modalDesc.textContent = desc || '';
    modal.classList.add('is-open');
  }
  function closeModal() { modal.classList.remove('is-open'); }

  modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
  modalClose.addEventListener('click', closeModal);
  document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });

  // Open on card click
  document.addEventListener('click', function(e) {
    var el = e.target;
    while (el && el !== document) {
      if (el.classList && el.classList.contains('card')) {
        var titleEl = el.querySelector('.card__title');
        var title = titleEl ? titleEl.textContent.trim() : 'Услуга';
        var desc = el.getAttribute('data-desc') || '';
        var price = el.getAttribute('data-price') || '';
        openModal(title, desc, price);
        return;
      }
      el = el.parentNode;
    }
  });

  // Popular chip logic
  var chipsWrap = document.getElementById('categoryChips');
  var select = document.getElementById('categorySelect');
  function filterPopular() {
    var categories = document.querySelectorAll('.category');
    Array.prototype.forEach.call(categories, function(block){
      var cards = block.querySelectorAll('.card');
      var visibleCards = 0;
      Array.prototype.forEach.call(cards, function(card){
        var show = card.getAttribute('data-popular') === '1';
        card.style.display = show ? '' : 'none';
        if (show) visibleCards += 1;
      });
      block.style.display = visibleCards > 0 ? '' : 'none';
    });
  }
  if (chipsWrap) {
    chipsWrap.addEventListener('click', function(e){
      var t = e.target || e.srcElement;
      if (!t || !t.classList || !t.classList.contains('chip')) return;
      if (t.getAttribute('data-cat') === '__popular') {
        // Activate chip and clear select
        var chips = chipsWrap.querySelectorAll('.chip');
        Array.prototype.forEach.call(chips, function(c){ c.classList.remove('is-active'); });
        t.classList.add('is-active');
        if (select) select.value = '';
        filterPopular();
      }
    });
  }
})();

(function initCursor() {
  const cursor = document.querySelector('.cursor');
  if (!cursor) return;
  let x = window.innerWidth / 2;
  let y = window.innerHeight / 2;
  let tx = x;
  let ty = y;
  const lerp = (a, b, t) => a + (b - a) * t;
  window.addEventListener('mousemove', (e) => { tx = e.clientX; ty = e.clientY; });
  const tick = () => {
    x = lerp(x, tx, 0.2);
    y = lerp(y, ty, 0.2);
    cursor.style.transform = `translate(${x}px, ${y}px)`;
    requestAnimationFrame(tick);
  };
  tick();

  const hoverables = Array.from(document.querySelectorAll('a, button, .card'));
  hoverables.forEach((el) => {
    el.addEventListener('mouseenter', () => cursor.classList.add('is-big'));
    el.addEventListener('mouseleave', () => cursor.classList.remove('is-big'));
    // Card tilt light
    el.addEventListener('mousemove', (ev) => {
      const rect = el.getBoundingClientRect();
      const mx = ((ev.clientX - rect.left) / rect.width) * 100;
      const my = ((ev.clientY - rect.top) / rect.height) * 100;
      el.style.setProperty('--mx', `${mx}%`);
      el.style.setProperty('--my', `${my}%`);
    });
  });
})();

(function initForm() {
  const form = document.getElementById('contactForm');
  const status = document.getElementById('formStatus');
  if (!form || !status) return;
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    status.textContent = 'Отправка...';
    const formData = new FormData(form);
    try {
      const resp = await fetch(form.action, { method: 'POST', body: formData });
      const data = await resp.json().catch(() => ({ ok: false }));
      if (resp.ok && data.ok) {
        status.textContent = 'Заявка отправлена. Я свяжусь с вами в ближайшее время.';
        form.reset();
      } else {
        status.textContent = 'Не удалось отправить. Напишите мне в Telegram: @uz1ps';
      }
    } catch (err) {
      status.textContent = 'Ошибка сети. Попробуйте позже или напишите в Telegram: @uz1ps';
    }
  });
})();

