<?php session_start(); //ouverture d'une session 


    // // nettoie la session avant la déconnection
    //session_unset();
    // // détruit une session, la déconnecte
    session_destroy();
    // connexion à la db
    include 'include/database.php';
    //  recupération de la varibable db pour faire des requêtes
    global $db;
?>

<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>TALL</title>
  <script src="leaflet/leaflet.js"></script>
  <link rel="shortcut icon" type="image/ico" href="img/favicon.ico"/>     
  <link rel = "stylesheet" href="leaflet/leaflet.css" />

  <!-- appel de chart.js -->
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="//bootswatch.com/cosmo/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style_index.css">

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
                  <a class="nav-link" href="#">Accueil
                  <span class="sr-only">(current)</span></a>
                  </li>
                  <li>
                  <a class="nav-link" href="Connexion.php">Connexion</a>
                  </li>
                  <li>
                  <a class="nav-link" href="html/contact_index.html">Contact</a>
                  </li>

                  </li>
              </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    
    <ul class="flex-container">
    <li class="flex-item" id="flex-menu">
    <div class="row">
      <div class="col-md-4 col-sm-5">
        <div class="tabs-left">
        <div id="ensemble_onglets">
          <ul class="nav nav-tabs">
              <!-- Onglet 1 = légendes, paragraphe utilisateur -->
            <li class="active"><a href="#1" data-toggle="tab"><img src = "img/legende.svg" alt = "logo"></a></li>
            <!-- Onglet 2 = buffer distance proche de l'uilisateur --> 
            <li><a href="#2" data-toggle="tab"><img src = "img/autour.svg" alt = "logo"></a></li>
            <!-- Onglet 3 = itinéraire -->  
            <li><a href="#3" data-toggle="tab"><img src = "img/itineraire.svg" alt = "logo"></a></li>
            <!-- Onglet 4 ! statistiques -->
            <li><a href="#4" data-toggle="tab"><img src = "img/stat.svg" alt = "logo"></span></a></li>
          </ul>
          <div class="tab-content">

      <!-- Onglet 1 = légendes, paragraphe utilisateur -->
      <div class="tab-pane active" id="1">
          <form id="legende_asso">
              <h3>Les associations</h3>
              <?php
              $q = $db->prepare("SELECT * FROM CATEGORIE ORDER by id_cate;");
              $q->execute();
              //récupération du résultat de la requête dans une variable :
              $liste_cate= $q->fetchAll();

              foreach($liste_cate as $value){ 
                  ?>
                  <input checked="checked" type="checkbox" class="liste_cate" name="<?php print($value[0]) ?>" id="<?php print($value[0]) ?>" value =<?php print($value[0]) ?>> 
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
                  <input checked="checked" type="checkbox" class="liste_equip" name="<?php print($value[0]) ?>" id="<?php print($value[0]) ?>" value =<?php print($value[0]) ?>> 
                  <label for = "<?php print($value[0]) ?>"></label>
                  <h6 id = "<?php print($value[0]) ?>_2"><?php print($value[1]) ?></h6>
                  <?php
                  }
                  ?>
      </div>

          <!-- Onglet 2 = buffer distance proche de l'uilisateur -->            
            <div class="tab-pane" id="2">
               <p>Connectez vous pour en savoir plus sur les associations et les équipements proches de chez vous ! </p>
            </div>

            <!-- Onglet 3 = itinéraire -->  
            <div class="tab-pane" id="3">
            <p>Connectez vous pour connaître l'itinéraire vous menant à une association ou un équipement choisi ! </p>                
            </div>

            <!-- Onglet 4 ! statistiques -->  
            <div class="tab-pane" id="4">
            <p>Connectez vous pour en savoir plus sur les associations et les équipements présents dans votre quartier ! </p>
            </div>
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
  <script src ="js/script.js"></script>
</html>
