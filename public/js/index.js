window.addEventListener('load', e => {
    updatePanierSizeIndex();
});


function updatePanierSizeIndex(){
    fetch("/panier/size")
        .then(response => response.json())
        .then(data => {
            let size = data['size'];
            let sizePanier = document.querySelector(".size-panier")
            sizePanier.textContent = size > 0 ? size : "";
        });
}