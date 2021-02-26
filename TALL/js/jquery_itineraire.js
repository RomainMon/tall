$(document).ready(function(){ // fonction qui permet de lancer les autres fonction jquery si la page c'est chargé correctement

    $("#itineraire_button").on('click', function(e){
        e.preventDefault(); // on empêche le bouton d'envoie d'envoyer le formulaire
        // récupération de la valeur de la commune choisie
        var commune = $('#choix_commune').val();
        // console.log(commune)

        $.ajax({
            url : "itineraire/longueur.php", // on donne l'URL du fichier de traitement
            type : "POST", // la requête est de type POST
            dataType : "html",
            // data : "lat="+lat+"&lng="+lng,
            success : function(code_html, success){
                $("#test_iti").html(code_html)
                console.log(code_html)
            },
            error : function(resultat, statut, error){
                console.log(error)
            },
            complete : function(resultat, statut){

            }

        });

    });
});