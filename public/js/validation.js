/**
 * NutriVerse Strict Validation Engine
 * 0 HTML5 attributes (required, number, etc.) used.
 * Rules:
 * - Ingredient: Letters and spaces only (No numbers).
 * - Quantity: Numbers only (No letters).
 * - Location/Ville: Letters and spaces only (No numbers).
 */

function validateNutriForm(form) {
    let isValid = true;
    let firstErrorField = null;
    const inputs = form.querySelectorAll('input, select, textarea');

    // Regex patterns
    const regexLetters = /^[a-zA-ZÀ-ÿ\s'-]+$/;
    const regexNumbers = /^\d+(\.\d+)?$/;

    inputs.forEach(function(field) {
        if (field.type === 'submit' || field.type === 'button' || field.type === 'hidden') return;

        field.style.borderColor = '';
        const val = field.value.trim();

        // 1. Mandatory Check (Empty)
        if (val === '' && field.type !== 'checkbox' && field.name !== 'message' && field.name !== 'description') {
            isValid = false;
            field.style.borderColor = 'var(--alert)';
            if (!firstErrorField) firstErrorField = field;
        }

        // 2. Ingredient Name Validation (No numbers)
        if (field.name === 'ingredient' && val !== '') {
            if (!regexLetters.test(val)) {
                isValid = false;
                field.style.borderColor = 'var(--alert)';
                if (!firstErrorField) firstErrorField = field;
            }
        }

        // 3. Quantity Validation (Numbers only)
        if ((field.name === 'quantite' || field.id === 'updateQty' || field.id === 'modalInput') && val !== '') {
            // Check if it's supposed to be a quantity field based on context/placeholder
            if (field.placeholder && field.placeholder.toLowerCase().includes('quantité') || field.name === 'quantite') {
                if (!regexNumbers.test(val) || parseFloat(val) <= 0) {
                    isValid = false;
                    field.style.borderColor = 'var(--alert)';
                    if (!firstErrorField) firstErrorField = field;
                }
            }
        }

        // 4. Location/Ville Validation (No numbers)
        if (field.name === 'ville' && val !== '') {
            if (!regexLetters.test(val)) {
                isValid = false;
                field.style.borderColor = 'var(--alert)';
                if (!firstErrorField) firstErrorField = field;
            }
        }
    });

    if (!isValid) {
        if (firstErrorField) firstErrorField.focus();
        if (typeof showToast === 'function') {
            showToast('Erreur : Vérifiez le type de données (Lettres pour Noms, Chiffres pour Quantités).', 'error');
        } else {
            alert('Veuillez saisir les types de données corrects (Lettres pour Noms, Chiffres pour Quantités).');
        }
    }

    return isValid;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form').forEach(function(form) {
        if (form.id === 'formDon') return;
        form.addEventListener('submit', function(e) {
            if (!validateNutriForm(form)) {
                e.preventDefault();
            }
        });
    });
    console.log("NutriVerse Strict Logic: Letters-Only/Numbers-Only Active.");
});
