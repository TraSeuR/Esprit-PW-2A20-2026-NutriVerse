document.addEventListener("DOMContentLoaded", function () {

    console.log("PAGE LOADED");

    const btn = document.querySelector(".btn-add");
    const container = document.getElementById("ingredients-container");

    if (!btn || !container) {
        console.log("BTN OU CONTAINER INTROUVABLE");
        return;
    }

    console.log("BOUTON OK");

    btn.addEventListener("click", function () {

        console.log("CLICK OK");

        const row = document.createElement("div");
        row.className = "ingredient-row";

        row.innerHTML = `
            <div class="ing-field">
                <input type="text" name="ingredient_nom[]" placeholder="Nom">
                <span class="msg"></span>
            </div>

            <div class="ing-field">
                <input type="text" name="ingredient_qte[]" placeholder="Quantité">
                <span class="msg"></span>
            </div>

            <div class="ing-field">
                <input type="text" name="ingredient_unite[]" placeholder="Unité">
                <span class="msg"></span>
            </div>

            <button type="button" class="btn-remove">✖</button>
        `;

        container.appendChild(row);

        // supprimer ligne
        row.querySelector(".btn-remove").addEventListener("click", function () {
            row.remove();
        });

        // validation
        attachEvents(row.querySelector("[name='ingredient_nom[]']"), "nom");
        attachEvents(row.querySelector("[name='ingredient_qte[]']"), "qte");
        attachEvents(row.querySelector("[name='ingredient_unite[]']"), "unite");

    });

    function validateField(input, type) {

        let val = input.value.trim();
        let msg = input.nextElementSibling;

        if (!msg) return;

        if (type === "nom") {
            if (val.length < 3) {
                msg.textContent = " Min 3 caractères";
                msg.className = "msg error";
            } else {
                msg.textContent = " OK";
                msg.className = "msg success";
            }
        }

        if (type === "qte") {
            if (val === "" || isNaN(val)) {
                msg.textContent = " Nombre valide";
                msg.className = "msg error";
            } else {
                msg.textContent = " OK";
                msg.className = "msg success";
            }
        }

        if (type === "unite") {
            if (val === "") {
                msg.textContent = " Obligatoire";
                msg.className = "msg error";
            } else {
                msg.textContent = " OK";
                msg.className = "msg success";
            }
        }
    }

    function attachEvents(input, type) {
        input.addEventListener("keyup", function () {
            validateField(input, type);
        });

        input.addEventListener("blur", function () {
            validateField(input, type);
        });
    }

    function initIngredientInputs() {

        let noms = document.getElementsByName("ingredient_nom[]");
        let qtes = document.getElementsByName("ingredient_qte[]");
        let unites = document.getElementsByName("ingredient_unite[]");

        for (let i = 0; i < noms.length; i++) {
            attachEvents(noms[i], "nom");
            attachEvents(qtes[i], "qte");
            attachEvents(unites[i], "unite");
        }
    }

    function checkIngredients() {

        let noms = document.getElementsByName("ingredient_nom[]");
        let qtes = document.getElementsByName("ingredient_qte[]");
        let unites = document.getElementsByName("ingredient_unite[]");

        let ok = true;

        for (let i = 0; i < noms.length; i++) {

            if (noms[i].value.trim() === "") ok = false;
            if (qtes[i].value.trim() === "" || isNaN(qtes[i].value)) ok = false;
            if (unites[i].value.trim() === "") ok = false;
        }

        return ok;
    }

    initIngredientInputs();

    let form = document.getElementById("recetteForm");

    if (form) {
        form.addEventListener("submit", function (e) {

            if (!checkIngredients()) {
                e.preventDefault();
                alert("Remplis correctement les ingrédients !");
            }
        });
    }

});