
var index = 0;


window.onload = () => {
    let addTailleButton = document.createElement("button");
    addTailleButton.className = "add_taille_link";
    addTailleButton.textContent = "Ajouter une taille";
    
    let newLinkLi = document.createElement("li");
    newLinkLi.appendChild(addTailleButton);

    let collectionHolder = document.querySelector("ul.qte-taille");

    let liInput = collectionHolder.querySelectorAll('li');
    index = liInput.length;

    liInput.forEach(element => {
        addTailleFormDeleteLink(element);
    });

    collectionHolder.appendChild(newLinkLi);



    addTailleButton.addEventListener("click", e => {
        addTailleForm(collectionHolder, newLinkLi);
    });
}

function addTailleForm(collectionHolder, newLinkLi){
    let prototype = collectionHolder.getAttribute("data-prototype");

    let newForm = prototype.replace(/__name__/g, index);

    index++;

    let newFormLi = document.createElement("li");
    newFormLi.innerHTML += newForm;

    addTailleFormDeleteLink(newFormLi);

    collectionHolder.insertBefore(newFormLi, newLinkLi);
}

function addTailleFormDeleteLink(tailleFormLi){
    let removeFormButton = document.createElement("button");
    removeFormButton.textContent = "Remove";

    removeFormButton.addEventListener("click", e => {
        tailleFormLi.remove();
    });

    tailleFormLi.appendChild(removeFormButton);
}
