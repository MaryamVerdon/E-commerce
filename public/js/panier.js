window.addEventListener('load', e => {
    addOnChangeQte();
});

function addOnChangeQte(){
    var selectsQte = document.querySelectorAll(".qte-article");

    selectsQte.forEach(selectQte => {
        selectQte.addEventListener("change", e => {
            qte = selectQte.options[selectQte.selectedIndex].value;
            tr = selectQte.parentElement.parentElement;
            id = tr.querySelector("#id-article").value;
            idTaille = tr.querySelector("#id-taille").value;
            console.log(qte,id,idTaille);

            fetch("/panier/modify/" + id + "?taille=" + idTaille + "&quantite=" + qte)
                .then(response => {
                    window.location="/panier";
                });
        });
    });
}

