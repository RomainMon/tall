<?php session_start();
?>

<html>
    <head>
       <meta charset="utf-8">
        <!-- importer le fichier de style -->
        <link rel="stylesheet" href="css/connect.css" media="screen" type="text/css" />
    </head>
    <body>        
        <div id="container">
            <?php
            // connexion à la db
            include 'include/database.php';
            //  recupération de la varibable db pour faire des requêtes
            global $db;
            ?>
            <form method="POST">
                 <!-- zone de connexion -->
                <h1>Connexion</h1>
                <div id ="select_user">
                    <label for="type_connexion"><b>Vous êtes</b></label><br>
                        <select name="type_connexion" id="type_connexion">                        
                            <option value="association">Une Association</option>
                            <option value="utilisateur">Un Utilisateur</option>                        
                        </select>
                </div>
                <label for="email"><b>Votre mail</b></label>               
                <!-- le type email contraint l'utilisateur d'insérer un text avec un @ dedans -->
                <input type="email" name="lemail" id="lemail" placeholder="Votre email" required>
                <label><b>Mot de passe</b></label>
                <!-- ici on a un type password -->
                <input type="password" name="lpassword" id="lpassword" placeholder="Votre mot de passe" required>
               
                <!-- le type submit permet de soumettre le formulaire, génère un bouton envoyer -->
                <input type="submit" name="formlogin" id="formlogin" value="Connexion">

                <?php
                // vérification que l'élément formlogin a été envoyé
                if(isset($_POST['formlogin']))
                {
                    extract($_POST);
                    print($type_connexion);
                    //Si connexion utilisateur ou association :
                    if ($type_connexion=="utilisateur"){                    
                        if(!empty($lemail) && !empty($lpassword)){
                        // vérification que le user existe bien
                        $q = $db->prepare("SELECT * FROM utilisateur WHERE email = :email");
                        $q->execute(['email' => $lemail]);
                        // stockage du résultat de la requête pour l'afficher. fetch crée un tableau
                        $result= $q->fetch();
                        if ($result == true)
                        {
                            // le compte existe
                            // vérification que le mdp entrée correspond au mdp crypté
                            $hashpassword = $result['mdp'];
                            if (password_verify($lpassword, $hashpassword)){
                                //echo "Le mot de passe est bon, connexion en cours";
                                // récupération d'éléments de session
                                $_SESSION['nom'] = $result['nom'];
                                $_SESSION['prenom'] = $result['prenom'];
                                $_SESSION['email'] = $result['email'];
                                $_SESSION['date_inscription'] = $result['date_inscription'];
                                header('Location: utilisateur.php');
                            }else{
                                echo "Le mot de passe n'est pas correct";
                            }
                        }else{
                            echo "le compte portant l'email ". $lemail." n'hexiste pas";
                        }
                    }else{
                        if(!empty($lemail) && !empty($lpassword)){
                            // vérification que le user existe bien
                            $q = $db->prepare("SELECT * FROM asso_connexion natural join association WHERE courriel = :email");
                            $q->execute(['email' => $lemail]);
                            // stockage du résultat de la requête pour l'afficher. fetch crée un tableau
                            $result= $q->fetch();
                            if ($result == true)
                            {
                                // le compte existe
                                // vérification que le mdp entrée correspond au mdp crypté
                                $hashpassword = $result['mdp'];
                                if (password_verify($lpassword, $hashpassword)){
                                    //echo "Le mot de passe est bon, connexion en cours";
                                    // récupération d'éléments de session
                                    $_SESSION['nom_asso'] = $result['titre'];                                   
                                    $_SESSION['email'] = $result['courriel'];
                                    $_SESSION['date_inscription'] = $result['date_compte'];
                                    header('Location: utilisateur.php');
                                }else{
                                    echo "Le mot de passe n'est pas correct";
                                }
                            }else{
                                echo "le compte portant l'email ". $lemail." n'hexiste pas";
                            }
                    }
                }    
                }else{
                    echo "Veuillez completer l'ensemble des champs";
                }
                }

                ?>
                <?php
                    if(isset($_SESSION['email'])){ ?>
                    <p>Votre email : <?= $_SESSION['email']; ?></p>
                    <p>Votre date d'inscription : <?= $_SESSION['date_inscription']; ?></p>
                    <?php   } else {
                        echo "Veuillez vous connecter à votre compte";
                } ?>

                <h2>Nouvel(lle) utilisateur(trice)</h2>        
                <div class="clic">
                    <a href ='nouvel_utilisateur.php' id="bouton" > Créer un compte </a>
                </div>
            </form>
        </div>
    </body>
</html>