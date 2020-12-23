# Tutoriel Postgresql / PHP / Leaflet


## Récupérer des données depuis leaflet :

Dans ce tutoriel, nous allons récupérer l'emplacement de point lors d'un clic avec la souris. Pour réaliser cette opération, nous avons besoin de quatre fichiers différents, le fichier HTML pour l'affichage web, le fichier js pour la définition des variables à afficher et enfin le fichier css pour le style des éléments. 


#### Fichier php

C'est dans le fichier php que la connexion à la base de données se fait, puis la récupération des variables et enfin le remplissage de la base de données.

    //Connexion à la base de donnée postgresql :
    $dbconn = pg_connect('host=localhost dbname=votre_base_de_donnee user=votre_user password=votre_mdp')
    or die('Could not connect: ' . pg_last_error());

    //Récupération des variables lat et longitude :
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    //Définition de la requête d'insertion des points :
    $query = "INSERT INTO point (geom) VALUES (ST_SetSRID( ST_Point( " . strval($lng) . ", " . strval($lat) . "), 4326));";

    //lancement de la requête :
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

    //affichage dans le navigateur dans l'onglet réseau :
    echo = $query

#### Fichier js

Dans le fichier javascript, il s'agit de récupérer les coordonnées lors d'un clic sur la carte puis de les envoyer via une requête xhr.
>Les objets XMLHttpRequest (XHR) permettent d'interagir avec des serveurs. On peut récupérer des données à partir d'une URL sans avoir à rafraîchir complètement la page. Cela permet à une page web d'être mise à jour sans perturber les actions de l'utilisateur. XMLHttpRequest est beaucoup utilisé par l'approche AJAX.

Source : [MDN Web Docs](https://developer.mozilla.org/fr/docs/Web/API/XMLHttpRequest)

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

    //Récupération des coordonnées du clic de la carte
    map.on('click', function(e){
        lat = e.latlng.lat;
        lng = e.latlng.lng;
        //envoi de la fonction
        launchXHR(lat, lng)
    })
    
    //fonction qui envoie les données récupérées dans la base de données
    function launchXHR(lat, lng) {
        //création de la requête         
        var xhttp = new XMLHttpRequest();  
        //requête sur le fichier php avec la méthode post
        xhttp.open("POST", "php/test.php", true);
        //
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        //envoie des données
        xhttp.send("lat="+lat+"&lng="+lng);
    }

## Afficher des données depuis postgresql :

Dans ce nouveau cas, il s'agit de faire tout à fait l'inverse de ce que nous avons fait dans la première partie. Nous allons récupérer des données depuis la base postgresql et les afficher sur la carte leaflet générée sur la page web. De la même façon que pour la première opération, nous allons utiliser les requêtes xhr pour accèder au contenu de la base de données.

#### Fichier php

Le fichier php se décompose en trois phases :

*Connexion à la base de données,

*Sélection des données à afficher via une requête sql,

*Envoi des données

Il y a un soucis avec la méthode développée avec la propriété geojson qui vient des deux méthodes suivantes : St_asGeosjson et St_transform qui permet d'afficher les données. Or dans cette propriété, il n'y a que la geométrie et non la totalité des informations qui sont liés aux éléments, nous ne pouvons donc pas effectué des discrétisation par exemple.
La solution peut venir en transformant directement les données en 4326, avant de mettre les données dans la base SQL.Pour le calculs liés au générateur de potentialité, la transformation se fera pour les couches concernées directement en sql avant de lancer les scripts.

    <?php
    //Connexion à la base de donnée postgresql :
    $dbconn = pg_connect("host=localhost port=5432 dbname=asso_test_2 user=postgres password=mdp")
    or die('Could not connect: '. preg_last_error());
    
    //Méthode avec St_AsGeojson et St_Transform (ouche utilisée commune)
    //Création de la requête de sélection des données avec une transformation dans l'EPSG 4326 et ajout d'un champs geojson à partir du st_AsGeojson
    //Préparation de la requête :
    $query = "SELECT * ,st_AsGeojson(ST_Transform(geom, 4326)) as geojson from commune;";
    //récupération du résultat de la requête dans une variable :
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    //Traitement de la requête pour récupérer un tableau
    $array = pg_fetch_all($result);
    //Envoi du tableau
    echo json_encode($array);
    ?>

    //Appel de la couche équipement pour test sur une couche de points.
    //L'appel se fait différement au lieu de renvoyer un tableau, nous renvoyons la première ligne du tableau qui correspond
    <?php
    // Connexion à la BDD
    $dbconn = pg_connect("host=localhost port=5432 dbname=asso_test_2 user=postgres password=mdp")
    or die('Could not connect: '. preg_last_error());
    //Préparation de la requête :
    $query = "SELECT json_build_object(
            'type', 'FeatureCollection',
            'features', json_agg(ST_AsGeoJSON(equip_4326.*)::json)
        ) 
        from equip_4326;";

    //récupération du résultat de la requête dans une variable :
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

    //Renvoi du résultat : nous extrayons la première ligne du tableau (même si il n'y en a qu'une en réalité)
    echo pg_fetch_result($result, 0, 0);

    ?>

#### Fichier JS

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

## Fichiers communs

#### Fichier HMTL 

    <! DOCTYPE html>
    <html>
        <head>
            <meta charset = "utf-8" />
            <title> Titre </title>
            <script src="leaflet/leaflet.js"></script>      
            <link rel = "stylesheet" href="leaflet/leaflet.css" />
            <link rel = "stylesheet" href="css/style.css" />
        </head>    
        <body>
            <div id = "bloc_page">
                <header>                    
                    <div id="logo">
                        <a href="index.html">
                            <img src = img/wima.png alt = "wima">
                        </a>                            
                    </div> 
                    <nav>
                        <ul>
                            <li><a id="accueil_on" href ="index.html">Accueil</a></li>
                            <li><a id="connexion_off" href ="connexion.html">Connexion</a></li>
                            <li><a id="information_off" href ="info.html">S'informer</a></li>
                            <li><a id="association_off" href ="association.html">Associations</a></li>
                        </ul>
                    </nav>   
                </header>
                <div id="fenetre_principale">                    
                    <aside>
                        <div id="search">                           
                            <input type="text" placeholder="Search.." class="searchbar">
                            <button type="submit"><i class="fa fa-search"></i></button>
                        </div>
                        <p id = "tall">Gestion des données que vous souhaitez afficher</p>
                        <form class= "sidebar">
                            <h3>Equipements</h3>                      
                            <input type="checkbox" name="checkbox" class="cm-toggle" id ='amap' >Amap<br>
                            <input type="checkbox" name="checkbox" class="cm-toggle" id ='composteur' >Composteur<br>
                            <input type="checkbox" name="checkbox" class="cm-toggle" id ='gaspillage' >Anti-Gaspillage<br>                            
                        </form> 
                    </aside>                
                    <div id = "map"></div>
                </div>
                <footer>
                    <div id="footer_text">
                        <p> CCBR - Teams enhaced by Virtual scriptor.com - tall@gmail.com</p>
                    </div>                    
                    <div id="lien_footer">                    
                        <a class="picto-item" aria-label="Site du master Geonum" href="https://mastergeonum.org/" target="_blank">
                            <img src = img/geonum.png id ="logo_geonum" alt="geonum">
                        </a>
                        <a class="picto-item" aria-label="Site de l'université Lyon 2" href="https://www.univ-lyon2.fr/" target="_blank">
                            <img src = img/Lyon_2.png id ="logo_lyon2" alt="lyon2">
                        </a>
                        <a class="picto-item" aria-label="Site de l'ENS" href="http://www.ens-lyon.fr//" target="_blank">
                            <img src = img/Ens.png id ="logo_ens" alt="ens">
                        </a>
                        <a class="picto-item" aria-label="Site d'Anciela" href="https://www.anciela.info/" target="_blank">
                            <img src = img/anciela.jpg id ="logo_anciela" alt="anciela">
                        </a>
                    </div>
                </footer>
            </div>           
        </body>
        <script src ="js/script.js"></script>
    </html>

#### Fichier css

    *{
        box-sizing: border-box;
    }


    body{   
        background-color: #9FC1B8;
        color : #181818;
        background: url("../img/bellecour.jpg") center no-repeat;
        -webkit-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
        font-family: 'Abadi',Arial, Helvetica, sans-serif;    
    }

    #bloc_page
    {
        height : 100%;
        display: flex;
        flex-flow: column;
        font-family: 'Abadi', Arial, Helvetica, sans-serif;
    }

    /*header*/
    header{
        display: flex;    
        justify-content: space-between;
        align-items: flex-end;
        border-radius: 5px;
        order : 0;
        margin-bottom: 5px;
        height: 6em;
    }

    #titre_principal{
        display: flex;
        flex-direction: column;
        width: 40%;
    }

    #logo
    {
        display: flex;
        position: absolute;
        top : 10px;
        width: 40%;
    }

    #logo img
    {
        margin-left : 10px;
        width: 82px;
        height: 82px; 
    }


    nav ul{
        list-style-type: none;
        display: flex;
        height: 80px;
        margin-left: 100px;    
        /*border: 6px solid violet;*/ 
    }

    nav li{    
        width: 125px;    
        margin-right:100px;    
    }
    nav a
    {   
        text-align: center;
        width: 150px;
        position: absolute;
        padding-top: 25px;
        padding-bottom: 30px;
        font-size: 1.25em;
        color: rgb(10, 2, 2);        
        font-weight: bolder;    
        text-decoration: none;     
    }

    #accueil_on{    
        color: white;
        background-color: #006161;
        border-bottom : 4px solid #006161;  
    }

    #accueil_off{  
        border-bottom : 4px solid #006161;  
    }

    #accueil_off:hover{    
        color: white;
        background-color: #006161;
        border-bottom : 4px solid #006161;  
    }

    #connexion_on{
        color: white;
        background-color: #257299;
        border-bottom : 4px solid #257299;
    }

    #connexion_off{
        border-bottom : 4px solid #257299;
    }

    #connexion_off:hover{
        color: white;
        background-color: #257299;
        border-bottom : 4px solid #257299;
    }

    #deconnexion_on{
        color: white;
        background-color: #257299;
        border-bottom : 4px solid #257299;
        padding-bottom: 8px;
    }

    #petit{
        font-size: 0.5em;
    }


    #information_on{
        color: white;
        background-color: #5f968e;
        border-bottom : 4px solid #5f968e;
    }

    #information_off{
        border-bottom : 4px solid #5f968e;
    }

    #information_off:hover{
        color: white;
        background-color: #5f968e;
        border-bottom : 4px solid #5f968e;
    }

    #association_on{
        color: white;
        background-color: #f9ca1f;
        border-bottom : 4px solid #f9ca1f;
    }

    #association_off{
        border-bottom : 4px solid #f9ca1f;
    }

    #association_off:hover{
        color: white;
        background-color: #f9ca1f;
        border-bottom : 4px solid #f9ca1f;
    }

    /*section principale*/

    aside{
        order : 1;
    }

    #fenetre_principale{
        display: flex;
        height: 33em;
    }

    aside{
        flex: 1 0 0;
        margin-right: 5px;
        display : flex;
        flex-direction: column;      
    }

    #tall{
        text-align: center;
        color: white;
    }

    #search{
        border :1px solid;
        border-radius: 5px;
    }


    .searchbar{
        width: 75%;
        text-align: center;
        align-self: center;
        border-radius: 5px 0px 0px 5px;    
    }

    /* Style the submit button */
    #search button {
        float: right;
        width: 25%;
        background: rgba(43, 110, 92, 0.15);
        color: white;
        font-size: 17px;
        border: none;
        cursor: pointer;
        border-radius: 0px 5px 5px 0px;
    }
    
    #search button:hover {
        background: rgba(43, 110, 92);
    }
    
    aside h1{    
        text-align: center;
    }


    #map {
        height: 100%;
        width: 100%;
        order:2;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-flow: column;
        flex: 3 0 0;
        box-shadow: 0px 5px 10px rgb(31, 49, 31);
    }


    #info{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 33em;
    }

    .construction {
        text-align: center;
        background-color: white;
        padding: 25px;
        border-radius: 5px;
        box-shadow: 0px 5px 10px rgb(31, 49, 31);
    }

    .construction a{
        text-decoration: none;
        color : black;
    }
    .construction a:hover{
        text-decoration: none;
        color : rgb(40, 71, 56);
        font-weight: bold;
        background-color: rgba(40, 71, 56, 0.589);
        padding: 10px;
        border-radius: 5px;
    }

    footer{
        margin-top: 15px;
        order:3;
        display : flex;
        height : 50px;
        box-shadow: 0px 5px 10px rgb(31, 49, 31);;
        align-items: flex-end;
        justify-content: center;
        border-bottom: 1px solid;
        border-radius: 5px;
        background-color: rgba(78, 123, 124, 0.582);
        font-weight: bold;
    }

    footer img{
        margin-left: 15px;
        width: 35px;
        height: 35px;
    }

    footer a{
        text-decoration: none;
    }

    #lien_footer{
        position :absolute;
        right : 15px;
    }

### Bibliographie

Requête [XMLHttpRequest (XHR)](https://developer.mozilla.org/fr/docs/Web/API/XMLHttpRequest)
