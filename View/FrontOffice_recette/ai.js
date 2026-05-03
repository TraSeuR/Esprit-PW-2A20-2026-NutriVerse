document.addEventListener("DOMContentLoaded", function () {

    /* GENERATEUR 1 : RECETTE PERSONNALISÉE*/

    const pref = document.getElementById("preferences");
    const btn = document.getElementById("btnGenerate");

    document.querySelectorAll(".ai-generator:not(.budget-generator) .quick-tag-btn").forEach(tag => {

        tag.onclick = function () {

            const value = this.dataset.tag;

            let arr = pref.value
                .split(",")
                .map(x => x.trim())
                .filter(x => x !== "");

            if (arr.includes(value)) {
                arr = arr.filter(x => x !== value);
                this.classList.remove("active");
            } else {
                arr.push(value);
                this.classList.add("active");
            }

            pref.value = arr.join(", ");
        };

    });

    if (btn) {

        btn.onclick = function () {

            let ingredients = document.getElementById("ingredients").value;
            let preferences = pref.value;

            btn.innerHTML = "Génération...";
            btn.disabled = true;

            let xhr = new XMLHttpRequest();

            xhr.open("POST", "generate_recette.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onload = function () {

                document.getElementById("resultats").innerHTML = this.responseText;

                btn.innerHTML = "Générer ma recette";
                btn.disabled = false;
            };

            xhr.send(
                "ingredients=" + encodeURIComponent(ingredients) +
                "&preferences=" + encodeURIComponent(preferences)
            );
        };
    }


    /*GENERATEUR 2 : BUDGET*/

    const btnBudget = document.getElementById("btnBudget");

    if (btnBudget) {

        let selectedMeal = "";
        let selectedPeople = "";
        let selectedPrefs = [];

        /* TYPE REPAS */
        document.querySelectorAll("#type_repas .quick-tag-btn").forEach(tag => {

            tag.onclick = function () {

                document.querySelectorAll("#type_repas .quick-tag-btn")
                    .forEach(x => x.classList.remove("active"));

                this.classList.add("active");
                selectedMeal = this.dataset.tag;
            };
        });

        /* PERSONNES */
        document.querySelectorAll("#personnes .quick-tag-btn").forEach(tag => {

            tag.onclick = function () {

                document.querySelectorAll("#personnes .quick-tag-btn")
                    .forEach(x => x.classList.remove("active"));

                this.classList.add("active");
                selectedPeople = this.dataset.tag;
            };
        });

        /* PREFERENCES */
        document.querySelectorAll("#budget_preferences .quick-tag-btn").forEach(tag => {

            tag.onclick = function () {

                const value = this.dataset.tag;

                this.classList.toggle("active");

                if (selectedPrefs.includes(value)) {
                    selectedPrefs = selectedPrefs.filter(x => x !== value);
                } else {
                    selectedPrefs.push(value);
                }
            };
        });

        /* GENERER */
        btnBudget.onclick = function () {

            let formData = new FormData();

            formData.append("budget", document.getElementById("budget").value);
            formData.append("devise", document.getElementById("devise").value);
            formData.append("type_repas", selectedMeal);
            formData.append("preferences", selectedPrefs.join(", "));
            formData.append("personnes", selectedPeople);

            btnBudget.innerHTML = "Génération...";
            btnBudget.disabled = true;

            fetch("budget_recette.php", {
                method: "POST",
                body: formData
            })

            .then(res => res.json())

            .then(data => {

                console.log(data);

                if (data.error) {
                    alert(data.error);

                    btnBudget.innerHTML = "Générer recette par budget";
                    btnBudget.disabled = false;
                    return;
                }

                let html = "";

                data.recipes.forEach(r => {

                 html += `
<a href="budget_details.php?type=budget&nom=${encodeURIComponent(r.nom)}&categorie=${encodeURIComponent(r.categorie)}&description=${encodeURIComponent(r.description)}&temps=${encodeURIComponent(r.temps)}&ingredients=${encodeURIComponent(
r.ingredients.map(i =>
i.nom + " - " +
i.quantite + " - " +
i.prix + " " +
document.getElementById("devise").value
).join("|")
)}&etapes=${encodeURIComponent(r.etapes.join("|"))}&conseil=${encodeURIComponent(r.conseil)}&image=${encodeURIComponent(r.image)}&budget_total=${encodeURIComponent(r.budget_total)}&budget_user=${encodeURIComponent(document.getElementById("budget").value)}&devise=${encodeURIComponent(document.getElementById("devise").value)}&personnes=${encodeURIComponent(selectedPeople)}" class="card-link">

<div class="card">
<img src="${r.image}" alt="${r.nom}">
<div class="card-content">
<div class="tags">
<span class="tag">${r.categorie}</span>
</div>
<h3>${r.nom}</h3>
</div>
</div>

</a>
`;
                });

                document.getElementById("resultats").innerHTML = html;

                btnBudget.innerHTML = "Générer recette par budget";
                btnBudget.disabled = false;

            })

            .catch(err => {

                console.log(err);
                alert("Erreur génération budget");

                btnBudget.innerHTML = "Générer recette par budget";
                btnBudget.disabled = false;

            });
        };
    }

});