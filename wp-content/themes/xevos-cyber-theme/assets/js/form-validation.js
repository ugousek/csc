/**
 * XEVOS Form Validation System v1.0
 *
 * Universal, reusable form validation library.
 * Works with any HTML form – just add data-validate to the <form> element
 * or call XevosValidation.init(formElement).
 *
 * Features:
 * - Real-time validation (blur + input after first touch)
 * - Built-in rules: required, email, tel, minlength, maxlength, pattern, match
 * - Custom rules via data-validate-* attributes
 * - Custom error messages via data-error-* attributes
 * - Accessible (aria-invalid, role="alert")
 * - Auto-discovers forms with [data-validate] or known xevos IDs
 * - No dependencies
 *
 * Usage (HTML):
 *   <form data-validate>
 *     <div class="xevos-form__group">
 *       <input type="email" name="email" required data-error-required="Vyplňte e-mail">
 *     </div>
 *   </form>
 *
 * Usage (JS):
 *   XevosValidation.init(document.getElementById('my-form'));
 *   XevosValidation.validate(document.getElementById('my-form')); // returns bool
 *
 * @package Xevos
 */
var XevosValidation = (function () {
  'use strict';

  // ---- Config ----
  var CSS = {
    error:    'xevos-form__input--error',
    errorMsg: 'xevos-form__error',
    group:    'xevos-form__group'
  };

  var defaultMessages = {
    required:  'Toto pole je povinné.',
    email:     'Zadejte platný e-mail.',
    tel:       'Zadejte platné telefonní číslo.',
    url:       'Zadejte platnou URL adresu.',
    number:    'Zadejte číslo.',
    minlength: 'Minimální počet znaků: {0}.',
    maxlength: 'Maximální počet znaků: {0}.',
    pattern:   'Neplatný formát.',
    match:     'Pole se neshodují.',
    select:    'Vyberte jednu z možností.',
    checkbox:  'Musíte souhlasit.',
    ico:       'IČO musí mít 8 číslic.',
    psc:       'PSČ musí mít 5 číslic.'
  };

  var patterns = {
    email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    tel:   /^[+]?[\d\s()-]{6,}$/,
    url:   /^https?:\/\/.+/,
    ico:   /^\d{8}$/,
    psc:   /^\d{3}\s?\d{2}$/
  };

  // ---- Helpers ----

  function getFieldSelector() {
    return 'input:not([type="hidden"]):not([type="submit"]):not([type="button"]):not([tabindex="-1"]), select, textarea';
  }

  function getErrorEl(field) {
    var group = field.closest('.' + CSS.group);
    if (!group) return null;
    var el = group.querySelector('.' + CSS.errorMsg);
    if (!el) {
      el = document.createElement('span');
      el.className = CSS.errorMsg;
      el.setAttribute('role', 'alert');
      el.setAttribute('aria-live', 'polite');
      group.appendChild(el);
    }
    return el;
  }

  function getMessage(field, rule, fallback) {
    // Check data-error-{rule} attribute first.
    var custom = field.getAttribute('data-error-' + rule);
    if (custom) return custom;
    // Check data-error (generic override).
    custom = field.getAttribute('data-error');
    if (custom && rule === 'required') return custom;
    return fallback || defaultMessages[rule] || 'Neplatná hodnota.';
  }

  function clearError(field) {
    field.classList.remove(CSS.error);
    field.removeAttribute('aria-invalid');
    var el = getErrorEl(field);
    if (el) {
      el.textContent = '';
      el.style.display = 'none';
    }
  }

  function showError(field, msg) {
    field.classList.add(CSS.error);
    field.setAttribute('aria-invalid', 'true');
    var el = getErrorEl(field);
    if (el) {
      el.textContent = msg;
      el.style.display = 'block';
    }
  }

  // ---- Validation rules ----

  function validateField(field, form) {
    var value = (field.value || '').trim();
    var type = field.type || '';
    var tagName = field.tagName.toLowerCase();

    // Checkbox required.
    if (type === 'checkbox') {
      if (field.required && !field.checked) {
        showError(field, getMessage(field, 'checkbox'));
        return false;
      }
      clearError(field);
      return true;
    }

    // Required.
    if (field.required || field.hasAttribute('required')) {
      if (!value) {
        var rule = (tagName === 'select' || type === 'select-one') ? 'select' : 'required';
        showError(field, getMessage(field, rule));
        return false;
      }
    }

    // Skip further if empty and optional.
    if (!value) {
      clearError(field);
      return true;
    }

    // Type-based validation.
    if (type === 'email' && !patterns.email.test(value)) {
      showError(field, getMessage(field, 'email'));
      return false;
    }

    if (type === 'tel' && !patterns.tel.test(value)) {
      showError(field, getMessage(field, 'tel'));
      return false;
    }

    if (type === 'url' && !patterns.url.test(value)) {
      showError(field, getMessage(field, 'url'));
      return false;
    }

    if (type === 'number' && isNaN(Number(value))) {
      showError(field, getMessage(field, 'number'));
      return false;
    }

    // Custom data-validate-* rules.
    var customType = field.getAttribute('data-validate');
    if (customType && patterns[customType] && !patterns[customType].test(value)) {
      showError(field, getMessage(field, customType));
      return false;
    }

    // Minlength.
    var minLen = field.getAttribute('minlength');
    if (minLen && value.length < parseInt(minLen, 10)) {
      showError(field, getMessage(field, 'minlength', defaultMessages.minlength.replace('{0}', minLen)));
      return false;
    }

    // Maxlength (HTML enforces this, but show error for copy-paste).
    var maxLen = field.getAttribute('maxlength');
    if (maxLen && value.length > parseInt(maxLen, 10)) {
      showError(field, getMessage(field, 'maxlength', defaultMessages.maxlength.replace('{0}', maxLen)));
      return false;
    }

    // Pattern attribute.
    var pat = field.getAttribute('pattern');
    if (pat) {
      var re = new RegExp('^' + pat + '$');
      if (!re.test(value)) {
        showError(field, getMessage(field, 'pattern'));
        return false;
      }
    }

    // Match (data-match="#other-field-id").
    var matchId = field.getAttribute('data-match');
    if (matchId) {
      var matchField = form ? form.querySelector(matchId) : document.querySelector(matchId);
      if (matchField && value !== matchField.value.trim()) {
        showError(field, getMessage(field, 'match'));
        return false;
      }
    }

    clearError(field);
    return true;
  }

  // ---- Form-level ----

  function validateForm(form) {
    var fields = form.querySelectorAll(getFieldSelector());
    var valid = true;
    var firstError = null;

    for (var i = 0; i < fields.length; i++) {
      if (!validateField(fields[i], form)) {
        valid = false;
        if (!firstError) firstError = fields[i];
      }
    }

    if (firstError) {
      firstError.focus();
      // Scroll into view if needed.
      if (firstError.scrollIntoView) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }

    return valid;
  }

  // ---- Init ----

  function initForm(form) {
    if (form._xevosValidation) return; // Already initialized.
    form._xevosValidation = true;

    var fields = form.querySelectorAll(getFieldSelector());

    for (var i = 0; i < fields.length; i++) {
      (function (field) {
        var touched = false;

        field.addEventListener('blur', function () {
          touched = true;
          validateField(field, form);
        });

        field.addEventListener('input', function () {
          if (touched) validateField(field, form);
        });

        field.addEventListener('change', function () {
          touched = true;
          validateField(field, form);
        });
      })(fields[i]);
    }

    // Intercept submit in capture phase (before AJAX handlers).
    form.addEventListener('submit', function (e) {
      if (!validateForm(form)) {
        e.preventDefault();
        e.stopImmediatePropagation();
      }
    }, true);
  }

  // ---- Auto-init ----

  function autoInit() {
    // 1. Forms with [data-validate] attribute.
    var dataForms = document.querySelectorAll('form[data-validate]');
    for (var i = 0; i < dataForms.length; i++) {
      initForm(dataForms[i]);
    }

    // 2. Known xevos form IDs.
    var knownIds = [
      'xevos-order-form',
      'xevos-contact-form',
      'xevos-inquiry-form'
    ];

    for (var j = 0; j < knownIds.length; j++) {
      var form = document.getElementById(knownIds[j]);
      if (form) initForm(form);
    }
  }

  // Run on DOMContentLoaded or immediately if already loaded.
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', autoInit);
  } else {
    autoInit();
  }

  // ---- Public API ----
  return {
    init: initForm,
    validate: validateForm,
    validateField: validateField,
    clearError: clearError,
    showError: showError,
    addPattern: function (name, regex, message) {
      patterns[name] = regex;
      if (message) defaultMessages[name] = message;
    },
    setMessage: function (rule, message) {
      defaultMessages[rule] = message;
    }
  };
})();
