/**
 * Archive filtering, sorting, pagination & load more.
 * URL-driven state — filter, page & order are synced to query params.
 *
 * @package Xevos\CyberTheme
 */

(function () {
  'use strict';

  var filtersEl = document.getElementById('aktuality-filters');
  var gridEl = document.getElementById('aktuality-grid');
  var loadMoreBtn = document.getElementById('load-more-btn');

  if (!filtersEl || !gridEl) return;

  var sortSelect = document.getElementById('sort-order');
  var gridWrap = gridEl.closest('.xevos-grid-wrap');
  var maxPages = 1;

  /* ── URL state ─────────────────────────────────────────── */

  function getUrlParams() {
    var p = new URLSearchParams(window.location.search);
    return {
      term:  p.get('kategorie') || '',
      page:  parseInt(p.get('page'), 10) || 1,
      order: p.get('order') || 'DESC',
    };
  }

  var state = getUrlParams();

  function pushUrl() {
    var p = new URLSearchParams();
    if (state.term)            p.set('kategorie', state.term);
    if (state.page > 1)        p.set('page', state.page);
    if (state.order !== 'DESC') p.set('order', state.order);
    var qs = p.toString();
    history.pushState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
  }

  function syncUI() {
    filtersEl.querySelectorAll('.xevos-filter-pill').forEach(function (b) {
      b.classList.toggle('is-active', (b.dataset.term || '') === state.term);
    });
    if (sortSelect) sortSelect.value = state.order;
  }

  /* ── Events ────────────────────────────────────────────── */

  /* Filter pills */
  filtersEl.addEventListener('click', function (e) {
    var btn = e.target.closest('.xevos-filter-pill');
    if (!btn) return;
    state.term = btn.dataset.term || '';
    state.page = 1;
    pushUrl();
    syncUI();
    fetchPosts(false);
  });

  /* Sort select */
  if (sortSelect) {
    sortSelect.addEventListener('change', function () {
      state.order = this.value;
      state.page = 1;
      pushUrl();
      fetchPosts(false);
    });
  }

  /* Load more */
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function () {
      if (state.page >= maxPages) return;
      state.page++;
      pushUrl();
      fetchPosts(true);
    });
  }

  /* Pagination */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.xevos-pagination__num');
    if (!btn) return;
    e.preventDefault();
    state.page = parseInt(btn.dataset.page, 10) || 1;
    pushUrl();
    fetchPosts(false);
    window.scrollTo({ top: gridEl.offsetTop - 120, behavior: 'smooth' });
  });

  /* Browser back / forward */
  window.addEventListener('popstate', function () {
    state = getUrlParams();
    syncUI();
    fetchPosts(false);
  });

  /* ── AJAX fetch ────────────────────────────────────────── */

  function fetchPosts(append) {
    if (!window.xevosAjax) return;
    if (gridWrap) gridWrap.classList.add('is-loading');

    var fd = new FormData();
    fd.append('action', 'xevos_filter_archive');
    fd.append('nonce',  xevosAjax.nonce);
    fd.append('post_type', 'aktualita');
    fd.append('taxonomy',  'kategorie-aktualit');
    fd.append('term',   state.term);
    fd.append('paged',  state.page);
    fd.append('order',  state.order);

    fetch(xevosAjax.ajaxUrl, { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data.success) {
          gridEl.innerHTML = '<p class="xevos-archive__empty">Nepodařilo se načíst příspěvky.</p>';
          return;
        }
        if (append) {
          gridEl.insertAdjacentHTML('beforeend', data.data.html);
        } else {
          gridEl.innerHTML = data.data.html;
        }
        maxPages = data.data.max_pages || 1;
        renderPagination();
        renderLoadMore();
      })
      .catch(function () {
        gridEl.innerHTML = '<p class="xevos-archive__empty">Chyba připojení. Zkuste to znovu.</p>';
      })
      .finally(function () {
        if (gridWrap) gridWrap.classList.remove('is-loading');
      });
  }

  /* ── UI renderers ──────────────────────────────────────── */

  function renderLoadMore() {
    if (!loadMoreBtn) return;
    loadMoreBtn.style.display = state.page >= maxPages ? 'none' : '';
  }

  function renderPagination() {
    var el = document.querySelector('.xevos-pagination');
    if (!el) return;
    var h = '';
    if (maxPages > 1) {
      if (state.page > 1)
        h += '<span class="xevos-pagination__num" data-page="' + (state.page - 1) + '">&lt;</span>';
      for (var i = 1; i <= maxPages; i++)
        h += '<span class="xevos-pagination__num' + (i === state.page ? ' is-active' : '') + '" data-page="' + i + '">' + i + '</span>';
      if (state.page < maxPages)
        h += '<span class="xevos-pagination__num" data-page="' + (state.page + 1) + '">&gt;</span>';
    }
    el.innerHTML = h;
  }

  /* ── Init — always fetch to get maxPages for pagination ── */

  syncUI();
  fetchPosts(false);

})();
