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
        <link rel="shortcut icon" type="image/ico" href="img/favicon.ico"/>
        <link rel="icon" type="image/png" href="img/logo.png" />
        <script src="leaflet/leaflet.js"></script>      
        <link rel = "stylesheet" href="leaflet/leaflet.css" />        
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
        <!-- appel bootstrap , appel nécessaire en début de script pour le menu horizontal -->
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
        <link rel = "stylesheet" href="css/style_utilisateur.css" />

    </head>    
    <body>
        <!-- Menu horizontal -->
        <nav class="navbar navbar-default" role="navigation">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><img src = "img/logo.svg" alt = "logo"></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a class="nav-link" href="#">Accueil
                        <span class="sr-only">(current)</span></a>
                    </li>
                    <li>
                        <a class="nav-link" href="profil_asso.php">Profil</a>
                    </li>
                    <li>
                        <a class="nav-link" href="accueil.php">Déconnexion</a>
                    </li>
                    <li>
                        <a class="nav-link" href="html/contact_asso.html">Contact</a>
                    </li>

                </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>

        <!-- Onglet menu vertical -->
        <ul class="flex-container">
            <li class="flex-item" id="flex-menu">
                <div class="row">
                    <div class="col-md-4 col-sm-5">
                    <div id="ensemble_onglets">
                        <div class="tabs-left">
                            <ul class="nav nav-tabs">
                                <!-- Onglet 1 = légendes, paragraphe utilisateur -->
                                <li class="active"><a href="#1" data-toggle="tab" onClick ="equipAssoCouche();"><img src = "img/legende.svg" alt = "logo"></a></li>
                                <!-- Onglet 2 = buffer distance proche de l'uilisateur --> 
                                <li><a href="#2" data-toggle="tab" onClick ="equipAssoCouche();"><img src = "img/stat.svg" alt = "logo"></a></li>
                                <!-- Onglet 3 = itinéraire -->
                                <li><a href="#3" data-toggle="tab" onClick ="equipAssoCouche();"><img src = "img/amc.svg" alt = "logo"></a></li>
                                <!-- Onglet 4 ! statistiques -->
                                <li><a href="#4" data-toggle="tab"onClick ="equipAssoCouche();"><img src = "img/autour.svg" alt = "logo"></span></a></li>
                                
                            </ul>
                        </div>
                            <!-- Ouverture de la div contenant le menu vertical et la carte -->
                        <div class="tab-content">

                            <!-- Onglet 1 --> 
                            <div class="tab-pane active" id="1">
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
                                    <input checked="checked" type="checkbox" class="equipAsso" name="equipAsso" id="equipAsso" value =<?= $_SESSION['id_asso']; ?>>
                                    <label for = "equipeAsso"></label>      
                                    <h6 id = "equipement_de_association">Vos équipements</h6>
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
                                <form id="legende_asso">
                                    <h3>Les associations</h3>
                                    <?php
                                    $q = $db->prepare("SELECT * FROM CATEGORIE ORDER by id_cate;");
                                    $q->execute();
                                    //récupération du résultat de la requête dans une variable :
                                    $liste_cate= $q->fetchAll();

                                    foreach($liste_cate as $value){ 
                                        ?>
                                        <input type="checkbox" class="liste_cate" name="<?php print($value[0]) ?>" id="<?php print($value[0]) ?>" value =<?php print($value[0]) ?>> 
                                        <label for = "<?php print($value[0]) ?>"></label>
                    
                                        <h6 id = "<?php print($value[0]) ?>_2"><?php print($value[1]) ?></h6>
                                        <!-- id va être égal à 007070_2 -->
                                        <?php
                                        }
                                        ?>
                                </form>
                                <form id="legende_equip">
                                    <h3>Les équipements</h3>                    
                                    <?php
                                    $q = $db->prepare("SELECT distinct(id_type_equip),type_equip FROM equipement ORDER by type_equip;");
                                    $q->execute();
                                    //récupération du résultat de la requête dans une variable :
                                    $liste_equip= $q->fetchAll();

                                    foreach($liste_equip as $value){ 
                                        ?>
                                        <input type="checkbox" class="liste_equip" name="<?php print($value[0]) ?>" id="<?php print($value[0]) ?>" value =<?php print($value[0]) ?>> 
                                        <label for = "<?php print($value[0]) ?>"></label>
                                        <h6 id = "<?php print($value[0]) ?>_2"><?php print($value[1]) ?></h6>
                                        <?php
                                        }
                                        ?>
                                </form>
                            </div>
                            <!-- Onglet 2 = Statistiques -->            
                            <div class="tab-pane" id="2" >
                                <h3>CONNAÎTRE LE TISSU ASSOCIATIF ET LES ÉQUIPEMENTS D'UNE COMMUNE</h3>                            
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
                                        <option selected="equipement">Équipement</option>
                                    </select><br>

                                    <!-- bouton qui lance la production du graphique : appel de la fonction dans le script js -->
                                    <button name="stat" class="btn" id="btn_stat" type="button">Calculer</button>
                                    
                                    <button name="PDF" type="button" class="btn" id="telecharge" onclick="generatePDF()"><i class="fa fa-download"></i></button>
                                </form>
                                
                                <!-- les valeurs sont récupérées dans une balise cachée  -->
                                <p hidden class ="nom_cate"></p>                        
                                <!-- définition de la balise ou sera créé le graph -->
                                <div id="suppression"> </div>
                                <canvas id="myChart" width="300" height="300"></canvas>
                            </div>

                            <!----------------onglet-03-Potentialite-------------------------->
                            <div class="tab-pane" id="3" >
                                <h3>Simuler l'implantation d'un nouvel équipement</h3>
                                <h2 class="explication">Cet outil vous permet de simuler l'implantation d'un nouvel équipement de votre choix dans une commune du Grand-Lyon.<br><br> La localisation est définie 
                                    par analyse de plusieurs critères : occupation du sol, distance aux équipements existants et desserte de population</h2>                            
                                
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
                                <button class="btn" name="stat" id="btn_potentiel" type="button">Exécuter</button>
                                </form>
                                <div hidden id="loading">
                                    <h2>Le calcul est en cours... Python travaille, prends un Picon</h2>
                                    <img id="img_chargement" src="img/load_animation.gif" alt="Loading" />
                                </div>
                                <div hidden id="resultat_calcul">
                                <h2>Calcul fini !</h2>
                                <br>
                                <h2>Une zone de 15m² vous est proposée</h2>
                                </div>
                            </div>

                            <!----------------onglet-04-Utilisateur-------------------------->
                            <div class="tab-pane" id="4" >
                                <h3>Identifier des particuliers intéressés par votre domaine</h3>
                                <h2 class = "explication">Compter le nombre d'intéressés autour d'un point sur la carte.<br>
                                <br>
                                Sélectionner un point sur la carte et une distance allant de 250 m à 2,5 km
                                </h2 >
                                <input type="range" name="rangeInput" id="rangeInput" min="250" max="2500" step="250" onchange="updateTextInput(this.value);searchBenevole(this.value);">
                                <h2>Distance en mètres : </p><p id="rangeText" value=""></h2>
                                <!-- <p>Le nombre de bénévoles potentiels est de :</p> -->
                                <h2 id = "result_popbene"  class = "popbene"></h2>                                   
                            </div>
                        </div>
                        </div><!-- /tab-content -->
                    </div><!-- /tabbable -->
                </div><!-- /col -->
            <!-- </div>/row -->
            </li><!-- /container -->

    <li class="flex-item" id="flex-map">
      <div id = "map"></div>
    </li>
     

    <!-- Appel jquery et bootstrap nécessaire pour le fonctionnement du menu vertical -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
    <!-- appel du script qui permet d'executer le script php contenant le script python -->
    <script src='script_python.js'></script>
    <script src ="js/icones.js"></script>
    <script src ="js/script_association.js"></script>
    <!-- appel du script qui permet de faire un screen shot de la carte -->
    <script>L.simpleMapScreenshoter().addTo(map)</script>
    </body>
</html>
