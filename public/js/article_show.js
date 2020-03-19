function addOnpanier(){
    let button = document.querySelector(".add-article-panier2");
        console.log(button);
        let id = document.querySelector("#id-article").value;
        console.log(id);
        let selectTaille = document.querySelector("#taille-article");
        console.log(selectTaille);
        button.addEventListener("click", e => {
            idTaille = selectTaille.options[selectTaille.selectedIndex].value;
            fetch("/panier/add/" + id + "?taille=" + idTaille)
                .then(response => {
                    if(response.status === 282){
                        alert("Pas assez de quantité pour l'article");
                        return;
                    }
                    return response.json()
                })
                .then(data => {
                    if(data)updatePanierSize(data['size'])
                });
        });
}

function updatePanierSize(size){
    let sizePanier = document.querySelector(".size-panier")
    sizePanier.textContent = size > 0 ? size : "";
}

window.addEventListener('load', e => {
    addOnpanier();
});