<?php session_start();
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
  <link rel = "stylesheet" href="leaflet/leaflet.css" />

  <!-- appel de chart.js -->
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
            <a class="nav-link" href="#">Accueil
            <span class="sr-only">(current)</span></a>
            </li>
            <li>
            <a class="nav-link" href="Connexion.php">Connexion</a>
            </li>
            <li>
            <a class="nav-link" href="html/contact.html">Contact</a>
            </li>

            </li>
        </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
    </nav>
    
    <ul class="flex-container">
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
