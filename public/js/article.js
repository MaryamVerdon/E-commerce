window.onload = () => {
    console.log("ok");
    document.querySelector(".item-article").addEventListener("click", e => {
        let id = document.querySelector(".item-article").document.querySelector(".id-article").value;
        console.log("ok");
        window.location = "/article/" + id;
    });
};