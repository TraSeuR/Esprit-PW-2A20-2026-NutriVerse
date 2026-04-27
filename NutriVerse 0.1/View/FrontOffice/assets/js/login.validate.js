/* ============================================================
   login.validate.js – Contrôle de saisie pour la page connexion
   ============================================================ */

(function () {
  'use strict';

  /* ---------- Helpers ---------- */
  function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    field.classList.add('input-error');

    // Remove any existing error message
    const existing = field.parentElement.querySelector('.error-msg');
    if (existing) existing.remove();

    const msg = document.createElement('span');
    msg.className = 'error-msg';
    msg.textContent = message;
    field.parentElement.insertAdjacentElement('afterend', msg);
  }

  function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    field.classList.remove('input-error');
    field.classList.add('input-valid');
    const msg = field.parentElement.querySelector('.error-msg') ||
                field.closest('.form-group')?.querySelector('.error-msg');
    if (msg) msg.remove();
  }

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
  }

  /* ---------- Real-time feedback ---------- */
  const emailField = document.getElementById('email');
  const pwField    = document.getElementById('mot_de_passe');

  if (emailField) {
    emailField.addEventListener('blur', function () {
      if (!this.value.trim()) {
        showError('email', 'L\'adresse e-mail est obligatoire.');
      } else if (!isValidEmail(this.value)) {
        showError('email', 'Veuillez saisir une adresse e-mail valide.');
      } else {
        clearError('email');
      }
    });
    emailField.addEventListener('input', function () {
      if (isValidEmail(this.value)) clearError('email');
    });
  }

  if (pwField) {
    pwField.addEventListener('blur', function () {
      if (!this.value) {
        showError('mot_de_passe', 'Le mot de passe est obligatoire.');
      } else {
        clearError('mot_de_passe');
      }
    });
    pwField.addEventListener('input', function () {
      if (this.value) clearError('mot_de_passe');
    });
  }

  /* ---------- Submit validation ---------- */
  const form = document.getElementById('form-login');
  if (form) {
    form.addEventListener('submit', function (e) {
      let valid = true;

      const email = emailField ? emailField.value.trim() : '';
      const pw    = pwField    ? pwField.value           : '';

      if (!email) {
        showError('email', 'L\'adresse e-mail est obligatoire.');
        valid = false;
      } else if (!isValidEmail(email)) {
        showError('email', 'Veuillez saisir une adresse e-mail valide.');
        valid = false;
      } else {
        clearError('email');
      }

      if (!pw) {
        showError('mot_de_passe', 'Le mot de passe est obligatoire.');
        valid = false;
      } else {
        clearError('mot_de_passe');
      }

      if (!valid) e.preventDefault();
    });
  }

  /* ---------- Password toggle ---------- */
  (function () {
    const btn   = document.getElementById('toggle-pw-btn');
    const field = document.getElementById('mot_de_passe');
    if (btn && field) {
      btn.addEventListener('click', function () {
        const hidden = field.type === 'password';
        field.type   = hidden ? 'text' : 'password';
        btn.textContent = hidden ? '🙈' : '👁️';
      });
    }
  })();
})();
