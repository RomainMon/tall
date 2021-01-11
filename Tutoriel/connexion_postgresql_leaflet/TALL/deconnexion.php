<?php session_start();
// // nettoie la session avant la déconnection
     session_unset();
    // // détruit une session, la déconnecte
     session_destroy();
?>
<html>
    <head>
       <meta charset="utf-8">
        <!-- importer le fichier de style -->
        <link rel="stylesheet" href="css/connect.css" media="screen" type="text/css" />
    </head>
    <body>
        <div id="container">            
            <h1>Vous êtes déconnecté</h1>
            <p> Merci pour la visite</p>
            <a href ='index.php' id="bouton" > ok</a>
        </div>
    </body>
</html>