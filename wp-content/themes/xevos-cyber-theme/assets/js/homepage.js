/**
 * Homepage-specific JavaScript.
 * Services swiper, recenze swiper, kyber-test slider, eventy scrollbar.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {

    /* ===== Hero Lottie map animation ===== */
    var lottieContainer = document.getElementById('hero-lottie-map');
    if (lottieContainer && typeof lottie !== 'undefined' && typeof xevosHero !== 'undefined') {
      lottie.loadAnimation({
        container: lottieContainer,
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: xevosHero.lottieUrl
      });
    }

    /* ===== Shield Lottie animations (statistiky section) ===== */
    if (typeof lottie !== 'undefined' && typeof xevosHero !== 'undefined' && xevosHero.shieldUrl) {
      /* Main feature shield */
      var shieldMain = document.getElementById('shield-lottie');
      if (shieldMain) {
        lottie.loadAnimation({
          container: shieldMain,
          renderer: 'svg',
          loop: false,
          autoplay: true,
          path: xevosHero.shieldUrl
        });
      }

      /* Card shields */
      document.querySelectorAll('.shield-lottie-card').forEach(function (el) {
        lottie.loadAnimation({
          container: el,
          renderer: 'svg',
          loop: false,
          autoplay: true,
          path: xevosHero.shieldUrl
        });
      });
    }

    if (typeof Swiper === 'undefined') return;

    /* ===== Services — Swiper only when 5+ cards ===== */
    var servicesEl = document.getElementById('services-swiper');
    if (servicesEl) {
      var slideCount = servicesEl.querySelectorAll('.swiper-slide').length;
      if (slideCount >= 5) {
        servicesEl.classList.add('swiper-active');
        new Swiper(servicesEl, {
          slidesPerView: 1,
          spaceBetween: 24,
          navigation: {
            prevEl: document.getElementById('services-prev'),
            nextEl: document.getElementById('services-next'),
          },
          pagination: {
            el: document.getElementById('services-pagination'),
            clickable: true,
          },
          breakpoints: {
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
            1280: { slidesPerView: 4 },
          },
        });
      }
    }

    /* Aktuality + Recenze Swipers moved to main.js (shared components) */

    /* ===== Kyber test simple Swiper ===== */
    var ktEl = document.getElementById('kt-swiper');
    if (ktEl) {
      new Swiper(ktEl, {
        slidesPerView: 1,
        spaceBetween: 24,
        pagination: { el: ktEl.querySelector('.swiper-pagination'), clickable: true },
      });
    }

    /* Kyber test slider moved to main.js */

    /* ===== Eventy scrollbar ===== */
    var eventyList = document.querySelector('.xevos-eventy__list');
    var scrollThumb = document.querySelector('.xevos-eventy__scrollbar-thumb');
    if (eventyList && scrollThumb) {
      function updateScrollbar() {
        var scrollTop = eventyList.scrollTop;
        var scrollHeight = eventyList.scrollHeight - eventyList.clientHeight;
        if (scrollHeight > 0) {
          var ratio = scrollTop / scrollHeight;
          var trackHeight = scrollThumb.parentElement.clientHeight;
          var thumbHeight = scrollThumb.clientHeight;
          scrollThumb.style.top = ratio * (trackHeight - thumbHeight) + 'px';
        }
      }
      eventyList.addEventListener('scroll', updateScrollbar);
      updateScrollbar();

      /* ===== Drag-to-scroll — ovládání scrollu tažením myši ===== */
      var isDragging = false;
      var dragStartY = 0;
      var dragStartScrollTop = 0;
      var dragMoved = false;

      eventyList.addEventListener('mousedown', function (e) {
        /* Neaktivovat drag pokud uživatel klikne na odkaz/tlačítko */
        if (e.target.closest('a, button')) return;
        isDragging = true;
        dragMoved = false;
        dragStartY = e.clientY;
        dragStartScrollTop = eventyList.scrollTop;
        eventyList.classList.add('is-dragging');
        e.preventDefault();
      });

      document.addEventListener('mousemove', function (e) {
        if (!isDragging) return;
        var deltaY = e.clientY - dragStartY;
        if (Math.abs(deltaY) > 3) dragMoved = true;
        eventyList.scrollTop = dragStartScrollTop - deltaY;
      });

      function stopDrag() {
        if (!isDragging) return;
        isDragging = false;
        eventyList.classList.remove('is-dragging');
      }

      document.addEventListener('mouseup', stopDrag);
      eventyList.addEventListener('mouseleave', stopDrag);

      /* Pokud uživatel táhl, potlač následný click (aby se neotevřelo školení) */
      eventyList.addEventListener('click', function (e) {
        if (dragMoved) {
          e.preventDefault();
          e.stopPropagation();
          dragMoved = false;
        }
      }, true);
    }

    /* ===== Scroll hint — countdown při vstupu do viewportu + dismiss klikem ===== */
    var eventyListWrap = document.querySelector('.xevos-eventy__list-wrap');
    if (eventyListWrap) {
      function dismissScrollHint() {
        if (!eventyListWrap.classList.contains('is-dismissed')) {
          eventyListWrap.classList.add('is-dismissed');
        }
      }

      /* Click na overlay (::before pseudo má pointer-events: auto během is-in-view) → dismiss */
      eventyListWrap.addEventListener('click', function (e) {
        if (!eventyListWrap.classList.contains('is-in-view')) return;
        if (eventyListWrap.classList.contains('is-dismissed')) return;
        /* Klikání na odkaz/tlačítko uvnitř listu nevyvolá dismiss zvlášť — overlay je stejně odchytí */
        dismissScrollHint();
        e.stopPropagation();
      });

      if ('IntersectionObserver' in window) {
        var scrollHintObserver = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('is-in-view');
              scrollHintObserver.unobserve(entry.target);
              /* Po doběhnutí countdown animace (4s) automaticky uvolnit pointer-events */
              setTimeout(dismissScrollHint, 4000);
            }
          });
        }, {
          threshold: 0.4
        });
        scrollHintObserver.observe(eventyListWrap);
      }
    }
  });
})();
