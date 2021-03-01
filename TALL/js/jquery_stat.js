$(document).ready(function(){ // fonction qui permet de lancer les autres fonction jquery si la page c'est chargé correctement

    $('#choix_asso_equip').on('change',function(e){
        e.preventDefault(); // on empêche le bouton d'envoie d'envoyer le formulaire
        // récupération de la valeur de la commune choisie
        var commune = $('#choix_commune').val();
        var assoEquip = $('#choix_asso_equip').val();
        console.log(commune)
        console.log(assoEquip)
        
        if (assoEquip == 'association'){
            $.ajax({
                url : "statistique/stat_asso_commune.php", // on donne l'URL du fichier de traitement
                type : "POST", // la requête est de type POST
                dataType : "html",
                data : 'commune=' + commune,
                success : function(code_html, success){
                    $(".nom_cate").html(code_html)
                    console.log(code_html)
                },
                error : function(resultat, statut, error){
                    console.log(error)
                },
                complete : function(resultat, statut){

                }

            });}
        else {
            $.ajax({
                url : "statistique/stat_equip_commune.php", // on donne l'URL du fichier de traitement
                type : "POST", // la requête est de type POST
                dataType : "html",
                data : 'commune=' + commune,
                success : function(code_html, success){
                    $(".nom_cate").html(code_html)
                    console.log(code_html)
                },
                error : function(resultat, statut, error){
                    console.log(error)
                },
                complete : function(resultat, statut){

                }

            });

        }


    })

        






})