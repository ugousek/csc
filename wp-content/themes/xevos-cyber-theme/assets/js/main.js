/**
 * XEVOS Cyber Security Center – Main JS.
 *
 * All modules are enqueued separately via wp_enqueue_script,
 * or inlined here for simplicity (no bundler needed).
 *
 * @package Xevos\CyberTheme
 */

(function () {
  'use strict';

  /* ===== Search overlay toggle ===== */
  var searchToggle = document.getElementById('header-search-toggle');
  var searchOverlay = document.getElementById('search-overlay');
  var searchClose = searchOverlay ? searchOverlay.querySelector('.xevos-search-overlay__close') : null;
  var searchInput = searchOverlay ? searchOverlay.querySelector('.xevos-search-form__input') : null;

  function openSearch() {
    if (!searchOverlay) return;
    searchOverlay.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    if (searchInput) setTimeout(function() { searchInput.focus(); }, 100);
  }
  function closeSearch() {
    if (!searchOverlay) return;
    searchOverlay.classList.remove('is-open');
    document.body.style.overflow = '';
  }
  if (searchToggle) searchToggle.addEventListener('click', openSearch);
  if (searchClose) searchClose.addEventListener('click', closeSearch);
  if (searchOverlay) {
    searchOverlay.addEventListener('click', function(e) { if (e.target === searchOverlay) closeSearch(); });
  }
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && searchOverlay && searchOverlay.classList.contains('is-open')) closeSearch();
  });

  /* ===== Mobile menu close button ===== */
  var mobileClose = document.querySelector('.xevos-mobile-menu__close');
  if (mobileClose) {
    mobileClose.addEventListener('click', function() { toggleMenu(false); });
  }

  /* ===== Copy URL button ===== */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.xevos-copy-url');
    if (!btn) return;

    var url = btn.dataset.url;
    navigator.clipboard.writeText(url).then(function () {
      btn.setAttribute('aria-label', 'Zkopírováno!');
      setTimeout(function () { btn.setAttribute('aria-label', 'Kopírovat URL'); }, 2000);
    });
  });

  /* ===== Školení tabs ===== */
  document.querySelectorAll('.xevos-skoleni-single__tab').forEach(function (tab) {
    tab.addEventListener('click', function () {
      var target = tab.dataset.tab;

      document.querySelectorAll('.xevos-skoleni-single__tab').forEach(function (t) { t.classList.remove('is-active'); });
      document.querySelectorAll('.xevos-skoleni-single__tab-panel').forEach(function (p) { p.classList.remove('is-active'); });

      tab.classList.add('is-active');
      var panel = document.getElementById('tab-' + target);
      if (panel) panel.classList.add('is-active');
    });
  });

  /* ===== Sticky header ===== */
  var header = document.getElementById('header');
  if (header) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 50) {
        header.classList.add('xevos-header--scrolled');
      } else {
        header.classList.remove('xevos-header--scrolled');
      }
    }, { passive: true });
  }

  /* ===== Hamburger menu ===== */
  var hamburger = document.querySelector('.xevos-header__hamburger');
  var mobileMenu = document.getElementById('mobile-menu');
  var overlay = document.querySelector('.xevos-mobile-menu__overlay');

  function toggleMenu(open) {
    if (!hamburger || !mobileMenu) return;
    var isOpen = (open !== undefined) ? open : !mobileMenu.classList.contains('is-open');

    hamburger.classList.toggle('is-active', isOpen);
    hamburger.setAttribute('aria-expanded', isOpen);
    mobileMenu.classList.toggle('is-open', isOpen);
    mobileMenu.setAttribute('aria-hidden', !isOpen);
    if (overlay) overlay.classList.toggle('is-visible', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
  }

  if (hamburger) {
    hamburger.addEventListener('click', function () { toggleMenu(); });
  }
  if (overlay) {
    overlay.addEventListener('click', function () { toggleMenu(false); });
  }
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && mobileMenu && mobileMenu.classList.contains('is-open')) {
      toggleMenu(false);
    }
  });

  /* ===== Mobile submenu accordion ===== */
  document.querySelectorAll('.xevos-mobile-menu__nav-item').forEach(function (item) {
    var submenu = item.querySelector('.sub-menu');
    if (!submenu) return;

    submenu.classList.add('xevos-mobile-menu__submenu');

    var link = item.querySelector(':scope > a');
    var toggle = document.createElement('button');
    toggle.className = 'xevos-mobile-menu__toggle';
    toggle.setAttribute('aria-label', 'Rozbalit podmenu');
    toggle.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';

    if (link) link.after(toggle);

    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      submenu.classList.toggle('is-open');
      toggle.classList.toggle('is-active');
    });
  });

  /* ===== Live search ===== */
  var searchInput = document.getElementById('xevos-search');
  var resultsContainer = document.getElementById('live-search-results');

  if (searchInput && resultsContainer) {
    var debounceTimer;

    searchInput.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      var query = searchInput.value.trim();

      if (query.length < 3) {
        resultsContainer.hidden = true;
        resultsContainer.innerHTML = '';
        return;
      }

      debounceTimer = setTimeout(function () { fetchSearchResults(query); }, 300);
    });

    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') resultsContainer.hidden = true; });
    document.addEventListener('click', function (e) { if (!e.target.closest('.xevos-search-form')) resultsContainer.hidden = true; });
  }

  /* Escape HTML to prevent XSS */
  function escHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str || ''));
    return div.innerHTML;
  }

  function fetchSearchResults(query) {
    if (!window.xevosAjax) return;

    var params = new URLSearchParams({
      action: 'xevos_live_search',
      nonce: xevosAjax.nonce,
      query: query,
    });

    fetch(xevosAjax.ajaxUrl + '?' + params)
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success && data.data.results.length) {
          resultsContainer.innerHTML = '';
          data.data.results.forEach(function (group) {
            var groupEl = document.createElement('div');
            groupEl.className = 'xevos-live-search__group';

            var labelEl = document.createElement('div');
            labelEl.className = 'xevos-live-search__group-label';
            labelEl.textContent = group.type;
            groupEl.appendChild(labelEl);

            group.items.forEach(function (item) {
              var itemEl = document.createElement('div');
              itemEl.className = 'xevos-live-search__item';
              var link = document.createElement('a');
              link.href = item.url;
              var strong = document.createElement('strong');
              strong.textContent = item.title;
              var span = document.createElement('span');
              span.textContent = item.excerpt;
              link.appendChild(strong);
              link.appendChild(span);
              itemEl.appendChild(link);
              groupEl.appendChild(itemEl);
            });

            resultsContainer.appendChild(groupEl);
          });
          resultsContainer.hidden = false;
        } else {
          resultsContainer.textContent = '';
          var empty = document.createElement('div');
          empty.className = 'xevos-live-search__empty';
          empty.textContent = 'Nic nenalezeno';
          resultsContainer.appendChild(empty);
          resultsContainer.hidden = false;
        }
      });
  }

  /* Blog archive filter/pagination/sort — moved to archive-filter.js */

  /* Školení archive filter — moved to skoleni-filter.js */

  /* ===== Pattern overlay position ===== */
  document.addEventListener('DOMContentLoaded', function () {
    var ktSection = document.querySelector('.xevos-kyber-test-section');
    if (ktSection) {
      var top = ktSection.getBoundingClientRect().top + window.scrollY + ktSection.offsetHeight * 0.5;
      document.body.style.setProperty('--pattern-top', top + 'px');
    }
  });

  /* ===== Swiper init ===== */
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swiper === 'undefined') return;

    var servicesEl = document.getElementById('services-swiper');
    if (servicesEl) {
      new Swiper(servicesEl, {
        slidesPerView: 1, spaceBetween: 24,
        navigation: {
          prevEl: document.getElementById('services-prev'),
          nextEl: document.getElementById('services-next'),
        },
        breakpoints: { 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 }, 1280: { slidesPerView: 4 } },
      });
    }

    var recenzeEl = document.getElementById('recenze-swiper');
    if (recenzeEl) {
      new Swiper(recenzeEl, {
        slidesPerView: 1, spaceBetween: 50, loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false, pauseOnMouseEnter: true },
        pagination: { el: recenzeEl.querySelector('.swiper-pagination'), clickable: true },
        breakpoints: { 768: { slidesPerView: 2, spaceBetween: 50 }, 1024: { slidesPerView: 3, spaceBetween: 50 } },
      });
    }

    var ktEl = document.getElementById('kt-swiper');
    if (ktEl) {
      new Swiper(ktEl, {
        slidesPerView: 1, spaceBetween: 24,
        pagination: { el: ktEl.querySelector('.swiper-pagination'), clickable: true },
      });
    }

    /* Kyber test panel slider — custom numbered pagination + image swap */
    var ktPanelEl = document.getElementById('kyber-test-panel-swiper');
    var ktPagination = document.getElementById('kyber-test-pagination');
    if (ktPanelEl && ktPagination) {
      var ktMainImg = document.getElementById('kyber-test-main-img');
      var ktImagesData = document.getElementById('kyber-test-images');
      var ktImages = ktImagesData ? JSON.parse(ktImagesData.textContent) : [];

      new Swiper(ktPanelEl, {
        slidesPerView: 1,
        spaceBetween: 0,
        effect: 'fade',
        fadeEffect: { crossFade: true },
        pagination: {
          el: ktPagination,
          clickable: true,
          renderBullet: function (index, className) {
            return '<span class="' + className + '">' + (index + 1) + '</span>';
          },
        },
        on: {
          slideChange: function () {
            if (ktMainImg && ktImages[this.activeIndex]) {
              ktMainImg.src = ktImages[this.activeIndex];
            }
          }
        }
      });
    }

    /* Eventy scrollbar — tracks scroll position of .xevos-eventy__list */
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
          scrollThumb.style.top = (ratio * (trackHeight - thumbHeight)) + 'px';
        }
      }
      eventyList.addEventListener('scroll', updateScrollbar);
      updateScrollbar();
    }
  });

})();
