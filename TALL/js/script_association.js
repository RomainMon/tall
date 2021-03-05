//création de la variable map
var map = L.map('map');
//appel osm
var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
//attribution osm
var osmAttrib='Map data © OpenStreetMap contributors';
//création de la couche osm
var osm = new L.TileLayer(osmUrl, {attribution: osmAttrib}).addTo(map);
//centrage de la carte
map.setView([45.7175, 4.919], 13);


///////////////////////////////////////////
//   Variables en fonction de la page   //
/////////////////////////////////////////

// Création de liste vide pour la mise à jour de l'affichage des associations et des équipements
var categoriesAsso = [];
var categoriesEquip = [];
var equipAsso = [];

// recuperation des paramètres de session depuis le DOM qui sont en hidden
// récupération de l'id_asso et de l'id_cate pour filter la couche   
id_asso = document.getElementById('id_asso').textContent;
equipAsso.push(id_asso)
console.log(equipAsso);
id_cate = document.getElementById('id_cate').textContent;
// console.log(id_asso);

// création d'un groupe layer pour pouvoir effacer les données
var equipements = L.layerGroup();
var communes = L.layerGroup();
var associations = L.layerGroup();
var utilisateurs = L.layerGroup();
var buffers = L.layerGroup();
// Création d'un layer groupe équipements asso
var equipementsAsso =L.layerGroup();

// Ajout du layer control
layerControl = L.control.layers().addTo(map);


/////////////////////////
//   Couche Commune   //
///////////////////////

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

/////////////////////////////////////////////////////////////////
//   Couche Equipement filtrée sur les équipements de l'asso  //
///////////////////////////////////////////////////////////////

//Appel de la couche equipement

function equipAssoCouche(){
var xhttp2 = new XMLHttpRequest();
//lecture de la connexion au fichier php (2 variables cf. biblio)
xhttp2.onreadystatechange = function() {
    if (this.readyState == 4 && this.status ==200) {
        //récupération du résultat de la requête sql et parcours de la couche :
        let response = JSON.parse(xhttp2.responseText)
        //appel de la couche
        equipementsAsso.clearLayers();
        var equipement = L.geoJSON(response, {
            //application du style
            pointToLayer : function(feature,latlng){
                return L.marker(latlng)
            },
            //application du filtre
            filter: function(feature,layer) {
                for (let item of equipAsso) {
                    if (feature.properties.id_asso == item) return true
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
        }).addTo(equipementsAsso);

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
        equipementsAsso.addTo(map)
    }
    };
xhttp2.open("GET", "php/equipement.php",true);
xhttp2.send();
}

equipAssoCouche();

// Affichage des équipements de la couche association :

$('#equipAsso').change(function(){    
    if (this.checked) {
        console.log(this.value);
        equipAsso.push(this.value)   
    }
    
    else{console.log(this.value);
        var index = categoriesAsso.indexOf(this.value);
        if (index > -1) {
            equipAsso.splice(index, 1);
        }}
    
    console.log(equipAsso);
equipAssoCouche();
});

////////////////////////////////////////////////////////////////////
//    Couche Association Filtrée avec le seul point de l'asso    //
//////////////////////////////////////////////////////////////////

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
            pointToLayer : utilisateurStile,
            //application du filtre
            filter: function(feature,layer) {
                if (feature.properties.id_asso == id_asso) return true
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
        map.setView(association.getBounds().getCenter(), 14);           
    }
    };
xhttp4.open("GET", "php/association.php",true);
xhttp4.send();


function majCouche(){
  
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
                            if (feature.properties.type_equip == item) return true
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
                    return L.marker(latlng)
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
                    return L.marker(latlng)
                },
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