$(document).ready(function(){ // fonction qui permet de lancer les autres fonction jquery si la page c'est chargé correctement

    $('#choix_commune').on('change',function(e){
        e.preventDefault(); // on empêche le bouton d'envoie d'envoyer le formulaire
        // récupération de la valeur de la commune choisie
        var commune = $('#choix_commune').val();
        console.log(commune)
        
        $.ajax({
            url : "script_php_jquery/rue_jquery.php", // on donne l'URL du fichier de traitement
            type : "POST", // la requête est de type POST
            dataType : "html",
            data : 'commune=' + commune,
            success : function(code_html, success){
                $("#choix_adresse").html(code_html)
                // console.log(data)
            },
            error : function(resultat, statut, error){
                console.log(error)
            },
            complete : function(resultat, statut){

            }

        });

    });

    $('#choix_adresse').on('change',function(e){
        e.preventDefault(); // on empêche le bouton d'envoie d'envoyer le formulaire
        // récupération de la valeur de la commune choisie
        var rue = $('#choix_adresse').val();
        // console.log(rue)
        var commune = $('#choix_commune').val();
        // console.log(commune)
        
        $.ajax({
            url : "script_php_jquery/numero_jquery.php", // on donne l'URL du fichier de traitement
            type : "POST", // la requête est de type POST
            dataType : "html",
            data : 'rue=' + rue + '&commune=' + commune,
            success : function(code_html, success){
                $("#choix_numero").html(code_html)
                // console.log(code_html)
            },
            error : function(resultat, statut, error){
                console.log(error)
            },
            complete : function(resultat, statut){

            }

        });

    });
    $('#choix_numero').on('change',function(e){
        e.preventDefault(); // on empêche le bouton d'envoie d'envoyer le formulaire
        // récupération de la valeur de la commune choisie
        var rue = $('#choix_adresse').val();
        console.log(rue)
        var commune = $('#choix_commune').val();
        console.log(commune)
        var numero = $('#choix_numero').val();
        console.log(numero)
        
        $.ajax({
            url : "script_php_jquery/rep_jquery.php", // on donne l'URL du fichier de traitement
            type : "POST", // la requête est de type POST
            dataType : "html",
            data : 'rue=' + rue + '&commune=' + commune + '&numero=' + numero,
            success : function(code_html, success){
                $("#choix_rep").html(code_html)
                // console.log(code_html)
            },
            error : function(resultat, statut, error){
                console.log(error)
            },
            complete : function(resultat, statut){

            }

        });

    });
});


// Fonction permettant la navigation entre les onglets du menu vertical
// Spoiler
$(document).ready(function() {
    $("#config").click(function() {
        $("#confignav").slideToggle("normal");
    });
});


// PopUp Cuadro
$("#abrir_box").click(function(){
    $("#cont_box").css("display", "block");
  });

  $("#cerrar_box").click(function(){
    $("#cont_box").css("display", "none");
  }); 

//Menu Tabla
$(document).ready(function() {
    $(".tabs-menu a").click(function(event) {
        event.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        var tab = $(this).attr("href");
        $(".tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });
});