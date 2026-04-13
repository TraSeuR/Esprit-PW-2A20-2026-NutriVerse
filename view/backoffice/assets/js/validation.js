document.addEventListener('DOMContentLoaded', function() {
    
    // VALIDATION GÉNÉRIQUE DE TOUS LES FORMULAIRES (JS Part 2 Pages 24-26)
    // Cette boucle remplace les validations spécifiques pour une approche plus globale et conforme.
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            let firstEmptyField = null;

            // On récupère tous les inputs et selects du formulaire
            const inputs = form.querySelectorAll('input, select, textarea');
            
            inputs.forEach(function(field) {
                if (field.type !== 'submit' && field.type !== 'button' && field.type !== 'hidden' && field.type !== 'checkbox') {
                    const value = field.value.trim();
                    
                    if (value === '') {
                        isValid = false;
                        if (!firstEmptyField) firstEmptyField = field;
                        field.style.borderColor = 'red';
                    } else {
                        // RÈGLES SPÉCIFIQUES (Lettres pour Ingrédient/Ville, Nombres pour Quantité)
                        if (field.name === 'ingredient' || field.name === 'ville') {
                            const letterPattern = /^[a-zA-ZÀ-ÿ\s'-]+$/;
                            if (!letterPattern.test(value)) {
                                isValid = false;
                                if (!firstEmptyField) firstEmptyField = field;
                                field.style.borderColor = 'red';
                            } else {
                                field.style.borderColor = '';
                            }
                        } 
                        else if (field.name === 'quantite' || field.name === 'nouvelle_quantite' || field.name === 'id_offre' || field.name === 'id_echange') {
                            // On autorise le préfixe ECH- ou OFF- si présent, mais on vérifie le reste
                            const cleanValue = value.replace(/OFF-|ECH-/i, '');
                            if (isNaN(cleanValue) || cleanValue === '' || Number(cleanValue) < 0) {
                                isValid = false;
                                if (!firstEmptyField) firstEmptyField = field;
                                field.style.borderColor = 'red';
                            } else {
                                field.style.borderColor = '';
                            }
                        } else {
                            field.style.borderColor = '';
                        }
                    }
                }
            });

            if (!isValid) {
                e.preventDefault(); 
                alert('Erreur de saisie :\n- Les noms (Ingrédient/Ville) ne doivent contenir que des lettres.\n- Les quantités et IDs doivent être des nombres positifs.');
                if (firstEmptyField) firstEmptyField.focus();
            }
        });
    });

    console.log("Validation NutriVerse JS chargée : 0 attribut required HTML5 utilisé.");
});
