<?php session_start(); //ouverture d'une session 

    // création d'un cookie
    // setcookie('pseudo', $_SESSION['email'], time()+(30*24*3600));
    // var_dump($_COOKIE);

    // // nettoie la session avant la déconnection
    //session_unset();
    // // détruit une session, la déconnecte
    session_destroy();
    // connexion à la db
    include 'include/database.php';
    //  recupération de la varibable db pour faire des requêtes
    global $db;
?>

<!DOCTYPE html>
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
    </head>    
    <body>
        <div id = "bloc_page">
            <header>                    
                <div id="logo">
                    <a href="index.html">
                        <img src = "img/logo.svg" alt = "TALL">
                        <div id="Ton_action_locale_lyonnaise">
                            <span>Ton action locale lyonnaise</span>
                        </div>
                    </a>                            
                </div> 
                <nav>
                    <ul>
                        <li><a id="accueil" href ="index.php">Accueil</a></li>
                        <li><a id="connexion" href ="connexion.php">Connexion</a></li>
                        <li><a id="contact" href ="html/contact.html">Contact</a></li>
                    </ul>
                </nav>   
            </header>
            <div id="fenetre_principale">                    
                <aside>
                    <form id="legende_asso">
                        <p>Les associations</p><br>
                        <?php
                        $q = $db->prepare("SELECT * FROM CATEGORIE ORDER by id_cate;");
                        $q->execute();
                        //récupération du résultat de la requête dans une variable :
                        $liste_cate= $q->fetchAll();

                        foreach($liste_cate as $value){ 
                            ?>
                            <input checked="checked" type="checkbox" class="liste_cate" name="<?php print($value[0]) ?>" id="<?php print($value[0]) ?>" value =<?php print($value[0]) ?>> 
                            <?php print($value[1]) ?><br>
                            <?php
                            }
                            ?>
                    </form>
                    <form id="legende_equip">
                        <p>Les équipements</p>
                        <br>
                        <?php
                        $q = $db->prepare("SELECT distinct(type_equip) FROM equipement ORDER by type_equip;");
                        $q->execute();
                        //récupération du résultat de la requête dans une variable :
                        $liste_equip= $q->fetchAll();

                        foreach($liste_equip as $value){ 
                            ?>
                            <input checked="checked" type="checkbox" class="liste_equip" name="<?php print($value[0]) ?>" id="<?php print($value[0]) ?>" value =<?php print($value[0]) ?>> 
                            <?php print($value[0]) ?><br>
                            <?php
                            }
                            ?>
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
    <script src ="js/icones.js"></script>
    <script src ="js/script.js"></script>
</html>
