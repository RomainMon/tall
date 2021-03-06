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
        <script src='js/jquery_stat.js'></script>      
        <!-- appel de chart.js -->
        <script src="js/package/dist/Chart.js"></script>
        <!-- export pdf library -->
        <script src="html2pdf.js-master/dist/html2pdf.bundle.min.js"></script>
        <!-- lien vers mon JS PDF -->
        <script src="js/pdf.js"></script>
        <!-- appel de screen shooter -->
        <script src="https://unpkg.com/leaflet-simple-map-screenshoter"></script>
        <!-- appel du script screen shot js -->
        <script src='js/screen_shot.js'></script>
        <!-- ajout d'une library d'icon -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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
                    <li><a id="accueil" href ="index.php">Accueil</a></li>
                    <li><a id="connexion" href ="deconnexion.php">Déconnexion</a></li>
                    <li><a id="contact" href ="html/contact.html">Contact</a></li>
                    <li><a id="profil" href ="profil_asso.php">Profil</a></li>
                </ul>
            </nav>   
        </header>
        <div id="fenetre_principale">                    
            <aside>
                <!-- partial:index.partial.html -->
                <div id="menu-tab"><!----------------tableau-01---------------------------------->
                    <div id="page-wrap">
                        <div class="tabs">
                            <!----------------onglet-01-accueil-------------------------->
                            <div class="tab"><input id="tab-1" checked="checked" name="tab-group-1" type="radio" /> <label for="tab-1">Accueil</label>
                                <div class="content">  
                                    <p hidden id="id_asso"><?= $_SESSION['id_asso']; ?></p>
                                    <p hidden id="id_cate"><?= $_SESSION['id_cate']; ?></p>
                                    
                                    <h3>Bonjour, <?= $_SESSION['nom_asso']; ?></h3>  
                                    <?php
                                        $q = $db->prepare("
                                        SELECT asso.adrs_numvo, asso.adrs_typev, asso.adrs_libvo, com.code_post, com.nom_com FROM association as asso join commune as com on (asso.adrs_codei = com.insee_com) 
                                        WHERE id_asso = :id_asso;
                                        ");
                                        $q->execute([
                                            'id_asso'=> $_SESSION['id_asso']
                                        ]);                   
                                        //récupération du résultat de la requête dans une variable :
                                        $adresse_user= $q->fetchAll();
                                        foreach($adresse_user as $value){                                
                                        ?>
                                        <p>Votre adresse : <?= $value['adrs_numvo']; ?> <?= $value['adrs_typev']; ?> <?= $value['code_post']; ?> <?= $value['nom_com']; ?></p>
                                        <?php
                                        }
                                    ?>
                                    <form>
                                        <input checked="checked" type="checkbox" class="equipAsso" name="equipAsso" id="equipAsso" value =<?= $_SESSION['id_asso']; ?>> Vos équipements<br>
                                        <!-- création du select avec envoi de la fonction de zoom sur l'equipement' -->
                                        <select name ="choix_equipement" id="choix_equipement">
                                            <option selected="selected">zoom sur un équipement</option>
                                            <?php
                                            // récupération des equipement de l'association
                                            $q = $db->prepare("SELECT nom, id_equip FROM equipement WHERE id_asso = :id_asso ORDER by nom ;");
                                            $q->execute(['id_asso' => $_SESSION['id_asso']]);                    
                                            //récupération du résultat de la requête dans une variable :
                                            $liste_equipement= $q->fetchAll();
                                            // Iterating through the product array
                                            foreach($liste_equipement as $value){
                                            ?>
                                            <!-- affichage des différentes communes dans le select -->
                                            <option value="<?php print($value[1]); ?>"><?php print($value[0]); ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </form>                            
                                    <br>
                                    <form id="legende_asso">
                                        <p>Les associations</p><br>
                                        <?php
                                        $q = $db->prepare("SELECT * FROM CATEGORIE ORDER by id_cate;");
                                        $q->execute();
                                        //récupération du résultat de la requête dans une variable :
                                        $liste_cate= $q->fetchAll();

                                        foreach($liste_cate as $value){ 
                                            ?>
                                            <input type="checkbox" class="liste_cate" name="<?php print($value[0]) ?>" id="<?php print($value[0]) ?>" value =<?php print($value[0]) ?>> 
                                            <label for = "<?php print($value[0]) ?>"></label><?php print($value[1]) ?><br>
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
                                            <input type="checkbox" class="liste_equip" name="<?php print($value[0]) ?>" id="<?php print($value[0]) ?>" value =<?php print($value[0]) ?>> 
                                            <?php print($value[0]) ?><br>
                                            <?php
                                            }
                                            ?>
                                    </form>
                                </div>
                            </div>
                            <!----------------onglet-02-Statistiques-------------------------->
                            <div class="tab"><input id="tab-2" name="tab-group-1" type="radio" /> <label for="tab-2">Statistiques</label>
                                <div class="content">                            
                                    <form id="stat">                            
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
                                        <button name="stat" id="btn_stat" type="button">Envoyer le bouzin</button>
                                        
                                        <button name="PDF" type="button" class="btn" onclick="generatePDF()"><i class="fa fa-download"></i> Télécharger</button>
                                    </form>
                                    
                                    <!-- les valeurs sont récupérées dans une balise cachée  -->
                                    <p hidden class ="nom_cate"></p>                        
                                    <!-- définition de la balise ou sera créé le graph -->
                                    <div id="suppression"> </div>
                                    <canvas id="myChart" width="300" height="300"></canvas>
                                </div>
                            </div>
                            <!----------------onglet-03-Potentialite-------------------------->
                            <div class="tab"><input id="tab-3" name="tab-group-1" type="radio" /> <label for="tab-3">Potentialité</label>
                                <div class="content">                            
                                    <form id="potentiel">                            
                                        <label for="choix_commune2">Choix de la commune</label>
                                        <!-- création du select avec envoi de la fonction de zoom sur la ville choisie -->
                                        <select name ="choix_commune2" id="choix_commune2" onchange="zoomVille(this);">
                                        <option selected="selected">Commune</option>
                                        <?php
                                        // récupération des communes du GL
                                        $q = $db->prepare("SELECT insee_com, nom_com  FROM commune ORDER by nom_com;");
                                        $q->execute();                    
                                        //récupération du résultat de la requête dans une variable :
                                        $liste_commune= $q->fetchAll();
                            
                                        // Iterating through the product array
                                        foreach($liste_commune as $value){
                                        ?>
                                        <!-- affichage des différentes communes dans le select -->
                                        <option value="<?php print($value[0]); ?>"><?php print($value[1]); ?></option>
                                        <?php
                                        }
                                        ?>
                                        </select>                            
                                        <br>
                                        <!-- liste déroulante pour les équipements -->
                                        <!-- liste déroulante pour les équipements -->
                                        <label for="choix_type_equip">Type d'équipement</label>
                                        <select name ="choix_type_equip" id="choix_type_equip">
                                            <option value="" selected= "selected">Choississez un des items</option>
                                            <?php
                                            // récupération des communes du GL
                                            $q = $db->prepare("SELECT distinct(type_equip) FROM equipement ORDER by type_equip;");
                                            $q->execute();                    
                                            //récupération du résultat de la requête dans une variable :
                                            $liste_type_equip= $q->fetchAll();
                                
                                            // Iterating through the product array
                                            foreach($liste_type_equip as $value){
                                            ?>
                                            <!-- affichage des différentes communes dans le select -->
                                            <option value="<?php print($value[0]); ?>"><?php print($value[0]); ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>                            
                                        <br>
                                    <!-- bouton pour le calculateur de potientialité et la création de PDF -->
                                    <button name="stat" id="btn_potentiel" type="button">Lancer la simulation</button>
                                    </form>
                                    <div hidden id="loading">
                                        <p>Calcul de potentialité en cours...</p>
                                        <img id="img_chargement" src="img/load_animation.gif" alt="Loading" />
                                    </div>
                                    <div hidden id="resultat_calcul"><p>Calcul fini !</p></div>
                                </div>
                                                   
                            </div>
                            <!----------------onglet-04-Utilisateur-------------------------->
                            <div class="tab"><input id="tab-4" name="tab-group-1" type="radio" /> <label for="tab-4">Bénévole</label>
                                <div class="content">
                                    <p>Cliquez sur un point de la carte et sélectionnez un rayon en mètres pour voir les utilisateurs potentiels autour de ce point</p>
                                    0 <input type="range" name="rangeInput" id="rangeInput" min="0" max="2500" step="250" onchange="updateTextInput(this.value);searchBenevole(this.value);"> 2 500
                                    <p>Distance en mètres : </p><p id="rangeText" value=""></p>
                                    <p>Nombre de particuliers intéressés par votre domaine</p>
                                    <p id = "result_popbene"  class = "popbene"></p>                                   
                                </div>
                           </div>
                        </div>                       
                    </div>
                </div>
            </aside>                
            <div id = "map"></div>
        </div>           
    </body>
    <!-- appel du script qui permet d'executer le script php contenant le script python -->
    
    <script src ="js/icones.js"></script>
    <script src ="js/script_association.js"></script>
    <script src='script_python.js'></script>
    <!-- appel du script qui permet de faire un screen shot de la carte -->
    <script>L.simpleMapScreenshoter().addTo(map)</script>
</html>
