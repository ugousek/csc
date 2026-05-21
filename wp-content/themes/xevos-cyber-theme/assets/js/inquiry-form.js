/**
 * Inquiry form (kybernetické testování) – AJAX handler.
 *
 * @package Xevos\CyberTheme
 */
(function () {
  'use strict';

  var form = document.getElementById('xevos-inquiry-form');
  if (!form) return;

  var btn = document.getElementById('xevos-inquiry-submit');
  var msg = document.getElementById('xevos-inquiry-message');
  var btnHtml = btn ? btn.innerHTML : '';

  function showError(text) {
    if (msg) {
      msg.textContent = text;
      msg.className = 'xevos-order-message xevos-order-message--error';
      msg.style.display = 'block';
    }
    btn.disabled = false;
    btn.innerHTML = btnHtml;
  }

  function sendForm() {
    var formData = new FormData(form);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', typeof xevosAjax !== 'undefined' ? xevosAjax.ajaxUrl : '/wp-admin/admin-ajax.php');

    xhr.onload = function () {
      var resp;
      try { resp = JSON.parse(xhr.responseText); } catch (ex) { resp = null; }

      if (xhr.status >= 200 && xhr.status < 300 && resp && resp.success) {
        if (msg) {
          msg.textContent = (resp.data && resp.data.message) || 'Poptávka byla odeslána.';
          msg.className = 'xevos-order-message xevos-order-message--success';
          msg.style.display = 'block';
        }
        form.reset();
        btn.disabled = false;
        btn.innerHTML = btnHtml;
      } else {
        showError((resp && resp.data && resp.data.message) || 'Nastala chyba. Zkuste to prosím znovu.');
      }
    };

    xhr.onerror = function () {
      showError('Chyba připojení. Zkuste to prosím znovu.');
    };

    xhr.send(formData);
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    btn.disabled = true;
    btn.textContent = 'Odesílám…';
    if (msg) {
      msg.style.display = 'none';
      msg.className = 'xevos-order-message';
    }

    /* Před odesláním si vždy vyžádáme čerstvý nonce — stránka může být
     * z CDN/cache se zastaralým tokenem. */
    if (typeof window.xevosGetFreshNonces === 'function') {
      window.xevosGetFreshNonces().then(sendForm, sendForm);
    } else {
      sendForm();
    }
  });
})();
