var tbody, tfoot, pageElement, thsTri;
var p=1, nbP, nbMax=15, parametres = [];

window.addEventListener("load", e => {
    tbody = document.querySelector("#tbody-article");
    tfoot = document.querySelector("#tfoot-article");
    pageElement = document.querySelector("#page");
    thsTri = document.querySelectorAll(".th-tri");
    addOnClickButtonPagine();
    addOnClickTri();
    fetchArticles(p,nbMax);
});

function addOnClickButtonPagine(){
    let precedent = document.querySelector("#precedent");
    let suivant = document.querySelector("#suivant");
    precedent.addEventListener("click", e => {
        fetchArticles(p-1,nbMax,parametres);
    });
    suivant.addEventListener("click", e => {
        fetchArticles(p+1,nbMax,parametres);
    });
}

function addOnClickTri(){
    thsTri.forEach(thTri => {
        thTri.addEventListener("click", e => {
            let input = thTri.querySelector("input");
            let icon = thTri.querySelector("i");
            let tabTri = input.value.split(".");
            if(parametres["critere_tri"] === tabTri[0]){
                tabTri[1] = (tabTri[1] === "ASC" ? "DESC" : "ASC");
                input.value = tabTri[0] + "." + tabTri[1];
            }else{
                thsTri.forEach(thT => {
                    thT.querySelector("i").className = "fas fa-sort";
                    thT.querySelector("input").value = thT.querySelector("input").value.split(".")[0] + ".ASC";
                });
            }
            icon.className = "fas fa-sort-" + (tabTri[1] === "ASC" ? "up" : "down");
            parametres["critere_tri"] = tabTri[0];
            parametres["tri_ordre"] = tabTri[1];
            fetchArticles(1,nbMax,parametres);
        });
    });
}

function fetchArticles(page, nbMaxParPage, parametres = []){
    let param = "";
    Object.keys(parametres).forEach(key => {
        param += ("&" + key + "=" + parametres[key]);
    });
    fetch('/admin/article/get?page=' + page + "&nb_max_par_page=" + nbMaxParPage + param)
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = "";
            console.log(data);
            data["articles"].forEach(article => {
                tbody.appendChild(createTrArticle(article));
            });
            paginate(data["pagination"]);
        })
        .catch(e => {
            console.log(e);
        });
}

function paginate(pagination){
    p = parseInt(pagination["page"]);
    nbP = parseInt(pagination["nbPages"]);
    pageElement.textContent = p + "/" + nbP;
    precedent.disabled = false;
    suivant.disabled = false;
    if(p <= 1){
        precedent.disabled = true;
    }else if(p >= nbP){
        suivant.disabled = true;
    }
}

function createTrArticle(article){
    let tr = document.createElement("tr");
    let td1 = document.createElement("td");
    let td2 = document.createElement("td");
    let td3 = document.createElement("td");
    let td4 = document.createElement("td");
    let td5 = document.createElement("td");
    let td6 = document.createElement("td");
    

    td1.textContent = article["id"];
    td2.textContent = article["libelle"];
    let img = document.createElement('img');
    img.src = article["image"];
    img.style.width = "20px";
    td3.appendChild(img);
    td4.textContent = article["description"];
    td5.textContent = article["prix_u"].toFixed(2) + "€";
    let suppBtn = document.createElement("button");
    suppBtn.textContent = 'supprimer';
    suppBtn.addEventListener('click',(event)=>{
        window.location = '/admin/article/'+article["id"]+'/remove';
    });
    let editBtn = document.createElement("button");
    editBtn.textContent = 'editer';
    editBtn.addEventListener('click',(event)=>{
        window.location = '/admin/article/'+article["id"]+'/edit';
    });
    td6.appendChild(suppBtn);
    td6.appendChild(editBtn);

    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);
    tr.appendChild(td5);
    tr.appendChild(td6);
    td1.addEventListener("click", e=> window.location="/admin/article/"+article["id"]);
    td2.addEventListener("click", e=> window.location="/admin/article/"+article["id"]);
    td3.addEventListener("click", e=> window.location="/admin/article/"+article["id"]);
    td4.addEventListener("click", e=> window.location="/admin/article/"+article["id"]);
    td5.addEventListener("click", e=> window.location="/admin/article/"+article["id"]);

    //tr.addEventListener("click", e=> window.location="/admin/article/show/"+article["id"]);

    return tr;
}