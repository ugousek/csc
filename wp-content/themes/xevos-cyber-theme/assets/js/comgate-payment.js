/**
 * Paid order handler – ComGate nebo faktura.
 * Načítá se pouze na single-skoleni s typ_prihlaseni !== zdarma/pozvanka.
 *
 * Routing: form.dataset.paymentMethod ('online' | 'invoice')
 * Nastavuje se kliknutím na label (.xevos-payment-method__option) nebo
 * change eventem na radio inputu. Nikdy se nespoléhá na CSS :checked.
 *
 * @package Xevos\CyberTheme
 */
(function () {
  'use strict';

  var form = document.getElementById('xevos-order-form');
  if (!form || form.dataset.free === '1') return;
  if (form.dataset.typ !== 'platba') return;

  var btn     = document.getElementById('xevos-order-submit');
  var msg     = document.getElementById('xevos-order-message');
  var btnHtml = btn ? btn.innerHTML : '';

  // Výchozí platební metoda.
  form.dataset.paymentMethod = 'online';

  // ── Sledování výběru metody ──────────────────────────────────────────────
  // Primárně: klik na label (nejspolehlivější pro stylované radio buttony).
  var options = form.querySelectorAll('.xevos-payment-method__option');
  Array.prototype.forEach.call(options, function (option) {
    option.addEventListener('click', function () {
      var radio = option.querySelector('input[type="radio"]');
      if (radio) {
        form.dataset.paymentMethod = radio.value;
      }
    });
  });

  // Záloha: nativní change event na radio inputech.
  var radios = form.querySelectorAll('input[name="payment_method"]');
  Array.prototype.forEach.call(radios, function (radio) {
    radio.addEventListener('change', function () {
      form.dataset.paymentMethod = this.value;
    });
  });

  // ── Submit ───────────────────────────────────────────────────────────────
  function showError(text) {
    if (msg) {
      msg.textContent   = text;
      msg.className     = 'xevos-order-message xevos-order-message--error';
      msg.style.display = 'block';
    }
    btn.disabled  = false;
    btn.innerHTML = btnHtml;
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    var method   = form.dataset.paymentMethod || 'online';
    var formData = new FormData(form);
    var ajaxUrl  = typeof xevosAjax !== 'undefined' ? xevosAjax.ajaxUrl : '/wp-admin/admin-ajax.php';
    var nonce    = typeof xevosAjax !== 'undefined' ? xevosAjax.nonce  : '';

    formData.set('action', method === 'invoice' ? 'xevos_create_invoice_order' : 'xevos_create_payment');
    formData.set('nonce', nonce);

    btn.disabled    = true;
    btn.textContent = 'Zpracovávám objednávku…';
    if (msg) {
      msg.style.display = 'none';
      msg.className     = 'xevos-order-message';
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', ajaxUrl);

    xhr.onload = function () {
      var resp;
      try { resp = JSON.parse(xhr.responseText); } catch (ex) { resp = null; }

      if (xhr.status >= 200 && xhr.status < 300 && resp && resp.success) {

        if (method === 'invoice') {
          // Faktura – zobrazit potvrzení, žádný redirect.
          if (msg) {
            msg.innerHTML     = (resp.data && resp.data.message) ? resp.data.message : 'Objednávka přijata. Zašleme vám pokyny k platbě.';
            msg.className     = 'xevos-order-message xevos-order-message--success';
            msg.style.display = 'block';
          }
          form.reset();
          form.dataset.paymentMethod = 'online';
          btn.disabled  = false;
          btn.innerHTML = btnHtml;

        } else {
          // ComGate – redirect na platební bránu.
          if (resp.data && resp.data.redirect_url) {
            if (msg) {
              msg.textContent   = 'Přesměrování na platební bránu…';
              msg.className     = 'xevos-order-message xevos-order-message--success';
              msg.style.display = 'block';
            }
            window.location.href = resp.data.redirect_url;
          } else {
            showError('Nepodařilo se získat platební odkaz.');
          }
        }

      } else {
        showError((resp && resp.data && resp.data.message) || 'Nastala chyba. Zkuste to prosím znovu.');
      }
    };

    xhr.onerror = function () {
      showError('Chyba připojení. Zkuste to prosím znovu.');
    };

    xhr.send(formData);
  });
})();
