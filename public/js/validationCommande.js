window.addEventListener('load', e =>{
    addNewAdresse();
    button = document.querySelector('#valide');
    console.log(button);
    button.addEventListener('click',()=>{
        onclickValidePanier();
    });
});

function addNewAdresse(){
    selectAdresse = document.querySelector('#adresse');
    formNewAdresse = document.querySelector('.form-new-adresse');
    if(selectAdresse.options[selectAdresse.selectedIndex].value === 'new'){
        formNewAdresse.style.display = block;
    }
    selectAdresse.addEventListener("change", e => {
        if(selectAdresse.options[selectAdresse.selectedIndex].value === 'new'){
            formNewAdresse.style.display = "block";
        }else{
            formNewAdresse.style.display = "none";
        }
    });
}

function onclickValidePanier(){
    selectAdresse = document.querySelector('#adresse');
    valeur = selectAdresse.options[selectAdresse.selectedIndex].value;
    
    window.location='/commande/new?adresse='+valeur;
}

