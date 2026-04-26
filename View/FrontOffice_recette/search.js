

document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("search");
    const resultDiv = document.getElementById("resultats");

    searchInput.addEventListener("keyup", function () {

        let query = this.value;//li labed ketbou

        // ken champ vide /vider résultats
        if (query.length < 1) {
    location.reload(); // recharge tt les recettes
    return;
}

       //rb var
       let rb = new XMLHttpRequest(); //hehdi ajax bch twali mnghir le tenzl entrer 
rb.open("POST", "search.php", true);
rb.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

rb.onload = function () {
    if (this.status === 200) {
        resultDiv.innerHTML = this.responseText;
    }
};

rb.send("query=" + encodeURIComponent(query));//enco: evite bugs al espc/carac spc

    });

});