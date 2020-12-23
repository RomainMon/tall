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
        response.forEach((el) => {
            L.geoJSON(JSON.parse(el.geojson),{
            //application du style
            style: myStyle,
            }).addTo(map)
            })
        }
    };
//requête du fichier php
xhttp.open("GET", "php/commune.php",true);
//envoie de la commande au fichier
xhttp.send();

//Appel de la couche equipement
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
                layer.bindPopup(
                "Type : "+ feature.properties.type_equip +
                "<br>Nom : " + feature.properties.nom+
                "<br>Adresse : "+ feature.properties.adresse + 
                "<br>Commune : " +feature.properties.code_post + " " + feature.properties.nom_com +
                "<br>Précision d'emplacement : " + feature.properties.infoloc +
                "<br>Type de site : " + feature.properties.type_site +
                "<br>Site Internet : " + feature.properties.site_inter +
                "<br>Mail : " + feature.properties.mail               
                )
            }
        }).addTo(map)
    }
    };
xhttp2.open("GET", "php/equipement.php",true);
xhttp2.send();

map.on('click', function(e){
    lat = e.latlng.lat;
    lng = e.latlng.lng;
    launchXHR(lat, lng)
})

function launchXHR(lat, lng) {
    // var params = JSON.stringify({ id: 1 });
    var xhttp = new XMLHttpRequest();  
    
xhttp.open("POST", "php/clic.php", true);

xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhttp.send("lat="+lat+"&lng="+lng);
}