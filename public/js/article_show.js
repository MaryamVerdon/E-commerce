function addOnpanier(){
    let button = document.querySelector(".add-article-panier");
        console.log(button);
    let tailles = document.querySelectorAll("input[name=taille]");
    let id = document.querySelector("#id-article").value;
        button.addEventListener("click", e => {
            tailles.forEach(taille => {
                //console.log(taille.value);
                if(taille.checked == true){
                    fetch("/panier/add/" + id + "?taille=" + taille.value)
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
                }
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