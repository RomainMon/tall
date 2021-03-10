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
  <link rel="shortcut icon" type="image/ico" href="img/favicon.ico"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.1.0/animate.min.css'>
<link rel="stylesheet" href="css/popup.css">

</head>
<body>
<!-- partial:index.partial.html -->
<div id="overlay" class="cover blur-in"></div>
<div class="row pop-up">
  <div class="box small-6 large-centered">
    <a href="accueil.php" class="close-button">&#10006;</a>
    <h3 class="popupsub">Ton Association locale Lyonnaise</h3>
    <p>Bonjour, bienvenue sur TALL ! <br>
      <br>Ici, trouve un composteur, une AMAP, une boîte à partage...<br>
    Tu es un particulier : Renseigne-toi sur le tissu associatif lyonnais. <br>
    Tu es membre d'une association : Trouve la place idéale pour ton nouvel équipement.<br>
    <br> Et bien plus !
    <br>Connecte-toi pour découvrir tous les outils disponibles !
    </p>
    <a href="accueil.php" class="button">J'ai compris</a>
  </div>
</div>
<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
  <script  src="js/popup.js"></script>

</body>
</html>
