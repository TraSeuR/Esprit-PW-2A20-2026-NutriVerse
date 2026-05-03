document.addEventListener("DOMContentLoaded", function () {

    const pref = document.getElementById("preferences");
    const btn = document.getElementById("btnGenerate");

    /* el filtr */
    document.querySelectorAll(".quick-tag-btn").forEach(tag => {

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

    /* btn generer */
      btn.onclick = function () {

    let ingredients = document.getElementById("ingredients").value;
    let preferences = pref.value;

    // changer bouton
    btn.innerHTML = "Génération en cours...";
    btn.disabled = true;
    btn.style.opacity = "0.7";

    let xhr = new XMLHttpRequest();

    xhr.open("POST", "generate_recette.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function () {

        document.getElementById("resultats").innerHTML = this.responseText;

        // remettre bouton normal
        btn.innerHTML = "Générer";
        btn.disabled = false;
        btn.style.opacity = "1";
    };

    xhr.send(
        "ingredients=" + encodeURIComponent(ingredients) +
        "&preferences=" + encodeURIComponent(preferences)
    );
};
});