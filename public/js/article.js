window.addEventListener('load', e => {
    addOnClickToPanier();
});

function addOnClickToPanier(){
    let articles = document.querySelectorAll(".item-article");
    articles.forEach(article => {
        let button = article.querySelector(".add-article-panier");
        let id = article.querySelector("#id-article").value;
        let selectTaille = article.querySelector("#taille-article");
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
    });
}

function updatePanierSize(size){
    let sizePanier = document.querySelector(".size-panier")
    sizePanier.textContent = size > 0 ? size : "";
}