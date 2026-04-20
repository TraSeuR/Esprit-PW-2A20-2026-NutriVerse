// Vérification champ par champ uniquement

function checkNom() {
    let val = document.getElementById("nom").value.trim();
    let msg = document.getElementById("nomMsg");

    if (val.length < 6) {
        msg.textContent = " Min 6 caractères";
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
    let val = document.getElementById("categorie").value;
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

function validerTout() {
    let ok = true;

    if (!checkNom()) ok = false;
    if (!checkDescription()) ok = false;
    if (!checkEtapes()) ok = false;
    if (!checkTemps()) ok = false;
    if (!checkCategorie()) ok = false;
    if (!checkIngredients()) ok = false;

    // ✅ détecter mode modification
    let isEdit = document.querySelector("input[name='id']") !== null;

    // ✅ image obligatoire seulement en ajout
    if (!isEdit) {
        if (!checkImage()) ok = false;
    }

    return ok;
}


// DOM chargé
document.addEventListener("DOMContentLoaded", function () {

    let form = document.getElementById("recetteForm");

    if (form) {

        form.addEventListener("submit", function(e) {

            if (!validerTout()) {
                e.preventDefault();

                // message global (optionnel mais pro)
                let globalMsg = document.getElementById("formError");
                if (globalMsg) {
                    globalMsg.textContent = "Veuillez corriger les erreurs dans le formulaire";
                }

                // scroll vers le haut
                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });
                
            }

        });

        document.getElementById("nom").addEventListener("keyup", checkNom);
        document.getElementById("description").addEventListener("keyup", checkDescription);
        document.getElementById("etapes").addEventListener("keyup", checkEtapes);
        document.getElementById("temps").addEventListener("keyup", checkTemps);
        document.getElementById("categorie").addEventListener("keyup", checkCategorie);
        document.getElementById("image").addEventListener("change", checkImage);

    }

});

let deleteId = null;

function confirmDelete(id) {
    deleteId = id;
    document.getElementById("confirmBox").classList.remove("hidden");
}

document.getElementById("confirmYes").onclick = function () {
    window.location.href = "delete.php?id=" + deleteId;
};

document.getElementById("confirmNo").onclick = function () {
    document.getElementById("confirmBox").classList.add("hidden");
};
function showMessage(type) {

    let text = "";

    if (type === "ajout") {
        text = "Recette ajoutée ✔";
    }

    if (type === "update") {
        text = "Recette mise à jour ✔";
    }

    if (type === "delete") {
        text = "Recette supprimée ✔";
    }

    let box = document.getElementById("successBox");
    let msg = document.getElementById("successText");

    msg.innerText = text;

    box.classList.remove("hidden");

    setTimeout(() => {
        box.classList.add("hidden");
    }, 2000);
}


function goHome() {
    window.location.href = "admin.php";
}