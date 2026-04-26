/**
 * nutriverse - Contrôles de saisie FrontOffice
 */

document.addEventListener('DOMContentLoaded', function () {

    // ========================================
    // 1. AUTO-CALCUL DES CALORIES 
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
            caloInput.value = total > 0 ? Math.round(total) : "";
        };

        [protInput, glucInput, lipiInput].forEach(input => {
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                calculateCalories();
            });
        });
    }

    // ========================================
    // 2. ASSISTANT PLANNING (Jours de la semaine)
    // ========================================
    const days = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
    const textarea = document.getElementById('programme_sport');

    if (textarea) {
        window.insertDay = function (day) {
            textarea.value += (textarea.value ? "\n" : "") + day + ": ";
            textarea.focus();
            // Sauvegarder après insertion automatique
            localStorage.setItem('draft_sport', textarea.value);
        };

        textarea.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const lines = this.value.split('\n');
                let lastLine = lines[lines.length - 1] || lines[lines.length - 2];
                if (!lastLine) return;

                for (let i = 0; i < days.length; i++) {
                    if (lastLine.toLowerCase().startsWith(days[i].toLowerCase() + ":")) {
                        e.preventDefault();
                        const nextDay = days[(i + 1) % days.length];
                        this.value += "\n" + nextDay + ": ";
                        localStorage.setItem('draft_sport', this.value);
                        return;
                    }
                }
            }
        });
    }

    // ========================================
    // 3. PERSISTANCE (Auto-save Planning)
    // ========================================
    const titreInput = document.getElementById('titre_planning');
    const sportInput = document.getElementById('programme_sport');
    const somInput = document.getElementById('sommeil');

    if (titreInput && sportInput && somInput) {
        // CHARGEMENT : On remplit si on trouve quelque chose
        const savedTitre = localStorage.getItem('draft_titre');
        const savedSport = localStorage.getItem('draft_sport');
        const savedSom = localStorage.getItem('draft_sommeil');

        if (savedTitre) titreInput.value = savedTitre;
        if (savedSport) sportInput.value = savedSport;
        if (savedSom) somInput.value = savedSom;

        // SAUVEGARDE : A chaque changement
        titreInput.addEventListener('input', () => localStorage.setItem('draft_titre', titreInput.value));
        sportInput.addEventListener('input', () => localStorage.setItem('draft_sport', sportInput.value));
        somInput.addEventListener('change', () => localStorage.setItem('draft_sommeil', somInput.value));
    }

    // ========================================
    // 4. VALIDATIONS DES FORMULAIRES
    // ========================================
    
    // Formulaire Régime
    const regimeForm = document.getElementById('regimeForm');
    if (regimeForm) {
        regimeForm.addEventListener('submit', (e) => {
            let isValid = true;
            ['nom', 'type', 'calorie_jour', 'proteine', 'glucide', 'lipides'].forEach(f => {
                const input = document.getElementById(f);
                const error = document.getElementById('error-' + f);
                if (!input || !error) return;
                error.textContent = "";
                if (!input.value.trim()) {
                    error.textContent = "Ce champ est obligatoire";
                    isValid = false;
                }
            });
            if (!isValid) e.preventDefault();
        });
    }

    // Formulaire Planning
    const planningForm = document.getElementById('planningForm');
    if (planningForm) {
        planningForm.addEventListener('submit', (e) => {
            let ok = true;
            ['titre_planning', 'programme_sport', 'sommeil'].forEach(f => {
                const input = document.getElementById(f);
                const errId = 'error-' + (f === 'titre_planning' ? 'titre' : (f === 'programme_sport' ? 'sport' : 'som'));
                const error = document.getElementById(errId);
                if (!input || !error) return;
                error.textContent = "";
                if (!input.value.trim()) {
                    error.textContent = "Ce champ est obligatoire";
                    ok = false;
                }
            });

            if (!ok) {
                e.preventDefault();
            } else {
                // VICTOIRE : On vide la mémoire seulement si tout est validé
                localStorage.removeItem('draft_titre');
                localStorage.removeItem('draft_sport');
                localStorage.removeItem('draft_sommeil');
            }
        });
    }

    // Formulaire Modification
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            const nomInput = document.getElementById('nom');
            const errorNom = document.getElementById('error-nom');
            if (nomInput && errorNom && nomInput.value.length < 3) {
                errorNom.style.display = 'block';
                e.preventDefault();
            }
        });
    }
});
