<section class="container my-orders">
    <div class="page-header fade-up">
        <h1>🛍️ Finaliser la commande</h1>
        <p>Veuillez remplir vos informations de livraison.</p>
    </div>

    <div class="commande-wrapper fade-up delay-1">
        <form method="post" action="index.php?action=place_order" id="orderForm" class="commande-form">
            
            <div class="checkout-layout">
                <!-- LEfT COLUMN (Carte Bancaire Details) -->
                <div class="checkout-left" id="cardDetailsSection" style="display: none;">
                    <h3>💳 Détails de la carte</h3>
                    <div class="form-group">
                        <label for="nom_carte">Nom sur la carte <span class="required">*</span></label>
                        <input type="text" id="nom_carte" name="nom_carte" placeholder="Ex: Foulen Ben Foulen">
                        <span class="error-msg" id="error-nom-carte">Veuillez entrer un nom valide.</span>
                    </div>
                    <div class="form-group">
                        <label for="numero_carte">Numéro de carte <span class="required">*</span></label>
                        <input type="text" id="numero_carte" name="numero_carte" placeholder="0000 0000 0000 0000" maxlength="19">
                        <span class="error-msg" id="error-numero-carte">Numéro de carte invalide.</span>
                    </div>
                    <div style="display: flex; gap: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="date_expiration">Expiration <span class="required">*</span></label>
                            <input type="text" id="date_expiration" name="date_expiration" placeholder="MM/AA" maxlength="5">
                            <span class="error-msg" id="error-date-exp">Date invalide.</span>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="cvv_carte">CVV <span class="required">*</span></label>
                            <input type="password" id="cvv_carte" name="cvv_carte" placeholder="123" maxlength="3">
                            <span class="error-msg" id="error-cvv">CVV invalide.</span>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN (Standard Form) -->
                <div class="checkout-right">
                    <div class="form-group">
                        <label for="nom">Nom complet <span class="required">*</span></label>
                        <input type="text" id="nom" name="nom" placeholder="Ex: Foulen Ben Foulen">
                        <span class="error-msg" id="error-nom">Veuillez entrer un nom valide (lettres uniquement, min 3 caractères).</span>
                    </div>

                    <div class="form-group">
                        <label for="adresse">Adresse de livraison <span class="required">*</span></label>
                        <textarea id="adresse" name="adresse" placeholder="Ex: 123 Rue de la République, Tunis"></textarea>
                        <span class="error-msg" id="error-adresse">L'adresse doit contenir au moins 10 caractères.</span>
                    </div>

                    <div class="form-group">
                        <label for="telephone">Téléphone <span class="required">*</span></label>
                        <input type="text" id="telephone" name="telephone" placeholder="Ex: 22123456">
                        <span class="error-msg" id="error-telephone">Le numéro de téléphone doit contenir exactement 8 chiffres.</span>
                    </div>

                    <div class="form-group">
                        <label>Mode de paiement</label>
                        <div class="payment-methods">
                            <button type="button" class="btn-payment active" data-method="livraison">Paiement à la livraison</button>
                            <button type="button" class="btn-payment" data-method="carte">Carte bancaire</button>
                        </div>
                        <input type="hidden" name="paiement" id="paiement" value="livraison">
                    </div>

                    <div class="form-group">
                        <label for="code_promo">Code promo</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="code_promo" name="code_promo" placeholder="Ex: NUTRI20">
                            <button type="button" id="btnApplyPromo" style="background: var(--orange, #ff8a00); color: white; border: none; padding: 0 20px; border-radius: 12px; cursor: pointer; font-weight: 600; white-space: nowrap;">Appliquer</button>
                        </div>
                        <span id="promoMsg" style="font-size: 0.85rem; margin-top: 5px; display: none;"></span>
                    </div>

                    <div class="total-summary">
                        <div class="total-line final">
                            <span>Total à payer</span>
                            <span class="total-amount"><?= number_format($total, 2) ?> DT</span>
                        </div>
                    </div>

                    <button type="submit" class="confirm-btn">Confirmer la commande</button>
                </div>
            </div>
        </form>
    </div>
</section>

<style>
    /* Styles spécifiques pour le formulaire de commande */
    .commande-wrapper {
        background: #ffffff;
        border-radius: 24px;
        padding: 3rem;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .checkout-layout {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }

    .checkout-left, .checkout-right {
        flex: 1;
        min-width: 300px;
    }

    .checkout-left {
        background: #f8fafc;
        padding: 2rem;
        border-radius: 16px;
        border: 1px solid #e5e9eb;
    }

    .checkout-left h3 {
        margin-top: 0;
        margin-bottom: 1.5rem;
        color: #1c2733;
    }

    .payment-methods {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .btn-payment {
        padding: 1rem;
        border: 2px solid #e5e9eb;
        border-radius: 12px;
        background: white;
        cursor: pointer;
        font-weight: 600;
        font-size: 1rem;
        color: #6f7680;
        transition: all 0.3s;
        text-align: left;
    }

    .btn-payment.active {
        border-color: var(--green, #59b84d);
        background: var(--green-soft, #edf7ec);
        color: var(--green-dark, #3f9636);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--text, #1c2733);
    }

    .form-group .required {
        color: #e74c3c;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 1rem;
        border: 2px solid #e5e9eb;
        border-radius: 12px;
        font-size: 1rem;
        font-family: inherit;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--green, #59b84d);
    }

    .form-group textarea {
        height: 120px;
        resize: vertical;
    }

    .error-msg {
        display: none;
        color: #e74c3c;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .error-msg.visible {
        display: block;
    }

    .total-summary {
        background: var(--green-soft, #edf7ec);
        padding: 1.5rem;
        border-radius: 16px;
        margin: 2rem 0;
    }

    .total-line.final {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--green-dark, #3f9636);
    }

    .total-amount {
        font-size: 1.5rem;
    }

    .confirm-btn {
        width: 100%;
        padding: 1.2rem;
        background: var(--green, #59b84d);
        color: white;
        border: none;
        border-radius: 999px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
        box-shadow: 0 8px 20px rgba(89, 184, 77, 0.3);
    }

    .confirm-btn:hover {
        background: var(--green-dark, #3f9636);
        transform: translateY(-2px);
    }
</style>

<script>
    // Toggle Payment Method
    const btnPayments = document.querySelectorAll('.btn-payment');
    const inputPaiement = document.getElementById('paiement');
    const cardSection = document.getElementById('cardDetailsSection');

    btnPayments.forEach(btn => {
        btn.addEventListener('click', function() {
            btnPayments.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const method = this.getAttribute('data-method');
            inputPaiement.value = method;
            
            if (method === 'carte') {
                cardSection.style.display = 'block';
            } else {
                cardSection.style.display = 'none';
            }
        });
    });

    // Formatting Card Number
    document.getElementById('numero_carte').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        let formattedValue = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) formattedValue += ' ';
            formattedValue += value[i];
        }
        e.target.value = formattedValue;
    });

    // Formatting Expiration Date
    document.getElementById('date_expiration').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value;
    });

    document.getElementById('orderForm').addEventListener('submit', function(e) {
        let isValid = true;

        // Validation Nom (Lettres et espaces, min 3 chars)
        const nom = document.getElementById('nom').value.trim();
        const nomRegex = /^[a-zA-ZÀ-ÿ\s]{3,}$/;
        if (!nomRegex.test(nom)) {
            document.getElementById('error-nom').classList.add('visible');
            isValid = false;
        } else {
            document.getElementById('error-nom').classList.remove('visible');
        }

        // Validation Adresse (Min 10 chars)
        const adresse = document.getElementById('adresse').value.trim();
        if (adresse.length < 10) {
            document.getElementById('error-adresse').classList.add('visible');
            isValid = false;
        } else {
            document.getElementById('error-adresse').classList.remove('visible');
        }

        // Validation Téléphone (Exactement 8 chiffres)
        const telephone = document.getElementById('telephone').value.trim();
        const telRegex = /^[0-9]{8}$/;
        if (!telRegex.test(telephone)) {
            document.getElementById('error-telephone').classList.add('visible');
            isValid = false;
        } else {
            document.getElementById('error-telephone').classList.remove('visible');
        }

        // Validation Carte Bancaire si selectionnée
        if (inputPaiement.value === 'carte') {
            const nomCarte = document.getElementById('nom_carte').value.trim();
            if (nomCarte.length < 3) {
                document.getElementById('error-nom-carte').classList.add('visible');
                isValid = false;
            } else {
                document.getElementById('error-nom-carte').classList.remove('visible');
            }

            const numCarte = document.getElementById('numero_carte').value.replace(/\s/g, '');
            if (numCarte.length !== 16) {
                document.getElementById('error-numero-carte').classList.add('visible');
                isValid = false;
            } else {
                document.getElementById('error-numero-carte').classList.remove('visible');
            }

            const expDate = document.getElementById('date_expiration').value;
            if (!/^\d{2}\/\d{2}$/.test(expDate)) {
                document.getElementById('error-date-exp').classList.add('visible');
                isValid = false;
            } else {
                document.getElementById('error-date-exp').classList.remove('visible');
            }

            const cvv = document.getElementById('cvv_carte').value;
            if (!/^\d{3}$/.test(cvv)) {
                document.getElementById('error-cvv').classList.add('visible');
                isValid = false;
            } else {
                document.getElementById('error-cvv').classList.remove('visible');
            }
        }

        if (!isValid) {
            e.preventDefault(); // Empêche l'envoi du formulaire
        }
    });

    // Simulation Code Promo REELLE
    document.getElementById('btnApplyPromo').addEventListener('click', function() {
        const code = document.getElementById('code_promo').value.trim();
        const msg = document.getElementById('promoMsg');
        const totalAmountEl = document.querySelector('.total-amount');
        
        if (code === "") return;

        // Appel AJAX au contrôleur
        fetch(`index.php?action=validate_promo&code=${code}`)
            .then(response => {
                if (!response.ok) throw new Error("Erreur serveur (" + response.status + ")");
                return response.text(); // On récupère d'abord en texte pour vérifier
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    msg.style.display = 'block';
                    if (data.success) {
                        msg.innerText = `✅ Code appliqué ! -${data.discount}% sur votre commande.`;
                        msg.style.color = "#27ae60";
                        this.style.background = "#27ae60";
                        this.innerText = "Appliqué";
                        this.disabled = true;
                        document.getElementById('code_promo').readOnly = true;

                        let currentTotal = parseFloat(totalAmountEl.innerText.replace(' DT', ''));
                        let newTotal = currentTotal * (1 - data.discount / 100);
                        totalAmountEl.innerText = newTotal.toFixed(2) + " DT";
                    } else {
                        msg.innerText = "❌ " + data.message;
                        msg.style.color = "#e74c3c";
                    }
                } catch (e) {
                    console.error("Réponse non-JSON reçue :", text);
                    msg.style.display = 'block';
                    msg.innerText = "❌ Réponse invalide du serveur.";
                    msg.style.color = "#e74c3c";
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                msg.style.display = 'block';
                msg.innerText = "❌ Impossible de contacter le serveur.";
                msg.style.color = "#e74c3c";
            });
    });
</script>