var tbody, tfoot, pageElement, p=1, nbP, nbMax=15, parametres = [];

window.addEventListener("load", e => {
    tbody = document.querySelector("#tbody-commandes");
    tfoot = document.querySelector("#tfoot-commandes");
    pageElement = document.querySelector("#page");
    addOnClickButton();
    fetchCommandes(p,nbMax);
});

function addOnClickButton(){
    let precedent = document.querySelector("#precedent");
    let suivant = document.querySelector("#suivant");
    precedent.addEventListener("click", e => {
        fetchCommandes(p-1,nbMax,parametres);
    });
    suivant.addEventListener("click", e => {
        fetchCommandes(p+1,nbMax,parametres);
    });
}

function fetchCommandes(page, nbMaxParPage, parametres = []){
    fetch('/admin/commande/get?page=' + page + "&nb_max_par_page=" + nbMaxParPage)
        .then(response => response.json())
        .then(data => {
            console.log(data);
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