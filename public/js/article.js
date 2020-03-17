window.addEventListener('load', e => {
    addOnClickToPanier();
    setFiltersByParameters(getUrlParameters());
    addOnChangeToFilters();
    setSortingByParameters(getUrlParameters());
    addOnClickToParameters();
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
            window.location = "/article" + getFiltersToUrl();
        });
    });
    categories.forEach(categorie => {
        categorie.addEventListener("change", e => {
            window.location = "/article" + getFiltersToUrl();
        });
    });
    types.forEach(type => {
        type.addEventListener("change", e => {
            window.location = "/article" + getFiltersToUrl();
        });
    });
    tailles.forEach(taille => {
        taille.addEventListener("change", e => {
            window.location = "/article" + getFiltersToUrl();
        });
    });
}

function addOnClickToParameters(){
    let triArticles = document.querySelectorAll("#btn-tri-articles");
    triArticles.forEach(triArticle => {
        triArticle.addEventListener("click", e => {
            if(triArticle.className.includes("actif")){
                let tabValues = triArticle.value.split(".");
                triArticle.value = tabValues[0] + "." + (tabValues[1] === "ASC" ? "DESC" : "ASC");
                window.location = "/article" + getFiltersToUrl();
            }else{
                triArticles.forEach(triArt => {
                    triArt.className = (triArt === triArticle ? (triArt.className + " actif") : triArt.className.replace(" actif",""));
                    window.location = "/article" + getFiltersToUrl();
                });
            }
        });
    });
}

function getFiltersToUrl(){
    let url = "";
    let sections = document.querySelectorAll(".input-section");
    let categories = document.querySelectorAll(".input-categorie");
    let types = document.querySelectorAll(".input-type");
    let tailles = document.querySelectorAll(".input-taille");
    let prixMin = document.querySelector("#input-prix-min");
    let prixMax = document.querySelector("#input-prix-max");

    let triArticles = document.querySelectorAll("#btn-tri-articles");

    triArticles.forEach(triArticle => {
        if(triArticle.className.includes("actif")){
            let tabValues = triArticle.value.split(".");
            url += ("&critere_tri=" + tabValues[0] + "&tri_ordre=" + tabValues[1]);
        }
    });
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

    let prix = "";
    if(prixMin.value && prixMin.value != ""){
        if(prixMax.value && prixMax.value != ""){
            prix = prixMin.value + "_" + prixMax.value;
        }else{
            prix = prixMin.value + "_999";
        }
    }else{
        if(prixMax.value && prixMax.value != ""){
            prix =  "999_" + prixMax.value;
        }else{
            prix = null;
        }
    }

    if(prix){
        url += "&prix_entre=" + prix;
    }

    return "?" + url.substring(1);
}

function setFiltersByParameters(parameters){
    let sections = document.querySelector(".filtre-sections");
    let categories = document.querySelector(".filtre-categories");
    let types = document.querySelector(".filtre-types");
    let tailles = document.querySelector(".filtre-tailles");
    
    if(parameters['sections']){
        parameters['sections'].forEach(s => {
            sections.querySelector("#section-" + s).checked = true;
        })
    }
    if(parameters['categories']){
        parameters['categories'].forEach(s => {
            categories.querySelector("#categorie-" + s).checked = true;
        })
    }
    if(parameters['types']){
        parameters['types'].forEach(s => {
            types.querySelector("#type-" + s).checked = true;
        })
    }
    if(parameters['tailles']){
        parameters['tailles'].forEach(s => {
            tailles.querySelector("#taille-" + s).checked = true;
        })
    }
}

function setSortingByParameters(parameters){
    let triArticles = document.querySelectorAll("#btn-tri-articles");
    if(parameters['critere_tri']){
        let critere = parameters['critere_tri'];
        let order = 'ASC';
        if(parameters['tri_ordre']){
            order = parameters['tri_ordre'];
        }
        triArticles.forEach(triArticle => {
            let tabValues = triArticle.value.split(".");
            if(tabValues[0].toUpperCase() === critere.toUpperCase()){
                triArticle.className = triArticle.className + " actif";
                triArticle.value = critere + "." + order;
                let icon = triArticle.querySelector("i");
                icon.className = "fas fa-sort-" + (order === 'ASC' ? "up" : "down")
                console.log(icon);
            }
        })
    }
}

function getUrlParameters() {
    var parameters = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        if(key.includes("[]")){
            if(!parameters[key.replace("[]","")]){
                parameters[key.replace("[]","")] = [];
            }
            parameters[key.replace("[]","")].push(value);
        }else{
            parameters[key] = value;
        }
    });
    return parameters;
}
