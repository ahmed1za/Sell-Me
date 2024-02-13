function previous(){
    const widthSlider = document.querySelector(".slider").offsetWidth;
    const sliderContent = document.querySelector(".slider_content");
    sliderContent.scrollLeft -= widthSlider;
    const scrollLeft = sliderContent.scrollLeft;

    console.log(scrollLeft,widthSlider-2);

    if(scrollLeft <=1000 ){
        console.log("on rentre dans la condition")
        document.querySelector('.slider_nav_button--prev').style.display= "none";
        document.querySelector('.slider_nav_button--next').style.display = "block"
    }else {
        document.querySelector('.slider_nav_button--next').style.display = "block"
    }
}

function next(){
    const widthSlider = document.querySelector(".slider").offsetWidth;
    const sliderContent = document.querySelector(".slider_content");
    sliderContent.scrollLeft += widthSlider;
    const scrollLeft = sliderContent.scrollLeft;

    if(scrollLeft >= widthSlider){
        document.querySelector('.slider_nav_button--next').style.display= "none";
    }else {
        document.querySelector('.slider_nav_button--prev').style.display = "block"
    }
}

//////

//création des variables et récupérer les id des éléments
document.addEventListener("DOMContentLoaded", function (){
    nombre = 4;
    position = 0;
    container = document.getElementById("container-carrousel");
    boutonG = document.getElementById("g");
    boutonD = document.getElementById("d");
    //définir la taille des images
    container.style.width = (100 * nombre) + "%";

    //boucle pour récupérer les images et les mettre dans des div
    for (i = 1; i <= nombre; i++) {
        div = document.createElement("div");
        div.className = "photo";
        div.style.backgroundImage = "url('image-sell-me/" + 'im' + i + ".jpg')";
        container.appendChild(div);
    }
    //création de fonction pour défiler le carrousel en cliquant sur les boutons
    boutonD.onclick = function () {
        if (position > -nombre + 1) {
            position--;
            container.style.transform = "translate(" + position * (100/nombre) + "%)";
            container.style.transition = "all 0.5s ease";
            afficherMasquer();
        }
    }
    boutonG.onclick = function () {
        if (position < 0) {
            position++;
            container.style.transform = "translate(" + position * (100/nombre) + "%)";
            container.style.transition = "all 0.5s ease";
            afficherMasquer();
        }
    }

    // Appel de la fonction pour afficher ou masquer les boutons
    afficherMasquer();

    // Ajout de la fonction pour faire défiler les images toutes les 4 secondes
    setInterval(defilerAutomatiquement, 5000);
}
);
// Fonction pour faire défiler les images automatiquement
function defilerAutomatiquement() {
    if (position > -nombre + 1) {
        position--;
        container.style.transform = "translate(" + position * (100/nombre) + "%)";
        container.style.transition = "all 0.5s ease";
        afficherMasquer();
    } else {
        // Si on atteint la dernière image, retourner à la première avec une transition
        position = 0;
        container.style.transform = "translate(" + position * (100/nombre) + "%)";
        container.style.transition = "transform 0.5s ease"; // Transition uniquement pour le retour à la première image
        afficherMasquer();
    }
}



function afficherMasquer() {
    //bouton droit
    if (position === -nombre + 1)
        boutonD.style.visibility = "hidden";
    else
        boutonD.style.visibility = "visible";

    //bouton gauche
    if (position === 0)
        boutonG.style.visibility = "hidden";
    else
        boutonG.style.visibility = "visible";
}


document.addEventListener("DOMContentLoaded", function() {
    var menuItem = document.querySelector('.menu-item');
    var subMenu = menuItem.querySelector('.sub-menu');

    menuItem.addEventListener('click', function() {
        subMenu.classList.toggle('active');
    });
});



function precedent(){
    const widthSlider = document.querySelector(".slider_detail").offsetWidth;
    const sliderContent = document.querySelector(".slider_content_detail");
    sliderContent.scrollLeft -= widthSlider;
    const scrollLeft = sliderContent.scrollLeft;

    console.log(scrollLeft,widthSlider);


}

function suivant(){
    const widthSlider = document.querySelector(".slider_detail").offsetWidth;
    const sliderContent = document.querySelector(".slider_content_detail");
    sliderContent.scrollLeft += widthSlider;
    const scrollLeft = sliderContent.scrollLeft;

}



document.addEventListener('DOMContentLoaded', function () {
    var nature = "{{ produit.vendeur.nature }}";

    if (nature === "particulier") {
        document.querySelector('.contacter-vendeur').disabled = false;
        document.querySelector('.ajouter-panier').disabled = true;
    } else if (nature === "professionnel") {
        document.querySelector('.contacter-vendeur').disabled = true;
        document.querySelector('.ajouter-panier').disabled = false;
    }
});


function masquerMessageFlash() {
    var messageFlash = document.querySelector('.message-flash');

    if (messageFlash) {
        // Ajoutez la classe 'fade-out' pour déclencher la transition de fondu
        messageFlash.classList.add('fade-out');

        // Attendez que la transition soit terminée, puis masquez le message
        setTimeout(function () {
            messageFlash.style.display = 'none';
        }, 2000); // 1000 millisecondes correspondent à la durée de la transition
    }
}

// Appelez la fonction après un certain délai (par exemple, 3000 millisecondes)
setTimeout(masquerMessageFlash, 3000);



    const eventSource = new EventSource("{{ mercure('https://example.com/books/1')|escape('js') }}");
    eventSource.onmessage = event => {
    // Will be called every time an update is published by the server
    console.log(JSON.parse(event.data));
}




