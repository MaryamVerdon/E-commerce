
var index = 0;


window.onload = () => {
    let addLigneButton = document.createElement("button");
    addLigneButton.className = "add_ligne_link";
    addLigneButton.textContent = "Ajouter une ligne";
    
    let newLinkLi = document.createElement("li");
    newLinkLi.appendChild(addLigneButton);

    let collectionHolder = document.querySelector("ul.lignes-de-commande");

    let liInput = collectionHolder.querySelectorAll('li');
    index = liInput.length;

    liInput.forEach(element => {
        addLigneFormDeleteLink(element);
    });

    collectionHolder.appendChild(newLinkLi);



    addLigneButton.addEventListener("click", e => {
        addLigneForm(collectionHolder, newLinkLi);
    });
}

function addLigneForm(collectionHolder, newLinkLi){
    let prototype = collectionHolder.getAttribute("data-prototype");

    let newForm = prototype.replace(/__name__/g, index);

    index++;

    let newFormLi = document.createElement("li");
    newFormLi.innerHTML += newForm;

    addLigneFormDeleteLink(newFormLi);

    collectionHolder.insertBefore(newFormLi, newLinkLi);
}

function addLigneFormDeleteLink(ligneFormLi){
    let removeFormButton = document.createElement("button");
    removeFormButton.textContent = "Remove";

    removeFormButton.addEventListener("click", e => {
        ligneFormLi.remove();
    });

    ligneFormLi.appendChild(removeFormButton);
}
