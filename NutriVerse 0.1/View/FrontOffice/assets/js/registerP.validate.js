/* ============================================================
   registerP.validate.js – Contrôle de saisie pour le profil
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
    if (group) group.appendChild(msg);
    else field.insertAdjacentElement('afterend', msg);
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

  function isValidPhone(phone) {
    // Accepts Tunisian / international formats: digits, spaces, +, -, ()
    return /^[\d\s\+\-\(\)]{7,20}$/.test(phone.trim());
  }

  /* ---------- Fields ---------- */
  const fields = {
    telephone:              document.getElementById('telephone'),
    date_naissance:         document.getElementById('date_naissance'),
    sexe:                   document.getElementById('sexe'),
    poids:                  document.getElementById('poids'),
    taille:                 document.getElementById('taille'),
    objectif_nutritionnel:  document.getElementById('objectif_nutritionnel'),
    preference_alimentaire: document.getElementById('preference_alimentaire'),
  };

  /* ---------- Real-time validation ---------- */
  if (fields.telephone) {
    fields.telephone.addEventListener('blur', function () {
      if (!this.value.trim()) showError('telephone', 'Le numéro de téléphone est obligatoire.');
      else if (!isValidPhone(this.value)) showError('telephone', 'Numéro de téléphone invalide.');
      else clearError('telephone');
    });
    fields.telephone.addEventListener('input', function () {
      if (isValidPhone(this.value)) clearError('telephone');
    });
  }

  if (fields.date_naissance) {
    fields.date_naissance.addEventListener('blur', function () {
      if (!this.value) {
        showError('date_naissance', 'La date de naissance est obligatoire.');
      } else {
        const today = new Date();
        const dob   = new Date(this.value);
        const age   = today.getFullYear() - dob.getFullYear();
        if (dob >= today) {
          showError('date_naissance', 'La date doit être dans le passé.');
        } else if (age < 10) {
          showError('date_naissance', 'Vous devez avoir au moins 10 ans.');
        } else if (age > 120) {
          showError('date_naissance', 'Date de naissance invalide.');
        } else {
          clearError('date_naissance');
        }
      }
    });
  }

  if (fields.sexe) {
    fields.sexe.addEventListener('change', function () {
      if (!this.value) showError('sexe', 'Veuillez sélectionner votre sexe.');
      else clearError('sexe');
    });
  }

  if (fields.poids) {
    fields.poids.addEventListener('blur', function () {
      const val = parseFloat(this.value);
      if (!this.value) showError('poids', 'Le poids est obligatoire.');
      else if (isNaN(val) || val < 20 || val > 400) showError('poids', 'Le poids doit être entre 20 et 400 kg.');
      else clearError('poids');
    });
  }

  if (fields.taille) {
    fields.taille.addEventListener('blur', function () {
      const val = parseFloat(this.value);
      if (!this.value) showError('taille', 'La taille est obligatoire.');
      else if (isNaN(val) || val < 50 || val > 300) showError('taille', 'La taille doit être entre 50 et 300 cm.');
      else clearError('taille');
    });
  }

  if (fields.objectif_nutritionnel) {
    fields.objectif_nutritionnel.addEventListener('change', function () {
      if (!this.value) showError('objectif_nutritionnel', 'Veuillez choisir un objectif.');
      else clearError('objectif_nutritionnel');
    });
  }

  if (fields.preference_alimentaire) {
    fields.preference_alimentaire.addEventListener('change', function () {
      if (!this.value) showError('preference_alimentaire', 'Veuillez choisir une préférence alimentaire.');
      else clearError('preference_alimentaire');
    });
  }

  /* ---------- Submit validation ---------- */
  const form = document.getElementById('form-profile');
  if (form) {
    form.addEventListener('submit', function (e) {
      let valid = true;

      const telephone = fields.telephone ? fields.telephone.value.trim() : '';
      const dob       = fields.date_naissance ? fields.date_naissance.value : '';
      const sexe      = fields.sexe ? fields.sexe.value : '';
      const poids     = fields.poids ? parseFloat(fields.poids.value) : null;
      const taille    = fields.taille ? parseFloat(fields.taille.value) : null;
      const objectif  = fields.objectif_nutritionnel ? fields.objectif_nutritionnel.value : '';
      const pref      = fields.preference_alimentaire ? fields.preference_alimentaire.value : '';

      // Téléphone
      if (!telephone) {
        showError('telephone', 'Le numéro de téléphone est obligatoire.'); valid = false;
      } else if (!isValidPhone(telephone)) {
        showError('telephone', 'Numéro de téléphone invalide.'); valid = false;
      } else { clearError('telephone'); }

      // Date de naissance
      if (!dob) {
        showError('date_naissance', 'La date de naissance est obligatoire.'); valid = false;
      } else {
        const today = new Date();
        const dobD  = new Date(dob);
        const age   = today.getFullYear() - dobD.getFullYear();
        if (dobD >= today) {
          showError('date_naissance', 'La date doit être dans le passé.'); valid = false;
        } else if (age < 10) {
          showError('date_naissance', 'Vous devez avoir au moins 10 ans.'); valid = false;
        } else if (age > 120) {
          showError('date_naissance', 'Date de naissance invalide.'); valid = false;
        } else { clearError('date_naissance'); }
      }

      // Sexe
      if (!sexe) {
        showError('sexe', 'Veuillez sélectionner votre sexe.'); valid = false;
      } else { clearError('sexe'); }

      // Poids
      if (!fields.poids || !fields.poids.value) {
        showError('poids', 'Le poids est obligatoire.'); valid = false;
      } else if (isNaN(poids) || poids < 20 || poids > 400) {
        showError('poids', 'Le poids doit être entre 20 et 400 kg.'); valid = false;
      } else { clearError('poids'); }

      // Taille
      if (!fields.taille || !fields.taille.value) {
        showError('taille', 'La taille est obligatoire.'); valid = false;
      } else if (isNaN(taille) || taille < 50 || taille > 300) {
        showError('taille', 'La taille doit être entre 50 et 300 cm.'); valid = false;
      } else { clearError('taille'); }

      // Objectif
      if (!objectif) {
        showError('objectif_nutritionnel', 'Veuillez choisir un objectif.'); valid = false;
      } else { clearError('objectif_nutritionnel'); }

      // Préférence alimentaire
      if (!pref) {
        showError('preference_alimentaire', 'Veuillez choisir une préférence alimentaire.'); valid = false;
      } else { clearError('preference_alimentaire'); }

      if (!valid) e.preventDefault();
    });
  }
})();
