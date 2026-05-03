/* ============================================================
   back.validate.js – Contrôle de saisie pour back.php
   Valide :
     • modal-add  → formulaire "Ajouter un utilisateur"  (action: ajoute.php)
     • modal-edit-X → formulaires "Modifier"             (action: update.php)
   ============================================================ */

(function () {
  'use strict';

  /* ──────────────────────────────────────────────────────────
     HELPERS
  ────────────────────────────────────────────────────────── */

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
  }

  function isValidPhone(phone) {
    // digits, spaces, +, -, () — min 7 chars
    return /^[\d\s\+\-\(\)]{7,20}$/.test(phone.trim());
  }

  /**
   * Show an error message below a field.
   * @param {HTMLElement} field  – the input/select element
   * @param {string}      msg   – error text
   */
  function showError(field, msg) {
    if (!field) return;
    field.classList.remove('input-valid');
    field.classList.add('input-error');

    // Remove any previous error for this field
    const group = field.closest('.u-form-group') || field.parentElement;
    const existing = group.querySelector('.back-error-msg');
    if (existing) existing.remove();

    const span = document.createElement('span');
    span.className = 'back-error-msg';
    span.textContent = msg;
    group.appendChild(span);
  }

  /**
   * Clear the error state from a field.
   * @param {HTMLElement} field
   */
  function clearError(field) {
    if (!field) return;
    field.classList.remove('input-error');
    field.classList.add('input-valid');
    const group = field.closest('.u-form-group') || field.parentElement;
    const existing = group.querySelector('.back-error-msg');
    if (existing) existing.remove();
  }

  /* ──────────────────────────────────────────────────────────
     VALIDATE FORM  (shared logic for both Add & Edit forms)
     Returns true if valid, false otherwise.
  ────────────────────────────────────────────────────────── */

  /**
   * @param {HTMLFormElement} form
   * @param {boolean} requirePassword  – true for Add, false for Edit (password optional)
   */
  function validateUserForm(form, requirePassword) {
    if (!form) return true;
    let valid = true;

    /* ── Identité ── */
    const prenom = form.querySelector('[name="prenom"]');
    const nom    = form.querySelector('[name="nom"]');
    const email  = form.querySelector('[name="email"]');
    const pw     = form.querySelector('[name="mot_de_passe"]');

    if (prenom !== null) {
      if (!prenom.value.trim()) {
        showError(prenom, 'Le prénom est obligatoire.'); valid = false;
      } else if (prenom.value.trim().length < 2) {
        showError(prenom, 'Le prénom doit comporter au moins 2 caractères.'); valid = false;
      } else { clearError(prenom); }
    }

    if (nom !== null) {
      if (!nom.value.trim()) {
        showError(nom, 'Le nom est obligatoire.'); valid = false;
      } else if (nom.value.trim().length < 2) {
        showError(nom, 'Le nom doit comporter au moins 2 caractères.'); valid = false;
      } else { clearError(nom); }
    }

    if (email !== null) {
      if (!email.value.trim()) {
        showError(email, 'L\'adresse e-mail est obligatoire.'); valid = false;
      } else if (!isValidEmail(email.value)) {
        showError(email, 'Adresse e-mail invalide.'); valid = false;
      } else { clearError(email); }
    }

    if (pw !== null) {
      if (requirePassword && !pw.value) {
        showError(pw, 'Le mot de passe est obligatoire.'); valid = false;
      } else if (pw.value && pw.value.length < 8) {
        showError(pw, 'Le mot de passe doit contenir au moins 8 caractères.'); valid = false;
      } else { clearError(pw); }
    }

    /* ── Informations personnelles (optional fields – validate only if filled) ── */
    const tel     = form.querySelector('[name="telephone"]');
    const dob     = form.querySelector('[name="date_naissance"]');
    const poids   = form.querySelector('[name="poids"]');
    const taille  = form.querySelector('[name="taille"]');

    if (tel !== null && tel.value.trim() && !isValidPhone(tel.value)) {
      showError(tel, 'Numéro de téléphone invalide.'); valid = false;
    } else if (tel !== null && tel.value.trim()) { clearError(tel); }

    if (dob !== null && dob.value) {
      const today = new Date();
      const dobD  = new Date(dob.value);
      const age   = today.getFullYear() - dobD.getFullYear();
      if (dobD >= today) {
        showError(dob, 'La date doit être dans le passé.'); valid = false;
      } else if (age < 10 || age > 120) {
        showError(dob, 'Date de naissance invalide.'); valid = false;
      } else { clearError(dob); }
    }

    if (poids !== null && poids.value) {
      const v = parseFloat(poids.value);
      if (isNaN(v) || v < 20 || v > 400) {
        showError(poids, 'Le poids doit être entre 20 et 400 kg.'); valid = false;
      } else { clearError(poids); }
    }

    if (taille !== null && taille.value) {
      const v = parseFloat(taille.value);
      if (isNaN(v) || v < 50 || v > 300) {
        showError(taille, 'La taille doit être entre 50 et 300 cm.'); valid = false;
      } else { clearError(taille); }
    }

    return valid;
  }

  /* ──────────────────────────────────────────────────────────
     REAL-TIME FEEDBACK (attach to a form's inputs)
  ────────────────────────────────────────────────────────── */

  function attachRealtimeFeedback(form) {
    if (!form) return;

    form.querySelectorAll('.u-form-input').forEach(function (field) {
      const name = field.getAttribute('name') || '';

      field.addEventListener('blur', function () {
        const val = field.value.trim();

        if (name === 'prenom' || name === 'nom') {
          if (!val) showError(field, 'Ce champ est obligatoire.');
          else if (val.length < 2) showError(field, 'Minimum 2 caractères.');
          else clearError(field);

        } else if (name === 'email') {
          if (!val) showError(field, 'L\'adresse e-mail est obligatoire.');
          else if (!isValidEmail(val)) showError(field, 'Adresse e-mail invalide.');
          else clearError(field);

        } else if (name === 'mot_de_passe') {
          if (val && val.length < 8) showError(field, 'Minimum 8 caractères.');
          else clearError(field);

        } else if (name === 'telephone') {
          if (val && !isValidPhone(val)) showError(field, 'Numéro invalide.');
          else clearError(field);

        } else if (name === 'poids') {
          if (val) {
            const v = parseFloat(val);
            if (isNaN(v) || v < 20 || v > 400) showError(field, 'Poids entre 20 et 400 kg.');
            else clearError(field);
          }

        } else if (name === 'taille') {
          if (val) {
            const v = parseFloat(val);
            if (isNaN(v) || v < 50 || v > 300) showError(field, 'Taille entre 50 et 300 cm.');
            else clearError(field);
          }
        }
      });

      // Clear error while typing (for text/email fields)
      if (['text', 'email', 'password', 'tel', 'number'].includes(field.type)) {
        field.addEventListener('input', function () {
          if (field.classList.contains('input-error')) {
            clearError(field);
          }
        });
      }
    });
  }

  /* ──────────────────────────────────────────────────────────
     WIRE UP: MODAL "AJOUTER" (modal-add)
  ────────────────────────────────────────────────────────── */

  const addForm = document.querySelector('#modal-add form[action="ajoute.php"]');
  if (addForm) {
    attachRealtimeFeedback(addForm);

    addForm.addEventListener('submit', function (e) {
      const valid = validateUserForm(addForm, true /* password required */);
      if (!valid) {
        e.preventDefault();
        // Scroll to first error inside the modal
        const firstError = addForm.querySelector('.input-error');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
  }

  /* ──────────────────────────────────────────────────────────
     WIRE UP: MODALS "MODIFIER" (modal-edit-X)
     There can be many edit modals (one per user), so we
     query all forms pointing to update.php.
  ────────────────────────────────────────────────────────── */

  document.querySelectorAll('form[action="update.php"]').forEach(function (editForm) {
    attachRealtimeFeedback(editForm);

    editForm.addEventListener('submit', function (e) {
      const valid = validateUserForm(editForm, false /* password optional on edit */);
      if (!valid) {
        e.preventDefault();
        const firstError = editForm.querySelector('.input-error');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
  });

  /* ──────────────────────────────────────────────────────────
     INJECT VALIDATION STYLES (error / valid)
     Appended once to <head> so no external CSS dependency.
  ────────────────────────────────────────────────────────── */

  (function injectStyles() {
    if (document.getElementById('back-validate-styles')) return;
    const style = document.createElement('style');
    style.id = 'back-validate-styles';
    style.textContent = `
      /* ── Back-office form validation styles ── */
      .u-form-input.input-error {
        border-color: #e53e3e !important;
        box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.15) !important;
        background-color: #fff5f5 !important;
      }
      .u-form-input.input-valid {
        border-color: #38a169 !important;
        box-shadow: 0 0 0 3px rgba(56, 161, 105, 0.15) !important;
      }
      .back-error-msg {
        display: block;
        color: #e53e3e;
        font-size: 0.78rem;
        margin-top: 4px;
        font-weight: 500;
        animation: backErrFade 0.2s ease;
      }
      @keyframes backErrFade {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
      }
    `;
    document.head.appendChild(style);
  })();

})();
