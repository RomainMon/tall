<?php session_start();
?>

<html>
    <head>
        <meta charset = "utf-8" />
        <title> TALL </title>
        <script src="leaflet/leaflet.js"></script>      
        <link rel = "stylesheet" href="leaflet/leaflet.css" />
        <link rel = "stylesheet" href="css/style.css" />
        <!-- Icônes -->
		<script src="https://kit.fontawesome.com/3b2bc082a4.js" crossorigin="anonymous"></script>
		<!-- Police -->
		<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet"> 
        <!-- appel de l'api google jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <!-- appel du script js jquery -->
        <script src='js/jquery_itineraire.js'></script>
    </head>    
    <body>
        <div id = "bloc_page">
            <header>                    
                <div id="logo">
                    <a href="index.php">
                        <img src = "img/logo.svg" alt = "TALL">
                        <div id="Ton_action_locale_lyonnaise">
                            <span>Ton action locale lyonnaise</span>
                        </div>
                    </a>                            
                </div> 
                <nav>
                    <ul>
                        <li><a id="accueil" href ="index.html">Accueil</a></li>
                        <li><a id="connexion" href ="deconnexion.php">Déconnexion</a></li>
                        <li><a id="contact" href ="info.html">Contact</a></li>
                        <li><a id="profil" href ="profil.php">Profil</a></li>
                    </ul>
                </nav>   
            </header>
            <div id="fenetre_principale">                    
                <aside>
                    <div id="Tissu_associatif_lyonnais">
                        <span>Vos préférences</span>
                        <?php 
                        foreach($_SESSION['preference'] as $value){
                        ?>
                        <p hidden class = "categorie"><?php print($value); ?></p>
                        <?php
                        }
                        ?>
                        <h3><?= $_SESSION['prenom']; ?> <?= $_SESSION['nom']; ?></h3>  
                        <p> <?= $_SESSION['date_inscription']; ?></p>                        
                    </div>
                    <div id = "itineraire">
                        <span>Calculs d'itinéraire le plus court</span>
                        <p>Cliquer sur un point d'association ou d'équipement sur la carte puis sur le bouton ci-dessous</p>
                        <button id="itineraire_button" onClick="itineraireDisplay()">test</button>
                        <!-- <button id="itineraire_button_bis">test</button> -->                        
                        <p id="test_iti" class = "poulpy"></p>
                        
                   
                </aside>                
                <div id = "map"></div>
                <aside id = "aside_left">
                    <div id="Tissu_associatif_lyonnais">
                            <span>Production de camenbert</span>
                </aside>
            </div>
            <footer>
                <div id="footer_text">
                    <p> CCBR - Teams enhaced by Virtual scriptor.com - tall@gmail.com</p>
                </div>                    
                <div id="lien_footer">                    
                    <a class="picto-item" aria-label="Site du master Geonum" href="https://mastergeonum.org/" target="_blank">
                        <img src = img/geonum.png width="2.5%" id ="logo_geonum" alt="geonum">
                    </a>
                    <a class="picto-item" aria-label="Site de l'université Lyon 2" href="https://www.univ-lyon2.fr/" target="_blank">
                        <img src = img/Lyon_2.png width="5%" id ="logo_lyon2" alt="lyon2">
                    </a>
                    <a class="picto-item" aria-label="Site de l'ENS" href="http://www.ens-lyon.fr//" target="_blank">
                        <img src = img/Ens.png width="5%" id ="logo_ens" alt="ens">
                    </a>
                    <a class="picto-item" aria-label="Site d'Anciela" href="https://www.anciela.info/" target="_blank">
                        <img src = img/anciela.jpg width="2.5%" id ="logo_anciela" alt="anciela">
                    </a>
                </div>
            </footer>
        </div>           
    </body>
    <script src ="js/script_utilisateur.js"></script>
</html>
