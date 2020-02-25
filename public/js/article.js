window.addEventListener('load', e => {
    addOnClickToPanier();
    addOnChangeToFilters();
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

function addOnChangeToFilters(){
    let sections = document.querySelectorAll(".input-section");
    let categories = document.querySelectorAll(".input-categorie");
    let types = document.querySelectorAll(".input-type");
    let tailles = document.querySelectorAll(".input-taille");
    sections.forEach(section => {
        section.addEventListener("change", e => {
            console.log(getFiltersToUrl());
        });
    });
    categories.forEach(categorie => {
        categorie.addEventListener("change", e => {
            console.log(getFiltersToUrl());
        });
    });
    types.forEach(type => {
        type.addEventListener("change", e => {
            console.log(getFiltersToUrl());
        });
    });
    tailles.forEach(taille => {
        taille.addEventListener("change", e => {
            console.log(getFiltersToUrl());
        });
    });
}

function getFiltersToUrl(){
    let url = "";
    let sections = document.querySelectorAll(".input-section");
    let categories = document.querySelectorAll(".input-categorie");
    let types = document.querySelectorAll(".input-type");
    let tailles = document.querySelectorAll(".input-taille");
    sections.forEach(section => {
        if(section.checked){
            url += ("&sections[]=" + section.value);
        }
    });
    categories.forEach(categorie => {
        if(categorie.checked){
            url += ("&categories[]=" + categorie.value);
        }
    });
    types.forEach(type => {
        if(type.checked){
            url += ("&types[]=" + type.value);
        }
    });
    tailles.forEach(taille => {
        if(taille.checked){
            url += ("&tailles[]=" + taille.value);
        }
    });
    return url;
}