document.addEventListener("DOMContentLoaded", function () {

    document.getElementById("btnGenerate").addEventListener("click", function () {

        let ingredients = document.getElementById("ingredients").value;
        let preferences = document.getElementById("preferences").value;

        let xhr = new XMLHttpRequest();

        xhr.open("POST", "generate_recette.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (this.status === 200) {
                document.getElementById("resultats").innerHTML = this.responseText;
            }
        };

        xhr.send(
            "ingredients=" + encodeURIComponent(ingredients) +
            "&preferences=" + encodeURIComponent(preferences)
        );

    });

});