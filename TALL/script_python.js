console.log("le fichier script python js est ouvert")

var commune 
var assoEquip

$("#btn_potentiel").on("click", function(){
    // récupération des valeurs du formulaire
    commune = $('#choix_commune2').val();
    assoEquip = $('#choix_type_equip').val();
    console.log(commune)
    console.log(assoEquip)

    function testContenu(){
        if( commune === null || commune ==='' || commune ==='Commune' && assoEquip === null || assoEquip ==='' || assoEquip ==='Choississez un des items'){
            console.log("le champ est vide");
            // si un des champs est mal rempli un message d'avertissement s'affiche
            $("#potentiel").after("<div id='avertissement_message'>Un des champs est mal renseigné. Veuillez recommencer.</div>")
        }else{
            // si les champs sont bien remplis, on supprime l'avertissement.
            $("#avertissement_message").remove();
            console.log("Le champ est rempli");
            executionScriptPython()
        }
    }

    testContenu();
});

// création d'une fonction qui execute le script php contenant le script python
function executionScriptPython(){
    
console.log("je suis dans la fonction");

// récupération des valeurs du formulaire
commune = $('#choix_commune2').val();
assoEquip = $('#choix_type_equip').val();

// je vérifie que la div ayant pour id resultat_calcul soit caché, sinon je la cache.
try{
    $("#resultat_calcul").hide();
}catch{
    console.log("l'élément n'a pas encore été découvert");
}

$("#loading").show(); // affiche l'espace de chargement
$.ajax({
    url : "generateur_potentialite.php", // on donne l'URL du fichier de traitement
    type : 'POST',
    async : 'true',
    data : {'code_insee' : commune,
            'equipement' : assoEquip,
    },
    success : function(code, success){
        console.log("c'est parti")
    },
    error : function(resultat, statut, error){
        console.log("il ya une erreur")
    },
    complete : function(resultat, statut){
        console.log("c'est fini")
        $("#loading").hide(); // cache l'espace de chargement à la fin du process
        $("#resultat_calcul").show(); // fait apparaitre le texte de fin dans l'id resultat_calcul
    }
    });

};