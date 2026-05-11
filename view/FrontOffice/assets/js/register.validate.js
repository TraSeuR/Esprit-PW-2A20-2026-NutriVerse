/* ============================================================
   register.validate.js – Contrôle de saisie pour l'inscription
   ============================================================ */

(function () {
  'use strict';

  /* ---------- Helpers ---------- */
  function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    field.classList.remove('input-valid');
    field.classList.add('input-error');

    const group = field.closest('.form-group');
    const existing = group ? group.querySelector('.error-msg') : null;
    if (existing) existing.remove();

    const msg = document.createElement('span');
    msg.className = 'error-msg';
    msg.textContent = message;
    if (group) {
      group.appendChild(msg);
    } else {
      field.insertAdjacentElement('afterend', msg);
    }
  }

  function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    field.classList.remove('input-error');
    field.classList.add('input-valid');
    const group = field.closest('.form-group');
    const msg   = group ? group.querySelector('.error-msg') : null;
    if (msg) msg.remove();
  }

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
  }

  /* ---------- Fields ---------- */
  const fields = {
    prenom:             document.getElementById('prenom'),
    nom:                document.getElementById('nom'),
    email:              document.getElementById('email'),
    mot_de_passe:       document.getElementById('mot_de_passe'),
    confirm_mot_de_passe: document.getElementById('confirm_mot_de_passe'),
    terms:              document.getElementById('terms'),
  };

  /* ---------- Real-time validation ---------- */
  if (fields.prenom) {
    fields.prenom.addEventListener('blur', function () {
      if (!this.value.trim()) showError('prenom', 'Le prénom est obligatoire.');
      else if (this.value.trim().length < 2) showError('prenom', 'Le prénom doit comporter au moins 2 caractères.');
      else clearError('prenom');
    });
  }

  if (fields.nom) {
    fields.nom.addEventListener('blur', function () {
      if (!this.value.trim()) showError('nom', 'Le nom est obligatoire.');
      else if (this.value.trim().length < 2) showError('nom', 'Le nom doit comporter au moins 2 caractères.');
      else clearError('nom');
    });
  }

  if (fields.email) {
    fields.email.addEventListener('blur', function () {
      if (!this.value.trim()) showError('email', 'L\'adresse e-mail est obligatoire.');
      else if (!isValidEmail(this.value)) showError('email', 'Adresse e-mail invalide.');
      else clearError('email');
    });
    fields.email.addEventListener('input', function () {
      if (isValidEmail(this.value)) clearError('email');
    });
  }

  if (fields.mot_de_passe) {
    fields.mot_de_passe.addEventListener('blur', function () {
      if (!this.value) showError('mot_de_passe', 'Le mot de passe est obligatoire.');
      else if (this.value.length < 8) showError('mot_de_passe', 'Le mot de passe doit contenir au moins 8 caractères.');
      else clearError('mot_de_passe');
    });
  }

  if (fields.confirm_mot_de_passe) {
    fields.confirm_mot_de_passe.addEventListener('blur', function () {
      const pw = fields.mot_de_passe ? fields.mot_de_passe.value : '';
      if (!this.value) showError('confirm_mot_de_passe', 'La confirmation est obligatoire.');
      else if (this.value !== pw) showError('confirm_mot_de_passe', 'Les mots de passe ne correspondent pas.');
      else clearError('confirm_mot_de_passe');
    });
  }

  /* ---------- Submit validation ---------- */
  const form = document.getElementById('form-register');
  if (form) {
    form.addEventListener('submit', function (e) {
      let valid = true;

      const prenom   = fields.prenom   ? fields.prenom.value.trim()   : '';
      const nom      = fields.nom      ? fields.nom.value.trim()      : '';
      const email    = fields.email    ? fields.email.value.trim()    : '';
      const pw       = fields.mot_de_passe ? fields.mot_de_passe.value : '';
      const pwConf   = fields.confirm_mot_de_passe ? fields.confirm_mot_de_passe.value : '';
      const termsOk  = fields.terms ? fields.terms.checked : false;

      if (!prenom) {
        showError('prenom', 'Le prénom est obligatoire.'); valid = false;
      } else if (prenom.length < 2) {
        showError('prenom', 'Le prénom doit comporter au moins 2 caractères.'); valid = false;
      } else { clearError('prenom'); }

      if (!nom) {
        showError('nom', 'Le nom est obligatoire.'); valid = false;
      } else if (nom.length < 2) {
        showError('nom', 'Le nom doit comporter au moins 2 caractères.'); valid = false;
      } else { clearError('nom'); }

      if (!email) {
        showError('email', 'L\'adresse e-mail est obligatoire.'); valid = false;
      } else if (!isValidEmail(email)) {
        showError('email', 'Adresse e-mail invalide.'); valid = false;
      } else { clearError('email'); }

      if (!pw) {
        showError('mot_de_passe', 'Le mot de passe est obligatoire.'); valid = false;
      } else if (pw.length < 8) {
        showError('mot_de_passe', 'Minimum 8 caractères requis.'); valid = false;
      } else { clearError('mot_de_passe'); }

      if (!pwConf) {
        showError('confirm_mot_de_passe', 'La confirmation est obligatoire.'); valid = false;
      } else if (pwConf !== pw) {
        showError('confirm_mot_de_passe', 'Les mots de passe ne correspondent pas.'); valid = false;
      } else { clearError('confirm_mot_de_passe'); }

      if (!termsOk) {
        showError('terms', 'Vous devez accepter les conditions d\'utilisation.'); valid = false;
      } else { clearError('terms'); }

      if (!valid) e.preventDefault();
    });
  }


})();
