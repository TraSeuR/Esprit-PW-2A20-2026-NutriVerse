<section class="container checkout-page">
    <div class="checkout-container fade-up">
        <!-- LEFT COLUMN: Intro -->
        <div class="checkout-intro">
            <div class="page-title">
                <span class="bag-icon">🛍️</span>
                <div>
                    <h1>Finaliser la commande</h1>
                    <p>Veuillez remplir vos informations de livraison.</p>
                </div>
            </div>
            
            <!-- Optional: Cart Summary can go here if needed later -->
            <div class="checkout-image-placeholder">
                <!-- Illustration or extra info -->
            </div>
        </div>

        <!-- RIGHT COLUMN: Form Card -->
        <div class="checkout-card fade-up delay-1">
            <form method="post" action="shop.php?action=place_order" id="orderForm">
                
                <div class="form-group">
                    <label for="nom">Nom complet <span class="required">*</span></label>
                    <input type="text" id="nom" name="nom" placeholder="Ex: Foulen Ben Foulen">
                    <span class="error-msg" id="error-nom">Veuillez entrer un nom valide.</span>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse de livraison <span class="required">*</span></label>
                    <textarea id="adresse" name="adresse" placeholder="Ex: 123 Rue de la République, Tunis"></textarea>
                    <span class="error-msg" id="error-adresse">L'adresse est trop courte.</span>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone <span class="required">*</span></label>
                    <input type="text" id="telephone" name="telephone" placeholder="Ex: 22123456">
                    <span class="error-msg" id="error-telephone">Numéro invalide (8 chiffres).</span>
                </div>

                <div class="form-group">
                    <label>Mode de paiement</label>
                    <div class="payment-selector">
                        <div class="payment-option active" data-method="livraison">
                            <span class="dot"></span>
                            <span>Paiement à la livraison</span>
                        </div>
                        <div class="payment-option" data-method="carte">
                            <span class="dot"></span>
                            <span>Carte bancaire</span>
                        </div>
                    </div>
                    <input type="hidden" name="paiement" id="paiement" value="livraison">
                </div>

                <!-- Section Carte (S'affiche si 'carte' est choisi) -->
                <div id="cardDetailsSection" style="display: none; background: #f9fbfd; padding: 20px; border-radius: 12px; border: 1px solid #e1e8ef; margin-bottom: 20px;">
                    <div class="form-group">
                        <label for="numero_carte">Numéro de carte</label>
                        <input type="text" id="numero_carte" name="numero_carte" placeholder="0000 0000 0000 0000" maxlength="19">
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="date_expiration" name="date_expiration" placeholder="MM/AA" maxlength="5" style="flex:1;">
                        <input type="password" id="cvv_carte" name="cvv_carte" placeholder="CVV" maxlength="3" style="flex:1;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="code_promo">Code promo</label>
                    <div class="promo-box">
                        <input type="text" id="code_promo" name="code_promo" placeholder="Ex: NUTRI20">
                        <button type="button" id="btnApplyPromo">Appliquer</button>
                    </div>
                    <span id="promoMsg" class="promo-feedback"></span>
                </div>

                <div class="checkout-footer">
                    <div class="total-display">
                        <span>Total à payer:</span>
                        <span class="total-amount"><?= number_format($total, 2) ?> DT</span>
                    </div>
                    <button type="submit" class="confirm-btn">Confirmer la commande</button>
                </div>
            </form>
        </div>
    </div>
</section>
</section>

<style>
    .checkout-page {
        padding: 60px 0;
        background: #fcfdfe;
        min-height: 80vh;
    }

    .checkout-container {
        display: flex;
        gap: 60px;
        align-items: flex-start;
        max-width: 1100px;
        margin: 0 auto;
    }

    .checkout-intro {
        flex: 1;
        padding-top: 20px;
    }

    .page-title {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 30px;
    }

    .bag-icon {
        font-size: 2.5rem;
        background: #eaf4ff;
        padding: 15px;
        border-radius: 18px;
    }

    .page-title h1 {
        font-size: 2.2rem;
        color: #1c2733;
        margin-bottom: 8px;
        font-weight: 800;
    }

    .page-title p {
        color: #6f7680;
        font-size: 1.1rem;
    }

    .checkout-card {
        flex: 1;
        background: white;
        border-radius: 28px;
        padding: 40px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.06);
        border: 1px solid #f0f3f5;
    }

    .form-group {
        margin-bottom: 22px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: #1c2733;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }

    .form-group input, .form-group textarea {
        width: 100%;
        padding: 14px 18px;
        border: 1px solid #e1e8ef;
        border-radius: 14px;
        font-size: 1rem;
        transition: all 0.3s;
        background: #fcfdfe;
    }

    .form-group input:focus, .form-group textarea:focus {
        border-color: #59b84d;
        background: white;
        box-shadow: 0 0 0 4px rgba(89, 184, 77, 0.1);
        outline: none;
    }

    .form-group textarea {
        height: 100px;
        resize: none;
    }

    .required { color: #e74c3c; margin-left: 4px; }

    /* Payment Selector */
    .payment-selector {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .payment-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        border: 1px solid #e1e8ef;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
        color: #6f7680;
    }

    .payment-option.active {
        border-color: #59b84d;
        background: #f2f9f1;
        color: #2d6a24;
    }

    .dot {
        width: 12px;
        height: 12px;
        border: 2px solid #e1e8ef;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .payment-option.active .dot {
        border-color: #59b84d;
        background: #59b84d;
    }

    /* Promo Box */
    .promo-box {
        display: flex;
        gap: 10px;
    }

    .promo-box input { flex: 1; }

    .promo-box button {
        background: #ff8a00;
        color: white;
        border: none;
        padding: 0 25px;
        border-radius: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }

    .promo-box button:hover { background: #e67e00; }

    .checkout-footer {
        margin-top: 30px;
        padding-top: 25px;
        border-top: 1px dashed #e1e8ef;
    }

    .total-display {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        font-weight: 700;
    }

    .total-display span:first-child { color: #6f7680; font-size: 1.1rem; }

    .total-amount { font-size: 1.8rem; color: #1c2733; }

    .confirm-btn {
        width: 100%;
        padding: 18px;
        background: #59b84d;
        color: white;
        border: none;
        border-radius: 16px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(89, 184, 77, 0.25);
        transition: all 0.3s;
    }

    .confirm-btn:hover { background: #4ca142; transform: translateY(-2px); }

    .error-msg { color: #e74c3c; font-size: 0.85rem; margin-top: 6px; display: none; }
    .error-msg.visible { display: block; }

    @media (max-width: 900px) {
        .checkout-container { flex-direction: column; gap: 40px; padding: 20px; }
        .checkout-intro { text-align: center; }
        .page-title { flex-direction: column; align-items: center; }
    }
</style>

<script>
    // Toggle Payment Method
    const paymentOptions = document.querySelectorAll('.payment-option');
    const inputPaiement = document.getElementById('paiement');
    const cardSection = document.getElementById('cardDetailsSection');

    paymentOptions.forEach(opt => {
        opt.addEventListener('click', function() {
            paymentOptions.forEach(o => o.classList.remove('active'));
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
    if(document.getElementById('numero_carte')) {
        document.getElementById('numero_carte').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) formattedValue += ' ';
                formattedValue += value[i];
            }
            e.target.value = formattedValue;
        });
    }

    // Formatting Expiration Date
    if(document.getElementById('date_expiration')) {
        document.getElementById('date_expiration').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    }

    document.getElementById('orderForm').addEventListener('submit', function(e) {
        let isValid = true;

        // Validation Nom
        const nom = document.getElementById('nom').value.trim();
        if (nom.length < 3) {
            document.getElementById('error-nom').classList.add('visible');
            isValid = false;
        } else {
            document.getElementById('error-nom').classList.remove('visible');
        }

        // Validation Adresse
        const adresse = document.getElementById('adresse').value.trim();
        if (adresse.length < 10) {
            document.getElementById('error-adresse').classList.add('visible');
            isValid = false;
        } else {
            document.getElementById('error-adresse').classList.remove('visible');
        }

        // Validation Téléphone
        const telephone = document.getElementById('telephone').value.trim();
        if (!/^[0-9]{8}$/.test(telephone)) {
            document.getElementById('error-telephone').classList.add('visible');
            isValid = false;
        } else {
            document.getElementById('error-telephone').classList.remove('visible');
        }

        if (!isValid) e.preventDefault();
    });

    // Promo Validation
    document.getElementById('btnApplyPromo').addEventListener('click', function() {
        const code = document.getElementById('code_promo').value.trim();
        const msg = document.getElementById('promoMsg');
        const totalAmountEl = document.querySelector('.total-amount');
        
        if (code === "") return;

        fetch(`shop.php?action=validate_promo&code=${code}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    msg.innerText = `✅ -${data.discount}% appliqué`;
                    msg.style.color = "#27ae60";
                    this.innerText = "OK";
                    this.disabled = true;
                    
                    let currentTotal = parseFloat(totalAmountEl.innerText.replace(' DT', ''));
                    let newTotal = currentTotal * (1 - data.discount / 100);
                    totalAmountEl.innerText = newTotal.toFixed(2) + " DT";
                } else {
                    msg.innerText = "❌ Code invalide";
                    msg.style.color = "#e74c3c";
                }
            });
    });
</script>
