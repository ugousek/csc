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
    }
  });
})();
