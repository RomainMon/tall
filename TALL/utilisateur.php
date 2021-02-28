<?php session_start();
// connexion à la db
include 'include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;
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
        <script src='js/jquery_stat.js'></script>
        <!-- appel de char.js -->
        <script src="js/package/dist/Chart.js"></script>

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
                        <!-- récupération dans une balise cachée des éléments de session pour les catégories d'association et l'id utilisateur  -->
                        <?php 
                        foreach($_SESSION['preference'] as $value){
                            ?>
                            <p hidden class = "categorie"><?php print($value); ?></p>
                            <?php
                        }
                        ?>
                        <p id="id_utilisateur"><?= $_SESSION['id_utilisateur']; ?></p>
                        <h3>Bonjour, <?= $_SESSION['prenom']; ?> <?= $_SESSION['nom']; ?></h3>  
                        <?php
                            $q = $db->prepare("
                                WITH adresse_user AS (SELECT ad.nom_1, ad.rep, ad.code_post, ad.numero FROM utilisateur AS u, adresse AS ad
                                WHERE id_utilisateur = :id_user AND u.id_adresse = ad.id_adresse)
                                SELECT ad.numero, ad.rep, ad.nom_1, co.code_post, co.nom_com FROM commune AS co
                                JOIN adresse_user AS ad ON (co.code_post = ad.code_post);
                            ");
                            $q->execute([
                                'id_user'=> $_SESSION['id_utilisateur']
                            ]);                   
                            //récupération du résultat de la requête dans une variable :
                            $adresse_user= $q->fetchAll();
                            foreach($adresse_user as $value){                                
                            ?>
                            <p>Mon adresse : <?= $value['numero']; ?> <?= $value['rep']; ?> <?= $value['nom_1']; ?> <?= $value['code_post']; ?> <?= $value['nom_com']; ?></p>
                            <?php
                            }
                        ?>



                    </div>
                    <div id = "itineraire">
                        <span>Calculs d'itinéraire le plus court</span>
                        <p>Cliquer sur un point d'association ou d'équipement sur la carte puis sur le bouton ci-dessous</p>
                        <button id="itineraire_button" onClick="itineraireDisplay()">Lancer le calcul</button>                                            
                        <p id="test_iti" class = "poulpy"></p>
                        
                   
                </aside>                
                <div id = "map"></div>
                <aside id = "aside_right">
                    <div id="statistiques">
                        <span>Production de camembert</span><br>
                        <form>                            
                            <label for="choix_commune">Choix de la commune</label>
                            <!-- création du select avec envoi de la fonction de zoom sur la ville choisie -->
                            <select name ="choix_commune" id="choix_commune" onchange="zoomVille(this);">
                            <option selected="selected">Commune</option>
                            <?php
                            // récupération des communes du GL
                            $q = $db->prepare("SELECT distinct(nom_com) FROM vue_adresse ORDER by nom_com;");
                            $q->execute();                    
                            //récupération du résultat de la requête dans une variable :
                            $liste_commune= $q->fetchAll();
                
                            // Iterating through the product array
                            foreach($liste_commune as $value){
                            ?>
                            <!-- affichage des différentes communes dans le select -->
                            <option value="<?php print($value[0]); ?>"><?php print($value[0]); ?></option>
                            <?php
                            }
                            ?>
                            </select>                            
                            <br>
                            <!-- liste déroulante pour les associations ou équipements -->
                            <label for="choix_asso_equip">Association ou Equipement</label>
                            <select name ="choix_asso_equip" id="choix_asso_equip">
                                <option value="" selected= "selected">Choississez un des items</option>
                                <option value="association">Association</option>
                                <option selected="equipement">equipement</option>
                            </select><br>

                            <!-- bouton qui lance la production du graphique : appel de la fonction dans le script js -->
                            <button name="stat" id="stat" onClick="makeChart()" type="button">Envoyer le bouzin</button>
                        </form>
                        
                        <!-- les valeurs sont récupérées dans une balise cachée  -->
                        <p hidden class ="nom_cate"></p>                        
                        <!-- définition de la balise ou sera créé le graph -->
                        <canvas id="myChart" width="300" height="300"></canvas>
                        
                    </div>
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
