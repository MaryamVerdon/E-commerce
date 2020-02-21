window.addEventListener('load', e => {
    addOnClickToPanier();
});

function addOnClickToPanier(){
    let articles = document.querySelectorAll(".item-article");
    articles.forEach(article => {
        let button = article.querySelector(".add-article-panier");
        let id = article.querySelector("#id-article").value;
        button.addEventListener("click", e => {
            fetch("/panier/add/" + id)
                .then(response => response.json())
                .then(data => updatePanierSize(data['size']));
        });
    });
}

function updatePanierSize(size){
    let sizePanier = document.querySelector(".size-panier")
    sizePanier.textContent = size > 0 ? size : "";
}