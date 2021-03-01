//création de la variable map
var map = L.map('map');
//appel osm
var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
//attribution osm
var osmAttrib='Map data © OpenStreetMap contributors';
//création de la couche osm
var osm = new L.TileLayer(osmUrl, {attribution: osmAttrib}).addTo(map);
//centrage de la carte
map.setView([45.7175, 4.919], 9);

// Appel du script php
//style pour la couche commune

var style_commune = {
    "color": "#fff",
    "weight": 2,
    "opacity": 0.65
};

//Création d'un style pour les communes
var myStyle = {
    "color": "#000000",
    "weight": 2,
    "opacity": 0.65
};

layerControl = L.control.layers().addTo(map);

//Création d'une fonction de style pour changer les icones selon le type d'équipement :
function PoIstile(feature, latlng) {
    switch(feature.properties["type_equip"]) {
        case "AMAP":
            var amapIcon = new L.icon({
                iconUrl: 'img/AMAP.png',//Chemin de l'image 
                iconSize:     [15, 15], // Taille de l'icone
                iconAnchor:   [7.5, 7.5], // Point d'insertion de l'icone
                popupAnchor:  [-3, -15], // Point d'insertion de la popup
                shadowAnchor: [15,30], //Point d'insertion de l'image d'ombre
                shadowUrl: 'img/marker-shadow.png'//Chemin de l'image d'ombre
            });
            return L.marker(latlng, {icon: amapIcon});
        case "compost":
            var composteurIcon = new L.icon({
                iconUrl: 'img/composteur.png',//Chemin de l'image 
                iconSize:     [15, 15], // Taille de l'icone
                iconAnchor:   [7.5, 7.5], // Point d'insertion de l'icone
                popupAnchor:  [-3, -15], // Point d'insertion de la popup
                shadowAnchor: [15,30], //Point d'insertion de l'image d'ombre
                shadowUrl: 'img/marker-shadow.png' //Chemin de l'image d'ombre
            });
            return L.marker(latlng, {icon: composteurIcon});              
        }       
};

//Appel de la couche commune
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    //lecture de la connexion au fichier php (2 variables cf. biblio)
    if (this.readyState == 4 && this.status ==200) {
        //récupération du résultat de la requête sql et parcours de la couche :        
        let response = JSON.parse(xhttp.responseText)                   
        //transformation du tableau récupéré en couche geojson        
        var commune = L.geoJSON(response,{
        //application du style
        style: myStyle,        
        }).addTo(map);
        layerControl.addOverlay(commune, "Commune");        
        }
    };
//requête du fichier php
xhttp.open("GET", "php/commune.php",true);
//envoie de la commande au fichier
xhttp.send();



//Appel de la couche equipement
// création d'un groupe layer pour pouvoir effacer les données
var equipements = L.layerGroup();
var xhttp2 = new XMLHttpRequest();
//lecture de la connexion au fichier php (2 variables cf. biblio)
xhttp2.onreadystatechange = function() {
    if (this.readyState == 4 && this.status ==200) {
        //récupération du résultat de la requête sql et parcours de la couche :
        let response = JSON.parse(xhttp2.responseText)
        //appel de la couche
        L.geoJSON(response, {
            //application du style
            pointToLayer : PoIstile,
            //appel de popup
            onEachFeature: function(feature, layer) {
                var popup_content = ""

                popup_content +=                    
                '<b>'+ "Type : "+ feature.properties.type_equip +'</b>'+
                "<br>Nom : " + feature.properties.nom+
                "<br>Adresse : "+ feature.properties.adresse + 
                "<br>Commune : " +feature.properties.code_post + " " + feature.properties.nom_com
                // affichage des données suivantes si elles existent
                if (feature.properties.infoloc){
                    popup_content += "<br>Précision d'emplacement : " + feature.properties.infoloc}
                if (feature.properties.type_site){
                    popup_content += "<br>Type de site : " + feature.properties.type_site}
                if (feature.properties.site_inter){
                    popup_content += "<br>"+'<a href="' + feature.properties.site_inter + '" target="_blank">'+ feature.properties.site_inter +'</a>'}
                if (feature.properties.mail){
                    popup_content += "<br>Mail : " + feature.properties.mail}   
                layer.bindPopup(popup_content)
            }
        }).addTo(equipements)
        equipements.addTo(map)
    }
    };
xhttp2.open("GET", "php/equipement.php",true);
xhttp2.send();

/*
//Appel de la couche ambassadeur_mdp
var xhttp3 = new XMLHttpRequest();
//lecture de la connexion au fichier php (2 variables cf. biblio)
xhttp3.onreadystatechange = function() {
    if (this.readyState == 4 && this.status ==200) {
        //récupération du résultat de la requête sql et parcours de la couche :
        let response = JSON.parse(xhttp3.responseText)
        //appel de la couche
        L.geoJSON(response, {
            //application du style
            //pointToLayer: function (feature, latlng) {
            //    return L.circleMarker(latlng,{radius:5});
            //},        
            //appel de popup
            onEachFeature: function(feature, layer) {
                layer.bindPopup(
                //"Type : "+ feature.properties.type_equip +
                //"<br>Nom : " + feature.properties.nom+
                "Adresse : "+ feature.properties.adresse
                //"<br>Commune : " +feature.properties.code postal + " " + feature.properties.nom_com +
                //"<br>Précision d'emplacement : " + feature.properties.infoloc +
                //"<br>Type de site : " + feature.properties.type_site +
                //"<br>Site Internet : " + feature.properties.site_inter +
                //"<br>Mail : " + feature.properties.mail               
                )
            }
        }).addTo(map)
    }
    };
xhttp3.open("GET", "php/ambassadeur_mdp.php",true);
xhttp3.send();
*/

map.on('click', function(e){
    lat = e.latlng.lat;
    lng = e.latlng.lng;
    launchXHR(lat, lng);
    // itineraireDisplay ()
})

function launchXHR(lat, lng) {
    // var params = JSON.stringify({ id: 1 });
    var xhttp = new XMLHttpRequest();  
    
xhttp.open("POST", "itineraire/select_point.php", true);

xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhttp.send("lat="+lat+"&lng="+lng);
}

var styleIti = {
    "color": 'rgb(35, 147, 219)',
    "weight": 7.5,
    "opacity": 0.8
};

// création d'un groupe layer pour pouvoir effacer les données
var itineraires = L.layerGroup() 
//Appel de la couche itineraire dans une fonction lancée par un bouton ou autre
function itineraireDisplay (){
    var xhttp_iti = new XMLHttpRequest();
    xhttp_iti.onreadystatechange = function() {
        //lecture de la connexion au fichier php (2 variables cf. biblio)
        if (this.readyState == 4 && this.status == 200) {
            //récupération du résultat de la requête sql et parcours de la couche :        
            let response = JSON.parse(xhttp_iti.responseText)
            // on efface les données du group layer
            itineraires.clearLayers();       
            var itineraire = L.geoJSON(response,{
                //application du style
                style: styleIti,
                })
            // itineraire.clearLayer();
            // ajout du calque au group layer
            itineraires.addLayer(itineraire);
            // ajourt du groupe layer à la carte
            itineraires.addTo(map);
            // zoom sur la couche
            map.fitBounds(itineraire.getBounds());            
            }
        };
    //requête du fichier php
    xhttp_iti.open("GET", "itineraire/itineraire.php",true);
    //envoie de la commande au fichier
    xhttp_iti.send();
}

///////////////////////////////////////
//    Couche Association Filtrée   ///
/////////////////////////////////////

//Création d'une fonction de style pour changer les icones selon le type d'association :
function PoIstile_asso(feature, latlng) {
    switch(feature.properties["id_cate"]) {
        case "007070":
            var jardinsIcon = new L.icon({
                iconUrl: 'img/jardins_partages.png',//Chemin de l'image 
                iconSize:     [15, 15], // Taille de l'icone
                iconAnchor:   [7.5, 7.5], // Point d'insertion de l'icone
                popupAnchor:  [-3, -15], // Point d'insertion de la popup
                shadowAnchor: [15,30], //Point d'insertion de l'image d'ombre
                shadowUrl: 'img/marker-shadow.png'//Chemin de l'image d'ombre
            });
            return L.marker(latlng, {icon: jardinsIcon});
        case "007075":
            var echangesIcon = new L.icon({
                iconUrl: 'img/echanges.png',//Chemin de l'image 
                iconSize:     [15, 15], // Taille de l'icone
                iconAnchor:   [7.5, 7.5], // Point d'insertion de l'icone
                popupAnchor:  [-3, -15], // Point d'insertion de la popup
                shadowAnchor: [15,30], //Point d'insertion de l'image d'ombre
                shadowUrl: 'img/marker-shadow.png' //Chemin de l'image d'ombre
            });
            return L.marker(latlng, {icon: echangesIcon});
        case "020025":
        var benevolatIcon = new L.icon({
            iconUrl: 'img/benevolat.png',//Chemin de l'image 
            iconSize:     [15, 15], // Taille de l'icone
            iconAnchor:   [7.5, 7.5], // Point d'insertion de l'icone
            popupAnchor:  [-3, -15], // Point d'insertion de la popup
            shadowAnchor: [15,30], //Point d'insertion de l'image d'ombre
            shadowUrl: 'img/marker-shadow.png' //Chemin de l'image d'ombre
        });
        return L.marker(latlng, {icon: benevolatIcon});
        case "030050":
        var dvptIcon = new L.icon({
            iconUrl: 'img/dvpt_durable.png',//Chemin de l'image 
            iconSize:     [15, 15], // Taille de l'icone
            iconAnchor:   [7.5, 7.5], // Point d'insertion de l'icone
            popupAnchor:  [-3, -15], // Point d'insertion de la popup
            shadowAnchor: [15,30], //Point d'insertion de l'image d'ombre
            shadowUrl: 'img/marker-shadow.png' //Chemin de l'image d'ombre
        });
        return L.marker(latlng, {icon: dvptIcon});
        case "024000":
        var environnementIcon = new L.icon({
            iconUrl: 'img/environnement.png',//Chemin de l'image 
            iconSize:     [15, 15], // Taille de l'icone
            iconAnchor:   [7.5, 7.5], // Point d'insertion de l'icone
            popupAnchor:  [-3, -15], // Point d'insertion de la popup
            shadowAnchor: [15,30], //Point d'insertion de l'image d'ombre
            shadowUrl: 'img/marker-shadow.png' //Chemin de l'image d'ombre
        });
        return L.marker(latlng, {icon: environnementIcon});              
        }       
};

// recuperation des preferences utilisateurs depuis le DOM qui sont en hidden
var categoriesUtilisateurs = document.getElementsByClassName('categorie');
// création d'un groupe layer pour pouvoir effacer les données
var associations = L.layerGroup()
// fonction d'appel de la couche en filtrant sur les paramètres utilisateurs
function appelCouche(couche){
    //Appel de la couche association
    var xhttp4 = new XMLHttpRequest();
    //lecture de la connexion au fichier php (2 variables cf. biblio)
    xhttp4.onreadystatechange = function() {
        if (this.readyState == 4 && this.status ==200) {
            //récupération du résultat de la requête sql et parcours de la couche :
            let response = JSON.parse(xhttp4.responseText)
            //appel de la couche
            L.geoJSON(response, {
                //application du style
                pointToLayer : PoIstile_asso,
                //application du filtre
                filter: function(feature,layer) {
                    if (feature.properties.id_cate == couche) return true
                },
                //appel de popup
                onEachFeature: function(feature, layer) {
                    var popup_content = ""
                    
                    popup_content += '<b>' + "Type : "+ feature.properties.nom_cate + '</b>' + // le <b> permet de mettre en gras
                    "<br>Nom : " + feature.properties.titre+
                    "<br>Adresse : "+ feature.properties.adrs_numvo + " " + feature.properties.adrs_typev + " " + feature.properties.adrs_libvo +
                    "<br>Commune : " +feature.properties.code_post + " " + feature.properties.nom_com +
                    "<br>Objet : " + feature.properties.objet
                    // les données suivantes ne sont ajoutées que si elles existent elle ne sont pas nulle ou = #N/A
                    if (feature.properties.siteweb != '#N/A'){
                        popup_content +=  "<br>"+'<a href="' + feature.properties.siteweb + '" target="_blank">'+feature.properties.siteweb+'</a>'}
                    if (feature.properties.courriel){
                        popup_content += "<br>Mail : " + feature.properties.courriel}                                 
                    layer.bindPopup(popup_content)
                }
            }).addTo(associations)
            associations.addTo(map)            
        }
        };
    xhttp4.open("GET", "php/association.php",true);
    xhttp4.send();
    }
    // Parcours de la liste des préférences et lancement de la fonction selon ces paramètres
    for (let item of categoriesUtilisateurs) {
        appelCouche(item.textContent);
    }

//Création d'un style pour les communes
var myStyle2 = {
    "color": "#FF0404",
    "weight": 2,
    "opacity": 0.65
};

/////////////////////////////////////////////////////////////
//   Fonction Zoom sur la commune pour les statistiques   //
///////////////////////////////////////////////////////////

// création d'un groupe layer pour pouvoir effacer les données de la commune zoomée
var communesZoom = L.layerGroup()
// Création de la fonction
function zoomVille(ville){    
    console.log(ville.value);          
    var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            //lecture de la connexion au fichier php (2 variables cf. biblio)
            if (this.readyState == 4 && this.status ==200) {
                //récupération du résultat de la requête sql et parcours de la couche :        
                let response = JSON.parse(xhttp.responseText)                   
                //Le groupe communes zoom est vidé
                communesZoom.clearLayers();                         
                var commune = L.geoJSON(response,{
                //application du style
                style: myStyle2,
                filter: function(feature,layer) {
                    if (feature.properties.nom_com == ville.value) return true
                }
                }).addTo(communesZoom);
                communesZoom.addTo(map)        
                }
                map.fitBounds(commune.getBounds())
            };
        //requête du fichier php
        xhttp.open("GET", "php/commune.php",true);
        //envoie de la commande au fichier
        xhttp.send();

    var xhttp4 = new XMLHttpRequest();
    //lecture de la connexion au fichier php (2 variables cf. biblio)
    xhttp4.onreadystatechange = function() {
        if (this.readyState == 4 && this.status ==200) {
            //récupération du résultat de la requête sql et parcours de la couche :
            let response = JSON.parse(xhttp4.responseText)
            //appel de la couche
            associations.clearLayers();
            L.geoJSON(response, {
                //application du style
                pointToLayer : PoIstile_asso,
                filter: function(feature,layer) {
                    if (feature.properties.nom_com == ville.value) return true
                },
                //appel de popup
                onEachFeature: function(feature, layer) {
                    var popup_content = ""
                    
                    popup_content += '<b>' + "Type : "+ feature.properties.nom_cate + '</b>' + // le <b> permet de mettre en gras
                    "<br>Nom : " + feature.properties.titre+
                    "<br>Adresse : "+ feature.properties.adrs_numvo + " " + feature.properties.adrs_typev + " " + feature.properties.adrs_libvo +
                    "<br>Commune : " +feature.properties.code_post + " " + feature.properties.nom_com +
                    "<br>Objet : " + feature.properties.objet
                    // les données suivantes ne sont ajoutées que si elles existent elle ne sont pas nulle ou = #N/A
                    if (feature.properties.siteweb != '#N/A'){
                        popup_content +=  "<br>"+'<a href="' + feature.properties.siteweb + '" target="_blank">'+feature.properties.siteweb+'</a>'}
                    if (feature.properties.courriel){
                        popup_content += "<br>Mail : " + feature.properties.courriel}                                 
                    layer.bindPopup(popup_content)
                }
            }).addTo(associations)
            associations.addTo(map)            
        }
        };
    xhttp4.open("GET", "php/association.php",true);
    xhttp4.send();
    
    //Appel de la couche equipement
    // création d'un groupe layer pour pouvoir effacer les données    
    var xhttp2 = new XMLHttpRequest();
    //lecture de la connexion au fichier php (2 variables cf. biblio)
    xhttp2.onreadystatechange = function() {
        if (this.readyState == 4 && this.status ==200) {
            //récupération du résultat de la requête sql et parcours de la couche :
            let response = JSON.parse(xhttp2.responseText)
            //appel de la couche
            equipements.clearLayers();
            L.geoJSON(response, {
                //application du style
                pointToLayer : PoIstile,
                // Filtre en fonction de la commune choisie
                filter: function(feature,layer) {
                    if (feature.properties.nom_com == ville.value) return true
                },
                //appel de popup
                onEachFeature: function(feature, layer) {
                    var popup_content = ""

                    popup_content +=                    
                    '<b>'+ "Type : "+ feature.properties.type_equip +'</b>'+
                    "<br>Nom : " + feature.properties.nom+
                    "<br>Adresse : "+ feature.properties.adresse + 
                    "<br>Commune : " +feature.properties.code_post + " " + feature.properties.nom_com
                    // affichage des données suivantes si elles existent
                    if (feature.properties.infoloc){
                        popup_content += "<br>Précision d'emplacement : " + feature.properties.infoloc}
                    if (feature.properties.type_site){
                        popup_content += "<br>Type de site : " + feature.properties.type_site}
                    if (feature.properties.site_inter){
                        popup_content += "<br>"+'<a href="' + feature.properties.site_inter + '" target="_blank">'+ feature.properties.site_inter +'</a>'}
                    if (feature.properties.mail){
                        popup_content += "<br>Mail : " + feature.properties.mail}   
                    layer.bindPopup(popup_content)
                }
            }).addTo(equipements)
            equipements.addTo(map)
        }
        };
    xhttp2.open("GET", "php/equipement.php",true);
    xhttp2.send();
}


// Fonction de création du graphique camembert avec en paramètre association ou équipement et la commune sur laquelle on veut voir les stats
function makeChart(){
    // récupération de la balise du dom où sera placé le graph
    var ctx = document.getElementById('myChart').getContext('2d');
    // création de deux tableaux vides pour placer les données
    // données littérales
    var cateData = [] 
    // nombres de données par communes
    var countData = []
    // récupération des id categoéries
    var typeData = []
    // récupération des données insérées par la requete sql dans une balise hidden
    var cate = document.getElementsByClassName('nom_cate');
    
    var i;
    // itération dans le tableau renvoyé
    for (i = 1; i < cate.length; i++) {
        var texte = cate[i].innerHTML
        // texte récupéré splité avec le séparateur :
        var souspart = texte.split(":")
        // transformartion en number de la valeur du count
        var countInt = parseInt(souspart[1]);
        // ajout des données dans les tableaux vides
        cateData.push(souspart[0]);
        countData.push(countInt);
        // console.log(souspart[2]);
        typeData.push(souspart[2]);
        } 
   
    // création du graphique (cf. doc chart.js)
    // création d'un tableau avec toutes les couleurs des icones équipements et associations:
    var paletteFond =[]
    
    var paletteTotale = ['007070','rgba(39, 90, 25, 0.7)',
                        '007075','rgba(255, 184, 65, 0.7)',
                        '020025','rgba(2, 75, 151, 0.7)',
                        '030050','rgba(247, 170, 28, 0.7)',
                        '024000','rgba(148, 193, 31, 0.7)',
                        'AMAP','rgba(233, 78, 27, 0.7)',
                        'compost','rgba(126, 79, 37, 0.7)']
    
    // parcours et renvoie de la valeur de couleur en fonction du type 
    for (i = 0; i < typeData.length; i++){
        console.log(typeData[i])
        for(j=0; j<paletteTotale.length;j++){
            if (typeData[i]==paletteTotale[j]){
                paletteFond.push(paletteTotale[j+1])
            }
        }
    }
        
    var myChart = new Chart(ctx, {
        type: 'piechart',
        data: {
            labels: cateData,
            datasets: [{
            // label: '# of Tomatoes',
            data: countData,
            backgroundColor: paletteFond,
            // borderColor: couleurBord(),
            borderWidth: 1.5
            }]
        },
        options: {
            //cutoutPercentage: 40,
            responsive: false,

        }
        });
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

// récupération de l'id_utilisateur pour filter la couche   
id_utilisateur = document.getElementById('id_utilisateur').textContent;
console.log(id_utilisateur);
// Affichage de l'adresse de l'utilisateur sur la carte 
          
var xhttpUtil = new XMLHttpRequest();
    xhttpUtil.onreadystatechange = function() {
        //lecture de la connexion au fichier php (2 variables cf. biblio)
        if (this.readyState == 4 && this.status ==200) {
            //récupération du résultat de la requête sql et parcours de la couche : 
            let response = JSON.parse(xhttpUtil.responseText)
        //appel de la couche
            var utilisateur = L.geoJSON(response, {
                //application du style
                pointToLayer : utilisateurStile,                
                filter : function(feature,layer) {
                    if (feature.properties.id_utilisateur == id_utilisateur) return true
                }
                
            }).addTo(map)
            // centre et zoom sur l'utilisateur à partir des coordonnées du centre de la couche ici un seul point
            map.setView(utilisateur.getBounds().getCenter(), 16);        
            }                
        };
//requête du fichier php
xhttpUtil.open("GET", "php/marker_utilisateur.php",true);
//envoie de la commande au fichier
xhttpUtil.send();