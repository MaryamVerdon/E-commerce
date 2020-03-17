var tbody, tfoot, pageElement, thsTri;
var p=1, nbP, nbMax=15, parametres = [];

window.addEventListener("load", e => {
    tbody = document.querySelector("#tbody-commandes");
    tfoot = document.querySelector("#tfoot-commandes");
    pageElement = document.querySelector("#page");
    thsTri = document.querySelectorAll(".th-tri");
    addOnClickButtonPagine();
    addOnClickTri();
    fetchCommandes(p,nbMax);
});

function addOnClickButtonPagine(){
    let precedent = document.querySelector("#precedent");
    let suivant = document.querySelector("#suivant");
    precedent.addEventListener("click", e => {
        fetchCommandes(p-1,nbMax,parametres);
    });
    suivant.addEventListener("click", e => {
        fetchCommandes(p+1,nbMax,parametres);
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
            fetchCommandes(1,nbMax,parametres);
        });
    });
}

function fetchCommandes(page, nbMaxParPage, parametres = []){
    let param = "";
    Object.keys(parametres).forEach(key => {
        param += ("&" + key + "=" + parametres[key]);
    });
    fetch('/admin/commande/get?page=' + page + "&nb_max_par_page=" + nbMaxParPage + param)
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = "";
            data["commandes"].forEach(commande => {
                tbody.appendChild(createTrCommande(commande));
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

function createTrCommande(commande){
    let tr = document.createElement("tr");
    let td1 = document.createElement("td");
    let td2 = document.createElement("td");
    let td3 = document.createElement("td");
    let td4 = document.createElement("td");
    let td5 = document.createElement("td");
    let td6 = document.createElement("td");

    td1.textContent = commande["id"];
    let date = new Date(commande["date"]["date"]);
    td2.textContent = date.getDate() + "/" + date.getMonth() + "/" + date.getFullYear();
    td3.textContent = commande["client"];
    td4.textContent = commande["statut_commande"];
    td5.textContent = commande["nb_articles"];
    td6.textContent = commande["total"].toFixed(2) + "€";

    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);
    tr.appendChild(td5);
    tr.appendChild(td6);

    tr.addEventListener("click", e=> window.location="/admin/commande/show/"+commande["id"]);

    return tr;
}