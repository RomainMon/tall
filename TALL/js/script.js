//création de la variable map
var sudOuest = L.latLng(45.44471679159555, 4.395217895507813);
// Le point en haut à droite  de la carte
var nordEst = L.latLng(46.00459325574482, 5.346221923828126); 
// L'étendue
var bounds = L.latLngBounds(sudOuest, nordEst);

var center = [45.761415578787926, 4.833812713623047];

var map = L.map('map', {
    center: center,
    maxBounds: bounds,
    zoom: 11,
    minZoom : 2,
    maxZoom: 18,
     });


map.setView(center, 12);

//appel osm
var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
//attribution osm
var osmAttrib='Map data © OpenStreetMap contributors';
//création de la couche osm
var osm = new L.TileLayer(osmUrl, {attribution: osmAttrib}).addTo(map);
//centrage de la carte



///////////////////////////////////////////
//   Variables en fonction de la page   //
/////////////////////////////////////////

// recuperation des catégories associations et equipements depuis le DOM
var categoriesAssoHTML = document.getElementsByClassName('liste_cate');
var categoriesAsso = []
for (let item of categoriesAssoHTML) {
    categoriesAsso.push(item.value)
    };
// console.log(categoriesAsso);

var categoriesEquipHTML = document.getElementsByClassName('liste_equip');
var categoriesEquip = []
for (let item of categoriesEquipHTML) {
    categoriesEquip.push(item.value)
    };
// console.log(categoriesEquip);

// création d'un groupe layer pour pouvoir effacer les données
var equipements = L.layerGroup();
var communes = L.layerGroup();
var associations = L.layerGroup();
var utilisateurs = L.layerGroup();
var buffers = L.layerGroup()
    
// Ajout du layer control
layerControl = L.control.layers().addTo(map);


communes.clearLayers();
    //Appel de la couche commune
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        //lecture de la connexion au fichier php (2 variables cf. biblio)
        if (this.readyState == 4 && this.status ==200) {
            //récupération du résultat de la requête sql et parcours de la couche :        
            let response = JSON.parse(xhttp.responseText)                   
            //transformation du tableau récupéré en couche geojson        
            communes.clearLayers();
            var commune = L.geoJSON(response,{
            //application du style
            style: myStyle,        
            }).addTo(communes);
            layerControl.addOverlay(commune, "Commune");        
            communes.addTo(map);    
        }
        };
    //requête du fichier php
    xhttp.open("GET", "php/commune.php",true);
    //envoie de la commande au fichier
    xhttp.send();

function majCouche(){
  
////////////////////////////
//   Couche Equipement   //
//////////////////////////

//Appel de la couche equipement

// fonction qui appelle les équipements sélectionnés

    var xhttp2 = new XMLHttpRequest();
    //lecture de la connexion au fichier php (2 variables cf. biblio)
    xhttp2.onreadystatechange = function() {
        if (this.readyState == 4 && this.status ==200) {
            //récupération du résultat de la requête sql et parcours de la couche :
            let response = JSON.parse(xhttp2.responseText)
            //appel de la couche
            equipements.clearLayers();
            var equipement = L.geoJSON(response, {
                //application du style
                pointToLayer : function(feature,latlng){
                    return L.marker(latlng,{icon : videIcon})
                },
                filter: function(feature,layer) {
                    for (let item of categoriesEquip) {
                        if (feature.properties.id_type_equip == item) return true
                        }
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
                    if(calque.feature.properties.id_type_equip == listeIconEquip[i])
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
                        if(calque.feature.properties.id_type_equip == listeIconEquip[i])
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

// fonction d'appel de la couche en filtrant sur les paramètres utilisateurs
    //Appel de la couche association
    var xhttp4 = new XMLHttpRequest();
    //lecture de la connexion au fichier php (2 variables cf. biblio)
    xhttp4.onreadystatechange = function() {
        if (this.readyState == 4 && this.status ==200) {
            //récupération du résultat de la requête sql et parcours de la couche :
            let response = JSON.parse(xhttp4.responseText)
            //appel de la couche
            associations.clearLayers();
            var association = L.geoJSON(response, {
                //application du style
                pointToLayer : function(feature,latlng){
                    return L.marker(latlng,{icon : videIcon})
                },
                //application du filtre
                filter: function(feature,layer) {
                    for (let item of categoriesAsso) {
                        if (feature.properties.id_cate == item) return true
                }},
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
    }
majCouche();

$('#legende_asso :checkbox').change(function(){
    console.log(categoriesAsso);
    if (this.checked) {
        console.log(this.value);
        categoriesAsso.push(this.value)   
    }
    
    else{console.log(this.value);
        var index = categoriesAsso.indexOf(this.value);
        if (index > -1) {
            categoriesAsso.splice(index, 1);
        }}
    
    console.log(categoriesAsso);

majCouche();
});

$('#legende_equip :checkbox').change(function(){
    console.log(categoriesEquip);
    if (this.checked) {
        console.log(this.value);
        categoriesEquip.push(this.value)   
    }
    
    else{console.log(this.value);
        var index = categoriesEquip.indexOf(this.value);
        if (index > -1) {
            categoriesEquip.splice(index, 1);
        }}
    
    console.log(categoriesEquip);

majCouche();
});
