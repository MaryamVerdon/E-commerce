var navbar = document.querySelector(".navbar-bar");
var sticky = navbar.offsetTop;

window.addEventListener('load', e => {
    updatePanierSizeIndex();
    addMouseMovePresentoir();
});

window.addEventListener("scroll", e => {
    if(window.pageYOffset >= sticky){
        navbar.classList.add("sticky");
    }else{
        navbar.classList.remove("sticky");
    }
})



function updatePanierSizeIndex(){
    fetch("/panier/size")
        .then(response => response.json())
        .then(data => {
            let size = data['size'];
            let sizePanier = document.querySelector(".size-panier")
            sizePanier.textContent = size > 0 ? size : "";
        });
}

function addMouseMovePresentoir(){
    let isDown = false, startX, scrollLeft;
    let presentoirs = document.querySelectorAll(".presentoir-all");
    presentoirs.forEach(present => {
        present.addEventListener("mousedown", e => {
            isDown = true;
            present.classList.add('active');
            startX = e.pageX - present.offsetLeft;
            scrollLeft = present.scrollLeft;
        });
        present.addEventListener('mouseleave', e => {
            isDown = false;
            present.classList.remove('active');
        });
        present.addEventListener('mouseup', e => {
            isDown = false;
            present.classList.remove('active');
        });
        present.addEventListener('mousemove', e => {
            if(!isDown) return;
            let x = e.pageX - present.offsetLeft;
            let walk = (x - startX) * 3;
            present.scrollLeft = scrollLeft - walk;
        });
    });
}