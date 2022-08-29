// Pour afficher la dialogBox nous aurons besoin de 3 fonctions
    // une fonction qui affiche la boite de dialogue. -> showDialogBox()
        // Cette fonction est appelée au click sur les boutons Reset ou Delete
    // une fonction qui masque/ferme la boite de dialogue -> hideDialogBox()
    // une fonction pour traiter la réponse de l'utilisateur -> isConfirmed()

// nous aurons besoin de récupérer l'id du bouton clické lors de l'appel de fonction...
// ... et le transmettre entre les fonctions, afin de savoir quoi faire selon la réponse de l'utilisateur

// pour cela nous déclarons un variable globale 'btnId' 
var btnId;

// on initialise la variable avec une valeur vide
btnId = "";



function showDialogBox() {
    // on rend le div container de la dialogBox (div.overlay) visible
    document.getElementById("overlay").hidden = false;

    // On crée un eventListener pour déterminer quel bouton a été cliqué (reset ou delete)
    document.addEventListener('click', (e) =>{
        // on retrouve l'id de l'élement cliqué (btnReset ou btnDelete)
        let elementId = e.target.id;
    //     // si l'élément a bien un id
        if (elementId !== ''){
            // on affecte l'id (elementId) à notre variable globale (btnId)
            btnId = elementId;
        }
        
    });
  }

    // answer est la réponse de l'utilisateur (passée à la fonction dans la définition onclick des boutons oui/non (cf. partials/dialogBox.html.twig).
    // oui renvoie true, non renvoie false
  function isConfirmed(answer) {
    // si not answer (on a cliqué sur non donc answer est false)
    if (!answer) {
        // on ferme la dialogBox
        closeDialogBox();
    } else { // sinon la réponse est oui (donc on reset ou on delete selon le bouton cliqué au départ, que l'on a stocké à l'ouverture de la dialgoBox dans btnId)
        
        // on récupère l'attribut href du bouton cliqué (reset ou delete) grace à l'id de ce dernier (btnId) dans une variable temporaire 'destination'
        destination = document.getElementById(btnId).getAttribute('href');
        
        // on redirige vers l'exécution normale de l'action demandée (reset ou delete) en attribuant destination à location.href (js pour indiquer la destination d'un lien par ex.)
        location.href = destination;
    }
    // on a fini avec la dialogBox, on la ferme en appelant la fonction closeDialogBox()
    closeDialogBox();
  }


  function closeDialogBox() {
    document.getElementById("overlay").hidden = true;
  }


