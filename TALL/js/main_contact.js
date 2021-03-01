$(document).ready(function(){
    
    var $img = $('#carrousel img'), // on cible les images contenues dans le carrousel
        indexImg = $img.length - 1, // on définit l'index du dernier élément
        i = 0, // on initialise un compteur
        $currentImg = $img.eq(i); // enfin, on cible l'image courante, qui possède l'index i (0 pour l'instant)
    
    $img.css('display', 'none'); // on cache les images
    $currentImg.css('display', 'block'); // on affiche seulement l'image courante
    $img.css({"border-radius":"10px",
    "box-shadow": "0 0 5px #000"
            });

    $('#carrousel ul').append('<div class="controls"> <button class="prev">Précédente</button> <button class="next">Suivante</button> </div>');
    $('.controls').css({
        "display": "flex",
        "justify-content": "center",
        "align-items": "center"
    });
    $('.prev, .next').css({
        "background-color": "rgba(70,154,185,1)",
        "padding": "15px 28px",
        "margin": "20px",
        "border-radius": "8px",
        "width": "150px",
        "color" : "white",
        "font-family": "'Roboto Mono', monospace"
    });

    $('.prev, .next').hover(function(){
        $(this).css({ //premier fonction qui défini l'état au survol
            "padding": "15px 28px",
            "margin": "20px",
            "border-radius": "8px",
            "width": "150px",
            "font-family": "'Roboto Mono', monospace",
            "transform": "scale(1.06)", 
            "transition": "0.2s all"
        })},
        function(){ // deuxième fonction qui définir l'état après le survole
            $(this).css({
                "background-color": "rgba(70,154,185,1)",
                "padding": "15px 28px",
                "margin": "20px",
                "border-radius": "8px",
                "width": "150px",
                "transform": "scale(1)",
                "color" : "white",
                "font-family": "'Roboto Mono', monospace"
            });
      }); 
    
    $('.next').click(function(){ // image suivante
    
        i++; // on incrémente le compteur
    
        if( i <= indexImg ){
            $img.css('display', 'none'); // on cache les images
            $currentImg = $img.eq(i); // on définit la nouvelle image
            $currentImg.css('display', 'block'); // puis on l'affiche
        }
        else{
            i = indexImg;
        }
    
    });
    
    $('.prev').click(function(){ // image précédente
    
        i--; // on décrémente le compteur, puis on réalise la même chose que pour la fonction "suivante"
    
        if( i >= 0 ){
            $img.css('display', 'none');
            $currentImg = $img.eq(i);
            $currentImg.css('display', 'block');
        }
        else{
            i = 0;
        }
    
    });
    
    function slideImg(){
        setTimeout(function(){ // on utilise une fonction anonyme
                            
            if(i < indexImg){ // si le compteur est inférieur au dernier index
            i++; // on l'incrémente
        }
        else{ // sinon, on le remet à 0 (première image)
            i = 0;
        }
    
        $img.css('display', 'none');
    
        $currentImg = $img.eq(i);
        $currentImg.css('display', 'block');
    
        slideImg(); // on oublie pas de relancer la fonction à la fin
    
        }, 7000); // on définit l'intervalle à 7000 millisecondes (7s)
    }
    
    slideImg(); // enfin, on lance la fonction une première fois
    
});
    