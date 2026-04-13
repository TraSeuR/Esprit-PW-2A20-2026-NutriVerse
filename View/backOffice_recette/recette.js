
// nverifiw el formulr lkol wkt nenzlou ajouter 

function validerFormulaire() {

    let errors = "";

    let nom = document.getElementById("nom").value.trim();
    let description = document.getElementById("description").value.trim();
    let etapes = document.getElementById("etapes").value.trim();
    let temps = document.getElementById("temps").value.trim();
    let categorie = document.getElementById("categorie").value.trim();
    let image = document.getElementById("image").value;

    if (nom.length < 6) {
        errors += "- Nom au moins 3 caractères\n";
    }

    if (description.length < 10) {
        errors += "- Description trop courte\n";
    }

    if (etapes.length < 10) {
        errors += "- Étapes insuffisantes\n";
    }

    if (!/^[0-9]+ ?(min)?$/.test(temps)) {
        errors += "- Temps invalide (ex: 20 min)\n";
    }

    if (categorie === "") {
        errors += "- Catégorie obligatoire\n";
    }

    if (image === "") {
        errors += "- Image obligatoire\n";
    }

    if (errors !== "") {
        alert("Erreurs :\n\n" + errors);
        return false;
    }

    return true;
}


// nverifiw bl champ bl champ

function checkNom() {
    let val = document.getElementById("nom").value.trim();
    let msg = document.getElementById("nomMsg");

    if (val.length < 3) {
        msg.textContent = " Min 3 caractères";
        msg.className = "msg error";
        return false;
    }

    msg.textContent = " OK";
    msg.className = "msg success";
    return true;
}

function checkDescription() {
    let val = document.getElementById("description").value.trim();
    let msg = document.getElementById("descMsg");

    if (val.length < 10) {
        msg.textContent = " Trop courte";
        msg.className = "msg error";
        return false;
    }

    msg.textContent = " OK";
    msg.className = "msg success";
    return true;
}

function checkEtapes() {
    let val = document.getElementById("etapes").value.trim();
    let msg = document.getElementById("etapesMsg");

    if (val.length < 10) {
        msg.textContent = " Ajoute plus de détails";
        msg.className = "msg error";
        return false;
    }

    msg.textContent = " OK";
    msg.className = "msg success";
    return true;
}

function checkTemps() {
    let val = document.getElementById("temps").value.trim();
    let msg = document.getElementById("tempsMsg");

    if (!/^[0-9]+ ?(min)?$/.test(val)) {
        msg.textContent = " Format: 20 min";
        msg.className = "msg error";
        return false;
    }

    msg.textContent = " OK";
    msg.className = "msg success";
    return true;
}

function checkCategorie() {
    let val = document.getElementById("categorie").value.trim();
    let msg = document.getElementById("catMsg");

    if (val === "") {
        msg.textContent = " Obligatoire";
        msg.className = "msg error";
        return false;
    }

    msg.textContent = " OK";
    msg.className = "msg success";
    return true;
}

function checkImage() {
    let val = document.getElementById("image").value;
    let msg = document.getElementById("imgMsg");

    if (val === "") {
        msg.textContent = " Choisir une image";
        msg.className = "msg error";
        return false;
    }

    msg.textContent = " OK";
    msg.className = "msg success";
    return true;
}
//ki nenzlou ala chmps lfonct se declenche 

document.addEventListener("DOMContentLoaded", function () {


    let form = document.getElementById("recetteForm");

    if (form) {

        form.addEventListener("submit", function(e) {
            if (!validerFormulaire()) {
                e.preventDefault();
            }
        });

        document.getElementById("nom").addEventListener("keyup", checkNom);
        document.getElementById("description").addEventListener("keyup", checkDescription);
        document.getElementById("etapes").addEventListener("keyup", checkEtapes);
        document.getElementById("temps").addEventListener("keyup", checkTemps);
        document.getElementById("categorie").addEventListener("keyup", checkCategorie);
        document.getElementById("image").addEventListener("change", checkImage);

    } else {
       
    }

});