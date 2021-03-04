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


///////////////////////////////////////////
//   Variables en fonction de la page   //
/////////////////////////////////////////

// recuperation des preferences utilisateurs depuis le DOM qui sont en hidden
var categoriesUtilisateurs = document.getElementsByClassName('categorie');


// Ajout du layer control
layerControl = L.control.layers().addTo(map);


/////////////////////////
//   Couche Commune   //
///////////////////////

//Création d'un style pour les communes
var myStyle = {
    "color": "#000000",
    "weight": 2,
    "opacity": 0.65
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
        map.fitBounds(commune.getBounds())        
        }
    };
//requête du fichier php
xhttp.open("GET", "php/commune.php",true);
//envoie de la commande au fichier
xhttp.send();



////////////////////////////
//   Couche Equipement   //
//////////////////////////

//Appel de la couche equipement
// création d'un groupe layer pour pouvoir effacer les données
var equipements = L.layerGroup();
// fonction qui appelle les équipements sélectionnés à partir des préférences utilisateurs

var xhttp2 = new XMLHttpRequest();
//lecture de la connexion au fichier php (2 variables cf. biblio)
xhttp2.onreadystatechange = function() {
    if (this.readyState == 4 && this.status ==200) {
        //récupération du résultat de la requête sql et parcours de la couche :
        let response = JSON.parse(xhttp2.responseText)
        //appel de la couche
        var equipement = L.geoJSON(response, {
            //application du style
            pointToLayer : function(feature,latlng){
                return L.marker(latlng,{icon : videIcon})
            },
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
        }).addTo(equipements);
        // Chargement de l'icone en fonction du zoom
        var currentZoom = map.getZoom();
        equipement.eachLayer(function(calque){
            var i;
            for(i = 0 ; i < listeIconEquip.length; i++){                            
                if(calque.feature.properties.type_equip == listeIconEquip[i])
                return calque.setIcon(zoomIcon(listeIconEquip[i],currentZoom))
            }                        
        });
        // Chargement des différentes icones en fonction du zoom 
        map.on('zoomend', function(){
            // zoom courant
            var currentZoom = map.getZoom();
            equipement.eachLayer(function(calque){
                var i;
                for(i = 0 ; i < listeIconEquip.length; i++){                            
                    if(calque.feature.properties.type_equip == listeIconEquip[i])
                    return calque.setIcon(zoomIcon(listeIconEquip[i],currentZoom))
                }                        
            });
        });
        equipements.addTo(map)
    }
    };
xhttp2.open("GET", "php/equipement.php",true);
xhttp2.send();
   


//////////////////////////////////////
//    Couche Association Filtrée   //
////////////////////////////////////


// création d'un groupe layer pour pouvoir effacer les données
var associations = L.layerGroup()
// fonction d'appel de la couche en filtrant sur les paramètres utilisateurs

//Appel de la couche association
var xhttp4 = new XMLHttpRequest();
//lecture de la connexion au fichier php (2 variables cf. biblio)
xhttp4.onreadystatechange = function() {
    if (this.readyState == 4 && this.status ==200) {
        //récupération du résultat de la requête sql et parcours de la couche :
        let response = JSON.parse(xhttp4.responseText)
        //appel de la couche
        var association = L.geoJSON(response, {
            //application du style
            pointToLayer : function(feature,latlng){
                return L.marker(latlng,{icon : videIcon})
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
        // Chargement de l'icone en fonction du zoom
        var currentZoom = map.getZoom();
        association.eachLayer(function(calque){
        var i;
        for(i = 0 ; i < listeIconEquip.length; i++){                            
            if(calque.feature.properties.id_cate == listeIconEquip[i])
            return calque.setIcon(zoomIcon(listeIconEquip[i],currentZoom))
        }                        
        });
        // Chargement des différentes icones en fonction du zoom 
        map.on('zoomend', function(){
            // zoom courant
            var currentZoom = map.getZoom();
            console.log(currentZoom);
            association.eachLayer(function(calque){
                var i;
                for(i = 0 ; i < listeIconEquip.length; i++){                            
                    if(calque.feature.properties.id_cate == listeIconEquip[i])
                    return calque.setIcon(zoomIcon(listeIconEquip[i],currentZoom))
                }                        
            });
            
        });
        associations.addTo(map)            
    }
    };
xhttp4.open("GET", "php/association.php",true);
xhttp4.send();

//////////////////////////////////////////////////////
//   Couche des ambassadeurs mouvement de palier   //
////////////////////////////////////////////////////

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
