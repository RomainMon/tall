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

//Vue de base de la carte
map.setView(center, 12);


//barre d'échelle
L.control.scale().addTo(map);

//////////////////////////////////
//   Ajout des fonds de plan   //
////////////////////////////////

//appel osm
var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
//attribution osm
var osmAttrib='Map data © OpenStreetMap contributors';
//création de la couche osm
// var osm = new L.TileLayer(osmUrl, {attribution: osmAttrib}).addTo(map);

var Stamen_Terrain = L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/terrain/{z}/{x}/{y}{r}.{ext}', {
	attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
	subdomains: 'abcd',
	minZoom: 0,
	maxZoom: 18,
	ext: 'png'
});

var GeoportailFrance_orthos = L.tileLayer('https://wxs.ign.fr/{apikey}/geoportail/wmts?REQUEST=GetTile&SERVICE=WMTS&VERSION=1.0.0&STYLE={style}&TILEMATRIXSET=PM&FORMAT={format}&LAYER=ORTHOIMAGERY.ORTHOPHOTOS&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}', {
	attribution: '<a target="_blank" href="https://www.geoportail.gouv.fr/">Geoportail France</a>',
	bounds: [[-75, -180], [81, 180]],
	minZoom: 2,
	maxZoom: 19,
	apikey: 'choisirgeoportail',
	format: 'image/jpeg',
	style: 'normal'
});

var CartoDB_Voyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
	attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
	subdomains: 'abcd',
	maxZoom: 19
});
CartoDB_Voyager.addTo(map);



// création de la variable base layer que l'on rajoute au widget après
var baseLayers = {
    "Plan": CartoDB_Voyager, 
    "Satellite": GeoportailFrance_orthos,
    "Terrain": Stamen_Terrain 
};

// Ajout du layer control
layerControl = L.control.layers(baseLayers).addTo(map);

///////////////////////////////////////////
//   Variables en fonction de la page   //
/////////////////////////////////////////

// recuperation des preferences utilisateurs depuis le DOM qui sont en hidden
var categoriesUtilisateursHTML = document.getElementsByClassName('categorie');
var categoriesUtilisateurs = []


for (let item of categoriesUtilisateursHTML) {
    categoriesUtilisateurs.push(item.textContent)
    }

var categoriesEquipHTML = document.getElementsByClassName('typequip');
var categoriesEquip = []
for (let item of categoriesEquipHTML) {
    categoriesEquip.push(item.textContent)
    };
console.log(categoriesEquip);

// récupération de l'id_utilisateur pour filter la couche   
id_utilisateur = document.getElementById('id_utilisateur').textContent;
console.log(id_utilisateur);

// création d'un groupe layer pour pouvoir effacer les données
var equipements = L.layerGroup();
var communes = L.layerGroup();
var associations = L.layerGroup();
var utilisateurs = L.layerGroup();
var buffers = L.layerGroup();
var communesZoom = L.layerGroup();
var itineraires = L.layerGroup();
    
/////////////////////////
//   Couche Commune   //
///////////////////////

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

////////////////////////////
//   Couche Equipement   //
//////////////////////////

// Appel de la couche equipement au démarrage avec les préférences utilisateurs

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
                for (let item of categoriesUtilisateurs) {
                    if (feature.properties.id_cate == item) return true
                    }
                },
            onEachFeature: function(feature, layer) {
                var popup_content = ""

                popup_content +=                    
                '<b>'+ "Type : "+ feature.properties.type_equip +'</b>'
                if (feature.properties.nom){
                    popup_content += "<br>Nom : " + feature.properties.nom}
               
                popup_content += "<br>Adresse : "+ feature.properties.adresse + 
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


/////////////////////////////////////////////////////////////////////////////////
//   Fonction au démarrage de la page et mise à jour couches séléctionnées    //
///////////////////////////////////////////////////////////////////////////////

function prefUtilisateur(){
    equipements.clearLayers();
    itineraires.clearLayers();
    communesZoom.clearLayers();
    buffers.clearLayers();
    


    ////////////////////////////
    //   Couche Equipement   //
    //////////////////////////

    //Appel de la couche equipement
    
    // fonction qui appelle les équipements sélectionnés à partir des préférences utilisateurs

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
                        for (let item of categoriesUtilisateurs) {
                            if (feature.properties.id_cate == item) return true
                            }
                        },
                    onEachFeature: function(feature, layer) {
                        var popup_content = ""

                        popup_content +=                    
                        '<b>'+ "Type : "+ feature.properties.type_equip +'</b>'
                        if (feature.properties.nom){
                            popup_content += "<br>Nom : " + feature.properties.nom}
                        popup_content += "<br>Adresse : "+ feature.properties.adresse + 
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
                        for (let item of categoriesUtilisateurs) {
                            if (feature.properties.id_cate == item) return true
                    }},
                    //appel de popup
                    onEachFeature: function(feature, layer) {
                        var popup_content = ""
                        popup_content += '<b>' + "Type : "+ feature.properties.nom_cate + '</b>' + // le <b> permet de mettre en gras
                        "<br>Nom : " + feature.properties.titre+
                        "<br>Adresse : "+ feature.properties.adrs_numvo + " " + feature.properties.adrs_typev + " " + feature.properties.adrs_libvo +
                        "<br>Commune : " +feature.properties.code_post + " " + feature.properties.nom_com 
                        // "<br>Objet : " + feature.properties.objet
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
    ///////////////////////////////////
    //   Adresse de l'utilisateur   //
    /////////////////////////////////

    // Affichage de l'adresse de l'utilisateur sur la carte appelée après les autres pour éviter que l'icone soit par-dessus les autres

    var xhttpUtil = new XMLHttpRequest();
        xhttpUtil.onreadystatechange = function() {
            //lecture de la connexion au fichier php (2 variables cf. biblio)
            if (this.readyState == 4 && this.status ==200) {
                //récupération du résultat de la requête sql et parcours de la couche : 
                let response = JSON.parse(xhttpUtil.responseText)
            //appel de la couche
                utilisateurs.clearLayers();
                var utilisateur = L.geoJSON(response, {
                    //application du style
                    pointToLayer : utilisateurStile,                
                    filter : function(feature,layer) {
                        if (feature.properties.id_utilisateur == id_utilisateur) return true
                    }
                    
                }).addTo(utilisateurs);
                utilisateurs.addTo(map);
                // centre et zoom sur l'utilisateur à partir des coordonnées du centre de la couche ici un seul point
                map.setView(utilisateur.getBounds().getCenter(), 14);        
                }                
            };
    //requête du fichier php
    xhttpUtil.open("GET", "php/marker_utilisateur.php",true);
    //envoie de la commande au fichier
    xhttpUtil.send();
    };

prefUtilisateur();

function majCoucheAsso(){
    //////////////////////////////////////
    //    Couche Association Filtrée   //
    ////////////////////////////////////
    
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
                        for (let item of categoriesUtilisateurs) {
                            if (feature.properties.id_cate == item) return true
                    }},
                    //appel de popup
                    onEachFeature: function(feature, layer) {
                        var popup_content = ""
                        popup_content += '<b>' + "Type : "+ feature.properties.nom_cate + '</b>' + // le <b> permet de mettre en gras
                        "<br>Nom : " + feature.properties.titre+
                        "<br>Adresse : "+ feature.properties.adrs_numvo + " " + feature.properties.adrs_typev + " " + feature.properties.adrs_libvo +
                        "<br>Commune : " +feature.properties.code_post + " " + feature.properties.nom_com 
                        "// <br>Objet : " + feature.properties.objet
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

    };

function majCoucheEquip(){
    
    ////////////////////////////
    //   Couche Equipement   //
    //////////////////////////

    //Appel de la couche equipement
    
    // fonction qui appelle les équipements sélectionnés à partir des préférences utilisateurs

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
                        '<b>'+ "Type : "+ feature.properties.type_equip +'</b>'
                        if (feature.properties.nom){
                            popup_content += "<br>Nom : " + feature.properties.nom}
                        popup_content += "<br>Adresse : "+ feature.properties.adresse + 
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
}


/////////////////////////////////////////////////////////////////////////
//   Fonction de mise à jour de la carte lors de la coche des cases   //
///////////////////////////////////////////////////////////////////////

$('#legende_cate :checkbox').change(function(){
    console.log(categoriesUtilisateurs);
    // Lorsqu'un checkbox de liste_cate est coché, ajout dans la liste des categories pour qu'elle s'affiche
    if (this.checked) {
        console.log(this.value);
        categoriesUtilisateurs.push(this.value)   
    }
    // Lorsqu'un checkbox de liste_cate est décoché, suppression dans la liste des categories pour qu'elle s'enlève
    else{console.log(this.value);
        var index = categoriesUtilisateurs.indexOf(this.value);
        if (index > -1) {
            categoriesUtilisateurs.splice(index, 1);
        }}
    
    console.log(categoriesUtilisateurs);

majCoucheAsso();
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

majCoucheEquip();
});

//////////////////////////////////////
//   navigation dans les onglets   //
////////////////////////////////////

// $('#tab-1').click(function(){
//     console.log('nardin')
//     prefUtilisateur()
// })

// $('#tab_3').change(function(){
//     console.log('oumok')   
//     prefUtilisateur()
// })

////////////////////////////
//   Couche Itinéraire   // 
////////////////////////// 

// Récupération des coordonnées au clic pour l'itinéraire
map.on('click', function(e){
    lat = e.latlng.lat;
    lng = e.latlng.lng;
    launchXHR(lat, lng);
})

// envoi des données vers le script php
function launchXHR(lat, lng) {
    // var params = JSON.stringify({ id: 1 });
    var xhttp = new XMLHttpRequest();  
    
xhttp.open("POST", "itineraire/select_point.php", true);

xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhttp.send("lat="+lat+"&lng="+lng);
}

//style pour l'itinéraire
var styleIti = {
    "color": 'rgb(35, 147, 219)',
    "weight": 7.5,
    "opacity": 0.8
};

// création d'un groupe layer pour pouvoir effacer les données
var itineraires = L.layerGroup() 
//Appel de la couche itineraire dans une fonction lancée par un bouton
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

/////////////////////////////////////////////////////////////
//   Fonction Zoom sur la commune pour les statistiques   //
///////////////////////////////////////////////////////////

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
    
    //couche associations
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
                filter: function(feature,layer) {
                    if (feature.properties.nom_com == ville.value) return true
                },
                //appel de popup
                onEachFeature: function(feature, layer) {
                    var popup_content = ""
                    
                    popup_content += '<b>' + "Type : "+ feature.properties.nom_cate + '</b>' + // le <b> permet de mettre en gras
                    "<br>Nom : " + feature.properties.titre+
                    "<br>Adresse : "+ feature.properties.adrs_numvo + " " + feature.properties.adrs_typev + " " + feature.properties.adrs_libvo +
                    "<br>Commune : " +feature.properties.code_post + " " + feature.properties.nom_com 
                    "// <br>Objet : " + feature.properties.objet
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
            var equipement = L.geoJSON(response, {
                //application du style
                pointToLayer : function(feature,latlng){
                    return L.marker(latlng,{icon : videIcon})
                },
                // Filtre en fonction de la commune choisie
                filter: function(feature,layer) {
                    if (feature.properties.nom_com == ville.value) return true
                },
                //appel de popup
                onEachFeature: function(feature, layer) {
                    var popup_content = ""

                    popup_content +=                    
                    '<b>'+ "Type : "+ feature.properties.type_equip +'</b>'
                    if (feature.properties.nom){
                        popup_content += "<br>Nom : " + feature.properties.nom}
                    popup_content += "<br>Adresse : "+ feature.properties.adresse + 
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
}

// je déclare les deux variables dans le global pour ensuite leur attribuer une valeur de commune ou d'asso ou d'equipement
var commune
var assoEquip

// il est important ici dans cette fonction jquery d'intégrer l'évènement mouseleave. Il permet de récupérer correctement les valeurs des sélecteurs pour ensuite les les exploiter
// dans la fonction jqery qui suit (celle qui contient le script qui crée les graph)
$('#choix_asso_equip, #choix_commune').mouseleave(function(){
    // récupération de la valeur de la commune choisie
    commune = $('#choix_commune').val();
    assoEquip = $('#choix_asso_equip').val();
    console.log(commune)
    console.log(assoEquip)
    
    if (assoEquip == 'association'){
        $.ajax({
            url : "statistique/stat_asso_commune.php", // on donne l'URL du fichier de traitement
            type : "POST", // la requête est de type POST
            dataType : "html",
            data : 'commune=' + commune,
            success : function(code_html, success){
                $(".nom_cate").html(code_html);
                console.log(code_html);
                map.removeLayer(equipements)
                associations.addTo(map)
                
            },
            error : function(resultat, statut, error){
                console.log(error)
            },
            complete : function(resultat, statut){

            }

        });
    }
    else {
        $.ajax({
            url : "statistique/stat_equip_commune.php", // on donne l'URL du fichier de traitement
            type : "POST", // la requête est de type POST
            dataType : "html",
            data : 'commune=' + commune,
            success : function(code_html, success){
                $(".nom_cate").html(code_html)
                console.log(code_html)
                map.removeLayer(associations)
                equipements.addTo(map)
            },
            error : function(resultat, statut, error){
                console.log(error)
            },
            complete : function(resultat, statut){

            }

        });

    }


});

$("#btn_stat").click(function(){

    
    try{
        $('canvas').remove();
        console.log('suppression effectuée')
    } catch {
        console.log('il y a rien à supp')
    }
  
    $("#suppression").append('<canvas id="myChart" width="300" height="300"></canvas>');

    
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
        
        var paletteTotale = ['007070','#4eae4f',
                            '007075','#624393',
                            '020025','#a8a800',
                            '030050','#006277',
                            '024000','#8ab7c0',
                            '1amap','#ba7300',
                            '2compost','#833204',
                            '3boite','#af0b2b']
        
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
        type: 'doughnut',
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
makeChart();
});


// Fonction pour la barre slider

function updateTextInput(val) {
    document.getElementById('rangeText').textContent=val; 
  }


/////////////////////////////////////////////////////
//   Fonction Zoom autour de chez l'utilisateur   //
///////////////////////////////////////////////////

function zoomAutour(){
    // récupération du temps et calcul de la distance de Manhattan:
    // temps = $('#rangeInput').val();
    distance = $('#rangeInput').val();
    // var zoomDistance = {0:{zoom:16},500:{zoom:16},1000:{zoom:15},1500:{zoom:13},2000:{zoom:13},2500:{zoom:13},3000:{zoom:13},3500:{zoom:13},4000:{zoom:12},4500:{zoom:12},4500:{zoom:12},5000:{zoom:12}};
    
    // Zoom sur l'utilisateur en fonction de la distance rentrée      
    var xhttpUtil = new XMLHttpRequest();
    xhttpUtil.onreadystatechange = function() {
        //lecture de la connexion au fichier php (2 variables cf. biblio)
        if (this.readyState == 4 && this.status ==200) {
            //récupération du résultat de la requête sql et parcours de la couche : 
            let response = JSON.parse(xhttpUtil.responseText)
        //appel de la couche
            utilisateurs.clearLayers();
            var utilisateur = L.geoJSON(response, {
                //application du style
                pointToLayer : utilisateurStile,                
                filter : function(feature,layer) {
                    if (feature.properties.id_utilisateur == id_utilisateur) return true
                }
                
            }).addTo(utilisateurs);
            utilisateurs.addTo(map);
            // centre et zoom sur l'utilisateur à partir des coordonnées du centre de la couche ici un seul point
            
            function zoomUtil(distance){
                return zoomDistance[distance].zoom
            };

            // zoomUtil(distance)
            // console.log(zoomUtil(distance))
            
            // map.setView(utilisateur.getBounds().getCenter(), zoomUtil(distance));        
            }                
        };
    //requête du fichier php
    xhttpUtil.open("GET", "php/marker_utilisateur.php",true);
    //envoie de la commande au fichier
    xhttpUtil.send();

    //couche associations
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
                filter: function(feature,layer) {
                    if (feature.properties.st_distance < distance) return true
                },
                //appel de popup
                onEachFeature: function(feature, layer) {
                    var popup_content = ""
                    
                    popup_content += '<b>' + "Type : "+ feature.properties.nom_cate + '</b>' + // le <b> permet de mettre en gras
                    "<br>Nom : " + feature.properties.titre+
                    "<br>Adresse : "+ feature.properties.adrs_numvo + " " + feature.properties.adrs_typev + " " + feature.properties.adrs_libvo +
                    "<br>Commune : " +feature.properties.code_post + " " + feature.properties.nom_com 
                    "// <br>Objet : " + feature.properties.objet
                    // les données suivantes ne sont ajoutées que si elles existent elle ne sont pas nulle ou = #N/A
                    if (feature.properties.siteweb != '#N/A'){
                        popup_content +=  "<br>"+'<a href="' + feature.properties.siteweb + '" target="_blank">'+feature.properties.siteweb+'</a>'}
                    if (feature.properties.courriel){
                        popup_content += "<br>Mail : " + feature.properties.courriel}                                 
                    layer.bindPopup(popup_content)
                }
            }).addTo(associations);
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
            associations.addTo(map);
        });
                        
        }
        };
    xhttp4.open("GET", "itineraire/autour_asso.php",true);
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
            var equipement = L.geoJSON(response, {
                //application du style
                pointToLayer : function(feature,latlng){
                    return L.marker(latlng,{icon : videIcon})
                },
                // Filtre en fonction de la commune choisie
                filter: function(feature,layer) {
                    if (feature.properties.st_distance < distance) return true
                },
                //appel de popup
                onEachFeature: function(feature, layer) {
                    var popup_content = ""

                    popup_content +=                    
                    '<b>'+ "Type : "+ feature.properties.type_equip +'</b>'
                    if (feature.properties.nom){
                        popup_content += "<br>Nom : " + feature.properties.nom}
                    popup_content += "<br>Adresse : "+ feature.properties.adresse + 
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

    
    xhttp2.open("GET", "itineraire/autour_equip.php",true);
    xhttp2.send();
  }

////////////////////////////////////////////////
//   Création d'un buffer pour la distance   //
//////////////////////////////////////////////

// Couche appelée par du jquery pour changer

function bufferUtil(distance){    
    // console.log(distance)
    // var bufferJson; 

    $.ajax({
        url : "php/buffer.php", // on donne l'URL du fichier de traitement
        type : "POST", // la requête est de type POST
        dataType : "html",
        data : 'distance=' + distance,
        success : function(response, success){
            buffers.clearLayers();                         
            var buffer = L.geoJSON(JSON.parse(response),{
            //application du style
            style: bufferStyle,
            // filter: function(feature,layer) {
            //     if (feature.properties.nom_com == ville.value) return true
            // }
            }).addTo(buffers);
            buffers.addTo(map);
            map.fitBounds(buffer.getBounds());
            // console.log(response)
            // console.log(bufferJson)
        },
        error : function(resultat, statut, error){
            console.log(error)
        },
        complete : function(resultat, statut){

        }

    });
 
};
