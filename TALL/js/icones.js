var videIcon =  L.icon({
    iconUrl: 'img/vide.png',//Chemin de l'image 
    iconSize:     [23, 23], // Taille de l'icone
    iconAnchor:   [11.5, 11.5], // Point d'insertion de l'icone
    popupAnchor:  [-3, -15], // Point d'insertion de la popup 
});

function zoomIcon(icon, currentZoom){
    return new L.icon({
    iconUrl: 'img/'+icon+'.png',//Chemin de l'image 
    iconSize:     zoom[currentZoom].iconSize, // Taille de l'icone
    iconAnchor:   zoom[currentZoom].iconAnchor, // Point d'insertion de l'icone
    popupAnchor:  [-3, -15], // Point d'insertion de la popup  
});
}


// liste des equipements et des catégories associations (il faudrait les récupérer dans la page html pour renvoyer les listes toujours actualisées)
var listeIconEquip = ['AMAP','compost','007070','007075','020025','030050','024000']
var zoom = { 0 :{iconSize : [5, 5],iconAnchor:[2.5, 2.5]},
             1 :{iconSize : [5, 5],iconAnchor:[2.5, 2.5]},
             2 :{iconSize : [5, 5],iconAnchor:[2.5, 2.5]},
             3 :{iconSize : [5, 5],iconAnchor:[2.5, 2.5]},
             4 :{iconSize : [5, 5],iconAnchor:[2.5, 2.5]},
             5 :{iconSize : [5, 5],iconAnchor:[2.5, 2.5]},
             6 :{iconSize : [5, 5],iconAnchor:[2.5, 2.5]},
             7 :{iconSize : [5, 5],iconAnchor:[2.5, 2.5]},
             8 :{iconSize : [5, 5],iconAnchor:[2.5, 2.5]},
             9 :{iconSize : [5, 5],iconAnchor:[2.5, 2.5]},
             9 :{iconSize : [5, 5],iconAnchor:[2.5, 2.5]},
             10 :{iconSize : [5, 5],iconAnchor:[2.5, 2.5]},11 :{iconSize : [5, 5],iconAnchor:[2.5, 2.5]},
             12 :{iconSize : [9.25, 9.25],iconAnchor:[4.125, 4.125]},13 :{iconSize : [9.25, 9.25],iconAnchor:[4.125, 4.125]},
             14 :{iconSize : [13.50, 13.50],iconAnchor:[6.25, 6.25]},15 :{iconSize : [13.50, 13.50],iconAnchor:[6.25, 6.25]},
             16 :{iconSize : [17.75, 17.75],iconAnchor:[8.375, 8.375]},17 :{iconSize : [17.75, 17.75],iconAnchor:[8.375, 8.375]},
             18 :{iconSize : [23, 23],iconAnchor:[11.50, 11.50]}
            }


// création de l'icone utilisateur
function utilisateurStile(feature, latlng) {    
    var utilisateurIcon = new L.icon({
        iconUrl: 'img/utilisateur.png',//Chemin de l'image 
        iconSize:     [25, 25], // Taille de l'icone
        iconAnchor:   [12.5, 12.5], // Point d'insertion de l'icone
        popupAnchor:  [-3, -15], // Point d'insertion de la popup
        shadowAnchor: [15,30], //Point d'insertion de l'image d'ombre
        shadowUrl: 'img/marker-shadow.png'//Chemin de l'image d'ombre
        });
        return L.marker(latlng, {icon: utilisateurIcon});
    }

// Style pour les buffers
var bufferStyle = {
    "color": "#a538d0",
    "weight": 2,
    // "opacity": 1
};


//Création d'un style pour les communes
var myStyle = {
    "color": "#000000",
    "weight": 2,
    "opacity": 0.65
};

//Création d'un style pour la commune zommée
var myStyle2 = {
    "color": "#FF0404",
    "weight": 2,
    "opacity": 0.65
};