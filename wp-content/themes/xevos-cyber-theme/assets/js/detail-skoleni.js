/**
 * Školení detail page – termin cards, lektoři swiper, bio toggle, lightbox.
 * Loaded only on single-skoleni.
 *
 * @package Xevos\CyberTheme
 */
(function () {
  'use strict';

  /* ===== Termin card click → scroll to form + set select ===== */
  document.querySelectorAll('.xevos-termin-card[data-termin]').forEach(function (card) {
    card.addEventListener('click', function () {
      var termin = this.dataset.termin;
      var select = document.getElementById('xevos-termin-select');
      var form = document.getElementById('objednavka');
      if (select) {
        select.value = termin;
      }
      if (form) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
    card.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        this.click();
      }
    });
  });

  /* ===== Lektoři Swiper (only when 3+ slides exist) ===== */
  var lektoriEl = document.getElementById('lektori-swiper');
  if (lektoriEl && typeof Swiper !== 'undefined') {
    new Swiper('#lektori-swiper', {
      slidesPerView: 2,
      spaceBetween: 80,
      navigation: {
        prevEl: '.xevos-skoleni-lektori-section .xevos-nav-arrow--prev',
        nextEl: '.xevos-skoleni-lektori-section .xevos-nav-arrow--next',
      },
      pagination: {
        el: '.xevos-lektori-pagination',
        clickable: true,
      },
      breakpoints: {
        0: { slidesPerView: 1, spaceBetween: 24 },
        768: { slidesPerView: 2, spaceBetween: 80 },
      },
    });
  }

  /* ===== Lektor bio: zobrazit/skrýt po 3 řádcích ===== */
  document.querySelectorAll('.xevos-lektor-card__bio').forEach(function (bio) {
    var toggle = bio.nextElementSibling;
    if (!toggle || !toggle.classList.contains('xevos-lektor-card__bio-toggle')) return;
    if (bio.scrollHeight > bio.clientHeight) {
      toggle.hidden = false;
      toggle.addEventListener('click', function () {
        var expanded = bio.classList.toggle('is-expanded');
        toggle.textContent = expanded ? 'Zobrazit méně' : 'Zobrazit více';
      });
    }
  });

  /* ===== Lightbox (parking image etc.) ===== */
  var triggers = document.querySelectorAll('.xevos-lightbox-trigger');
  if (triggers.length) {
    var overlay = document.createElement('div');
    overlay.className = 'xevos-lightbox-overlay';
    overlay.innerHTML = '<button class="xevos-lightbox-close" aria-label="Zavřít">&times;</button><img src="" alt="">';
    document.body.appendChild(overlay);

    var lbImg = overlay.querySelector('img');
    function closeLb() { overlay.classList.remove('active'); }

    triggers.forEach(function (a) {
      a.addEventListener('click', function (e) {
        e.preventDefault();
        lbImg.src = a.href;
        lbImg.alt = (a.querySelector('img') || {}).alt || '';
        overlay.classList.add('active');
      });
    });

    overlay.addEventListener('click', function (e) { if (e.target !== lbImg) closeLb(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeLb(); });
  }

})();
