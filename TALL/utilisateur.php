<?php session_start();
// connexion à la db
include 'include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;
?>

<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>CodePen - Bootstrap Vertical Tabs</title>
  <script src="leaflet/leaflet.js"></script>      
  <link rel = "stylesheet" href="leaflet/leaflet.css" />
    <!-- Icônes -->
    <script src="https://kit.fontawesome.com/3b2bc082a4.js" crossorigin="anonymous"></script>
    <!-- Police -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet"> 
    <!-- appel de l'api google jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <!-- appel du script js jquery -->
  <script src='js/jquery_itineraire.js'></script>
  <script src='js/jquery_stat.js'></script>
  <!-- appel de chart.js -->
  <script src="js/package/dist/Chart.js"></script>
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="//bootswatch.com/cosmo/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="css/style_utilisateur.css">

</head>
<body>
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
        <a class="nav-link" href="utilisateur.php">Accueil
        <span class="sr-only">(current)</span></a>
        </li>
        <li>
        <a class="nav-link" href="deconnexion.php">Déconnexion</a>
        </li>
        <li>
        <a class="nav-link" href="info.html">Contact</a>
        </li>
        <li>
        <a class="nav-link" href="profil_user.php">Profil</a>
        </li>

        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<!-- partial:index.partial.html -->
<ul class="flex-container">
  <li class="flex-item" id="flex-menu">
    <div class="row">
      <div class="col-md-4 col-sm-5">
        <div class="tabs-left">
          <ul class="nav nav-tabs">
              <!-- Onglet 1 = légendes, paragraphe utilisateur -->
            <li class="active"><a href="#1" data-toggle="tab"><span class="glyphicon glyphicon-eye-open"></span></a></li>
            <!-- Onglet 2 = buffer distance proche de l'uilisateur --> 
            <li><a href="#2" data-toggle="tab"><span class="glyphicon glyphicon-tower"></span></a></li>
            <!-- Onglet 3 = itinéraire -->  
            <li><a href="#3" data-toggle="tab"><span class="glyphicon glyphicon-road"></span></a></li>
            <!-- Onglet 4 ! statistiques -->
            <li><a href="#4" data-toggle="tab"><span class="glyphicon glyphicon-stats"></span></a></li>
          </ul>
          <div class="tab-content">

          <!-- Onglet 1 = légendes, paragraphe utilisateur -->
            <div class="tab-pane active" id="1">
                <!-- récupération dans une balise cachée des éléments de session pour les catégories d'association et l'id utilisateur  -->
                <?php 
                foreach($_SESSION['preference'] as $value){
                    ?>
                    <p hidden class = "categorie"><?php print($value); ?></p>
                    <?php
                }
                ?>
                <p hidden id="id_utilisateur"><?= $_SESSION['id_utilisateur']; ?></p>
                <p>Bonjour, <?= $_SESSION['prenom']; ?> <?= $_SESSION['nom']; ?></p>
                La carte affiche les associations ainsi que les équipements selon les préférences que vous avez remplies lors de votre inscription.
                Si vous souhaitez afficher les autres éléments vous pouvez cocher les case ci-dessous :<br>
                <form id="legende">
                    Les associations<br>
                    <?php
                    $q = $db->prepare("SELECT * FROM CATEGORIE ORDER by id_cate;");
                    $q->execute();
                    //récupération du résultat de la requête dans une variable :
                    $liste_cate= $q->fetchAll();

                    foreach($liste_cate as $value){
                        
                            if (in_array($value[0], $_SESSION['preference'])){
                                ?>
                                <input checked="checked" type="checkbox" class="cm-toggle" name="cate_1" id="<?php print($value[0]) ?>" value =<?php print($value[0]) ?>> 
                                <?php print($value[1]) ?><br>
                            <?php
                            }
                            else {
                                ?>
                                <input type="checkbox" class="cm-toggle" name="cate_1" id="<?php print($value[0]) ?>" value =<?php print($value[0]) ?>> 
                                <?php print($value[1]) ?><br>
                            <?php
                            }
                            
                        }
                        ?>
            </div>

          <!-- Onglet 2 = buffer distance proche de l'uilisateur -->            
            <div class="tab-pane" id="2">
                <p>Sélectionnez le temps de trajet jusqu'aux associations ou équipements </p>
                0 <input type="range" name="rangeInput" id="rangeInput" min="0" max="5000" step="500" onchange="updateTextInput(this.value);bufferUtil(this.value);zoomAutour()"> 5 000
                <p>Distance en mètres : </p><p id="rangeText" value=""></p>
                <button name="autour" id="autour" type="button" onclick='zoomAutour()'>Envoyer le bouzin</button>
            </div>

            <!-- Onglet 3 = itinéraire -->  
            <div class="tab-pane" id="3">
             <!-- récupération dans une balise cachée des éléments de session pour les catégories d'association et l'id utilisateur  -->
                <?php 
                foreach($_SESSION['preference'] as $value){
                ?>
                <p hidden class = "categorie"><?php print($value); ?></p>
                <?php
                }
                ?>
                <p hidden id="id_utilisateur"><?= $_SESSION['id_utilisateur']; ?></p>
                <h3>Bonjour, <?= $_SESSION['prenom']; ?> <?= $_SESSION['nom']; ?></h3>  
                <?php
                    $q = $db->prepare("
                    SELECT ad.numero, ad.rep, ad.nom_1, ad.code_post, ad.nom_com FROM vue_adresse AS ad, utilisateur AS u
                    WHERE u.id_utilisateur = :id_user AND u.id_adresse = ad.id_adresse;
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
                <span>Calculs d'itinéraire le plus court</span>
                <p>Cliquer sur un point d'association ou d'équipement sur la carte puis sur le bouton ci-dessous</p>
                <button id="itineraire_button" onClick="itineraireDisplay()">Lancer le calcul</button>                                            
                <p id="test_iti" class = "poulpy"></p>
            </div>

            <!-- Onglet 4 ! statistiques -->  
            <div class="tab-pane" id="4">
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
                    <button name="stat" id="stat" onClick="makeChart()" type="button">Envoyer le bouzin</button>
                    <input Type="button" value="Nouvelle recherche" onClick="history.go(0)">
                </form>
                    <!-- les valeurs sont récupérées dans une balise cachée  -->
                    <p hidden class ="nom_cate"></p>                        
                    <!-- définition de la balise ou sera créé le graph -->
                    <canvas id="myChart" width="300" height="300"></canvas>
            </div>




          </div><!-- /tab-content -->
        </div><!-- /tabbable -->
      </div><!-- /col -->
    </div><!-- /row -->
  </li><!-- /container -->

  <li class="flex-item" id="flex-map">
    <div id = "map"></div>
  </li>

</ul>  
<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
  <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
  <script src ="js/icones.js"></script>
  <script src ="js/script_utilisateur.js"></script>

</html>
