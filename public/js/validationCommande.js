window.addEventListener('load', e =>{
    addNewAdresse();
    button = document.querySelector('#valide');
    button.addEventListener('click',()=>{
        onclickValidePanier();
    });
});

function addNewAdresse(){
    selectAdresse = document.querySelector('#adresse');
    formNewAdresse = document.querySelector('.form-new-adresse');
    button = document.querySelector('#valide');
    if(selectAdresse.options[selectAdresse.selectedIndex].value === 'new'){
        formNewAdresse.style.display = "block";
        button.disabled = "true";
    }
    selectAdresse.addEventListener("change", e => {
        if(selectAdresse.options[selectAdresse.selectedIndex].value === 'new'){
            formNewAdresse.style.display = "block";
            button.disabled = true;
        }else{
            formNewAdresse.style.display = "none";
            button.disabled = false;
            console.log("ok");
        }
    });
}

function onclickValidePanier(){
    selectAdresse = document.querySelector('#adresse');
    valeur = selectAdresse.options[selectAdresse.selectedIndex].value;
    
    window.location='/commande/new?adresse='+valeur;
}

