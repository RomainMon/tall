<?php session_start();
// // nettoie la session avant la déconnection
     session_unset();
    // // détruit une session, la déconnecte
     session_destroy();
?>
<html>
    <head>
       <meta charset="utf-8">
       <title>TALL</title>
        <link rel="shortcut icon" type="image/ico" href="img/favicon.ico"/>
        <!-- importer le fichier de style -->
        <link rel="stylesheet" href="css/connect.css" media="screen" type="text/css" />
    </head>
    <body>
        <div id="container">            
            <h1>À Bientôt sur TALL</h1>
            <h2> Merci pour la visite</h2>
            <a href ='index.php' id="bouton" > ok</a>
        </div>
    </body>
</html>