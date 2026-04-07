/**
 * Comgate paid order – AJAX form handler.
 * Loaded only on single-skoleni with typ_prihlaseni === 'platba'.
 *
 * @package Xevos\CyberTheme
 */
(function () {
  'use strict';

  var form = document.getElementById('xevos-order-form');
  if (!form || form.dataset.free === '1') return;
  if (form.dataset.typ !== 'platba') return;

  var btn = document.getElementById('xevos-order-submit');
  var msg = document.getElementById('xevos-order-message');
  var btnHtml = btn ? btn.innerHTML : '';

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    var formData = new FormData(form);
    formData.set('action', 'xevos_create_payment');
    formData.set('nonce', typeof xevosAjax !== 'undefined' ? xevosAjax.nonce : '');

    btn.disabled = true;
    btn.textContent = 'Zpracovávám objednávku…';
    if (msg) {
      msg.style.display = 'none';
      msg.className = 'xevos-order-message';
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', typeof xevosAjax !== 'undefined' ? xevosAjax.ajaxUrl : '/wp-admin/admin-ajax.php');

    xhr.onload = function () {
      var resp;
      try { resp = JSON.parse(xhr.responseText); } catch (ex) { resp = null; }

      if (xhr.status >= 200 && xhr.status < 300 && resp && resp.success && resp.data && resp.data.redirect_url) {
        if (msg) {
          msg.textContent = 'Přesměrování na platební bránu…';
          msg.className = 'xevos-order-message xevos-order-message--success';
          msg.style.display = 'block';
        }
        window.location.href = resp.data.redirect_url;
      } else {
        if (msg) {
          msg.textContent = (resp && resp.data && resp.data.message) || 'Nastala chyba. Zkuste to prosím znovu.';
          msg.className = 'xevos-order-message xevos-order-message--error';
          msg.style.display = 'block';
        }
        btn.disabled = false;
        btn.innerHTML = btnHtml;
      }
    };

    xhr.onerror = function () {
      if (msg) {
        msg.textContent = 'Chyba připojení. Zkuste to prosím znovu.';
        msg.className = 'xevos-order-message xevos-order-message--error';
        msg.style.display = 'block';
      }
      btn.disabled = false;
      btn.innerHTML = btnHtml;
    };

    xhr.send(formData);
  });
})();
