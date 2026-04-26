/**
 * NutriVerse BackOffice - Validation & Utilitaires
 * Contrôles de saisie pour les formulaires BackOffice (Admin)
 */

document.addEventListener('DOMContentLoaded', function () {

    // ========================================
    // AUTO-CALCUL DES CALORIES
    // ========================================
    const protInput = document.getElementById('proteine');
    const glucInput = document.getElementById('glucide');
    const lipiInput = document.getElementById('lipides');
    const caloInput = document.getElementById('calorie_jour');

    if (protInput && glucInput && lipiInput && caloInput) {
        const calculateCalories = () => {
            const p = parseFloat(protInput.value) || 0;
            const g = parseFloat(glucInput.value) || 0;
            const l = parseFloat(lipiInput.value) || 0;
            const total = (p * 4) + (g * 4) + (l * 9);
            caloInput.value = total > 0 ? Math.round(total) : caloInput.value;
        };

        [protInput, glucInput, lipiInput].forEach(input => {
            input.addEventListener('input', (e) => {
                // FORCE ONLY NUMBERS
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                calculateCalories();
            });
        });
    }

    // ========================================
    // ASSISTANT PLANNING - SMART TEXTAREA
    // ========================================
    const days = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
    const textarea = document.getElementById('programme_sport');

    if (textarea) {
        // Fonction globale pour insertion de jour (boutons)
        window.insertDay = function (day) {
            textarea.value += (textarea.value ? "\n" : "") + day + ": ";
            textarea.focus();
        };

        textarea.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const lines = this.value.split('\n');
                let lastLine = lines[lines.length - 1]; // text on the last line
                
                // If the user presses enter with a blank line at the very bottom,
                // the lines array will have an empty string. Let's ignore empty trailing lines.
                if (!lastLine && lines.length > 1) {
                    lastLine = lines[lines.length - 2];
                }

                for (let i = 0; i < days.length; i++) {
                    if (lastLine.toLowerCase().startsWith(days[i].toLowerCase() + ":")) {
                        e.preventDefault();
                        const nextDay = days[(i + 1) % days.length];
                        
                        this.value += "\n" + nextDay + ": ";
                        
                        // Force Move Cursor to End for all browsers
                        this.selectionStart = this.value.length;
                        this.selectionEnd = this.value.length;
                        return;
                    }
                }
            }
        });
    }

    // ========================================
    // VALIDATION : Formulaire Admin Ajout (add_programme_back.php)
    // ========================================
    const adminForm = document.getElementById('adminForm');
    if (adminForm) {
        adminForm.addEventListener('submit', (e) => {
            let ok = true;
            const fields = ['nom', 'calorie_jour', 'proteine', 'glucide', 'lipides', 'titre_planning', 'programme_sport'];

            fields.forEach(f => {
                const input = document.getElementById(f);
                const error = document.getElementById('error-' + f);
                if (!input || !error) return;
                error.textContent = "";

                if (!input.value.trim()) {
                    error.textContent = "Ce champ est obligatoire";
                    ok = false;
                } else if (['calorie_jour', 'proteine', 'glucide', 'lipides'].includes(f) && isNaN(input.value.trim())) {
                    error.textContent = "Veuillez saisir un nombre valide";
                    ok = false;
                }
            });

            if (!ok) e.preventDefault();
        });
    }

    // ========================================
    // VALIDATION : Formulaire Admin Edit (edit_planning.php)
    // ========================================
    const editAdminForm = document.getElementById('editAdminForm');
    if (editAdminForm) {
        editAdminForm.addEventListener('submit', (e) => {
            let ok = true;
            const fields = ['nom', 'calorie_jour', 'proteine', 'glucide', 'lipides', 'titre_planning', 'programme_sport'];

            fields.forEach(f => {
                const input = document.getElementById(f);
                const error = document.getElementById('error-' + f);
                if (!input || !error) return;
                error.textContent = "";

                if (!input.value.trim()) {
                    error.textContent = "Ce champ est obligatoire";
                    ok = false;
                } else if (['calorie_jour', 'proteine', 'glucide', 'lipides'].includes(f) && isNaN(input.value.trim())) {
                    error.textContent = "Veuillez saisir un nombre valide";
                    ok = false;
                }
            });

            if (!ok) e.preventDefault();
        });
    }

    // ========================================
    // VALIDATION : Formulaire Régime Back (edit_regime.php)
    // ========================================
    const regimeForm = document.getElementById('regimeForm');
    if (regimeForm && !adminForm) {
        const inputs = {
            nom: document.getElementById('nom'),
            calorie: document.getElementById('calorie_jour'),
            proteine: document.getElementById('proteine'),
            glucide: document.getElementById('glucide'),
            lipides: document.getElementById('lipides')
        };

        const errors = {
            nom: document.getElementById('error-nom'),
            calorie: document.getElementById('error-calorie'),
            proteine: document.getElementById('error-proteine'),
            glucide: document.getElementById('error-glucide'),
            lipides: document.getElementById('error-lipides')
        };

        function validateField(field) {
            let isValid = true;
            let msg = "";

            switch (field) {
                case 'nom':
                    if (inputs.nom && inputs.nom.value.trim() === "") {
                        msg = "Le nom est obligatoire.";
                        isValid = false;
                    }
                    if (errors.nom) errors.nom.textContent = msg;
                    break;
                case 'calorie':
                case 'proteine':
                case 'glucide':
                case 'lipides':
                    if (inputs[field] && inputs[field].value.toString().trim() === "") {
                        msg = "Champ requis.";
                        isValid = false;
                    } else if (inputs[field] && isNaN(inputs[field].value)) {
                        msg = "Nombre invalide.";
                        isValid = false;
                    }
                    if (errors[field]) errors[field].textContent = msg;
                    break;
            }
            return isValid;
        }

        Object.keys(inputs).forEach(key => {
            if (inputs[key]) {
                inputs[key].addEventListener('input', () => validateField(key));
            }
        });

        regimeForm.addEventListener('submit', function (e) {
            let isFormValid = true;
            Object.keys(inputs).forEach(key => {
                if (!validateField(key)) isFormValid = false;
            });
            if (!isFormValid) e.preventDefault();
        });
    }
});
