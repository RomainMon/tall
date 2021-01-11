<?php session_start();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login page</title>
    </head>
    <body>
    <h1>Bienvenue sur votre profil</h1>
    <?php
        if(isset($_SESSION['email'])){ ?>
    <p>Votre email : <?= $_SESSION['email']; ?></p>
    <p>Votre date d'inscription : <?= $_SESSION['date_inscription']; ?></p>
    <?php   } else {
        echo "Veuillez vous connecter à votre compte";
    } ?>
    </body>
</html>