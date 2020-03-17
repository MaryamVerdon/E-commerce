var tbody, tfoot, pageElement, thsTri;
var p=1, nbP, nbMax=15, parametres = [];

window.addEventListener("load", e => {
    tbody = document.querySelector("#tbody-clients");
    tfoot = document.querySelector("#tfoot-clients");
    pageElement = document.querySelector("#page");
    thsTri = document.querySelectorAll(".th-tri");
    addOnClickButtonPagine();
    addOnClickTri();
    fetchClients(p,nbMax);
});

function addOnClickButtonPagine(){
    let precedent = document.querySelector("#precedent");
    let suivant = document.querySelector("#suivant");
    precedent.addEventListener("click", e => {
        fetchClients(p-1,nbMax,parametres);
    });
    suivant.addEventListener("click", e => {
        fetchClients(p+1,nbMax,parametres);
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
            fetchClients(1,nbMax,parametres);
        });
    });
}

function fetchClients(page, nbMaxParPage, parametres = []){
    let param = "";
    Object.keys(parametres).forEach(key => {
        param += ("&" + key + "=" + parametres[key]);
    });
    fetch('/admin/client/get?page=' + page + "&nb_max_par_page=" + nbMaxParPage + param)
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = "";
            data["clients"].forEach(client => {
                tbody.appendChild(createTrClient(client));
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
    }
    if(p >= nbP){
        suivant.disabled = true;
    }
}

function createTrClient(client){
    let tr = document.createElement("tr");
    let td1 = document.createElement("td");
    let td2 = document.createElement("td");
    let td3 = document.createElement("td");
    let td4 = document.createElement("td");

    td1.textContent = client["id"];
    td2.textContent = client["nom"];
    td3.textContent = client["prenom"];
    td4.textContent = client["email"];

    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);

    tr.addEventListener("click", e=> window.location="/admin/client/"+client["id"]);

    return tr;
}