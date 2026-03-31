/**
 * Školení archive: filter, sort, pagination, load more.
 * URL-driven state — same pattern as archive-filter.js
 */

(function () {
  'use strict';

  var filtersEl = document.getElementById('skoleni-filters');
  var gridEl = document.getElementById('skoleni-grid');
  var loadMoreBtn = document.getElementById('skoleni-load-more');

  if (!filtersEl || !gridEl) return;

  var sortSelect = document.getElementById('skoleni-sort');
  var gridWrap = gridEl.closest('.xevos-grid-wrap');
  var maxPages = 1;

  /* ── URL state ─────────────────────────── */

  function getUrlParams() {
    var p = new URLSearchParams(window.location.search);
    return {
      term:  p.get('kategorie') || '',
      page:  parseInt(p.get('page'), 10) || 1,
      order: p.get('order') || 'date-DESC',
    };
  }

  var state = getUrlParams();

  function pushUrl() {
    var p = new URLSearchParams();
    if (state.term)                   p.set('kategorie', state.term);
    if (state.page > 1)               p.set('page', state.page);
    if (state.order !== 'date-DESC')  p.set('order', state.order);
    var qs = p.toString();
    history.pushState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
  }

  function syncUI() {
    filtersEl.querySelectorAll('.xevos-filter-pill').forEach(function (b) {
      b.classList.toggle('is-active', (b.dataset.term || '') === state.term);
    });
    if (sortSelect) sortSelect.value = state.order;
  }

  /* ── Events ────────────────────────────── */

  filtersEl.addEventListener('click', function (e) {
    var btn = e.target.closest('.xevos-filter-pill');
    if (!btn) return;
    state.term = btn.dataset.term || '';
    state.page = 1;
    pushUrl();
    syncUI();
    fetchPosts(false);
  });

  if (sortSelect) {
    sortSelect.addEventListener('change', function () {
      state.order = this.value;
      state.page = 1;
      pushUrl();
      fetchPosts(false);
    });
  }

  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function () {
      if (state.page >= maxPages) return;
      state.page++;
      pushUrl();
      fetchPosts(true);
    });
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('#skoleni-pagination .xevos-pagination__num');
    if (!btn) return;
    e.preventDefault();
    state.page = parseInt(btn.dataset.page, 10) || 1;
    pushUrl();
    fetchPosts(false);
    window.scrollTo({ top: gridEl.offsetTop - 120, behavior: 'smooth' });
  });

  window.addEventListener('popstate', function () {
    state = getUrlParams();
    syncUI();
    fetchPosts(false);
  });

  /* ── AJAX ──────────────────────────────── */

  function fetchPosts(append) {
    if (!window.xevosAjax) return;
    if (gridWrap) gridWrap.classList.add('is-loading');

    /* Parse order string: "date-DESC" → orderby=date, order=DESC */
    var parts = state.order.split('-');
    var orderby = parts[0] || 'date';
    var order = parts[1] || 'DESC';

    var fd = new FormData();
    fd.append('action', 'xevos_filter_archive');
    fd.append('nonce',  xevosAjax.nonce);
    fd.append('post_type', 'skoleni');
    fd.append('taxonomy',  'kategorie-skoleni');
    fd.append('term',   state.term);
    fd.append('paged',  state.page);
    fd.append('order',  order);
    fd.append('card_variant', 'list');

    fetch(xevosAjax.ajaxUrl, { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data.success) return;
        if (append) {
          gridEl.insertAdjacentHTML('beforeend', data.data.html);
        } else {
          gridEl.innerHTML = data.data.html;
        }
        maxPages = data.data.max_pages || 1;
        renderPagination();
        renderLoadMore();
      })
      .finally(function () {
        if (gridWrap) gridWrap.classList.remove('is-loading');
      });
  }

  function renderLoadMore() {
    if (!loadMoreBtn) return;
    loadMoreBtn.style.display = state.page >= maxPages ? 'none' : '';
  }

  function renderPagination() {
    var el = document.getElementById('skoleni-pagination');
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

  /* ── Init ──────────────────────────────── */

  syncUI();
  fetchPosts(false);

})();
