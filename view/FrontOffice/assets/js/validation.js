/**
 * NutriVerse Form Validation Helper
 * Handles real-time validation and error display under inputs
 */
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form[novalidate]');

    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            // Validate on blur or input
            input.addEventListener('blur', () => validateInput(input));
            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid')) {
                    validateInput(input);
                }
            });
        });

        form.addEventListener('submit', (e) => {
            let isValid = true;
            inputs.forEach(input => {
                if (!validateInput(input)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                const firstError = form.querySelector('.is-invalid');
                if (firstError) firstError.focus();
            }
        });
    });
});

function validateInput(input) {
    const group = input.closest('.form-group') || input.closest('.u-form-group');
    if (!group) return true;

    let errorMsg = '';
    const val = input.value.trim();

    // Required check
    if (input.hasAttribute('required') && !val) {
        errorMsg = 'Ce champ est obligatoire.';
    } 
    // Email check
    else if (input.type === 'email' && val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
        errorMsg = 'Veuillez entrer une adresse e-mail valide.';
    }
    // Password length check
    else if (input.id === 'mot_de_passe' && val && val.length < 8) {
        errorMsg = 'Le mot de passe doit contenir au moins 8 caractères.';
    }
    // Confirm password match
    else if (input.id === 'confirm_mot_de_passe' || input.id === 'confirm_pw') {
        const mainPw = document.getElementById('mot_de_passe');
        if (mainPw && val !== mainPw.value) {
            errorMsg = 'Les mots de passe ne correspondent pas.';
        }
    }
    // Phone validation
    else if (input.type === 'tel' && val && !/^[0-9+\s]{8,15}$/.test(val)) {
        errorMsg = 'Format de téléphone invalide.';
    }

    showError(input, group, errorMsg);
    return errorMsg === '';
}

function showError(input, group, msg) {
    let errorDisplay = group.querySelector('.error-feedback');
    
    if (!errorDisplay) {
        errorDisplay = document.createElement('div');
        errorDisplay.className = 'error-feedback';
        errorDisplay.style.color = '#dc2626';
        errorDisplay.style.fontSize = '0.8rem';
        errorDisplay.style.marginTop = '4px';
        errorDisplay.style.fontWeight = '500';
        group.appendChild(errorDisplay);
    }

    if (msg) {
        input.classList.add('is-invalid');
        input.style.borderColor = '#dc2626';
        errorDisplay.textContent = msg;
        errorDisplay.style.display = 'block';
    } else {
        input.classList.remove('is-invalid');
        input.style.borderColor = '';
        errorDisplay.textContent = '';
        errorDisplay.style.display = 'none';
    }
}
