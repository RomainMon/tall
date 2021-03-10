<?php session_start();
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
                    <label for="type_connexion"><h3>Vous êtes</h3></label><br>
                        <select name="type_connexion" id="choix"> 
                            <option value="utilisateur">Un particulier</option>
                            <option value="association">Une association</option>                       
                        </select>
                        </div>
                <label for="email"><h3>Votre e-mail</h3></label>               
                <!-- le type email contraint l'utilisateur d'insérer un text avec un @ dedans -->
                <input type="email" name="lemail" id="choix" placeholder="Votre e-mail" required>
                <label><h3>Mot de passe</h3></label>
                <!-- ici on a un type password -->
                <input type="password" name="lpassword" id="choix" placeholder="Votre mot de passe" required>
               
                <!-- le type submit permet de soumettre le formulaire, génère un bouton envoyer -->
                <input type="submit" name="formlogin" id="formlogin" value="Connexion">

                <?php
                // vérification que l'élément formlogin a été envoyé
                if(isset($_POST['formlogin']))
                {
                    extract($_POST);                    
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
                                // recuperation des preferences utilisateur dans un array pour filtre des valeurs nulles
                                $liste_preference =[
                                    $result['id_cate_1'],
                                    $result['id_cate_2'],
                                    $result['id_cate_3'],
                                    $result['id_cate_4'],
                                    $result['id_cate_5']
                                    ];                                
                                // récupération d'éléments de session
                                $_SESSION['id_utilisateur']=$result['id_utilisateur'];
                                $_SESSION['nom'] = $result['nom'];
                                $_SESSION['prenom'] = $result['prenom'];
                                $_SESSION['email'] = $result['email'];
                                $_SESSION['telephone'] = $result['telephone'];
                                $_SESSION['date_inscription'] = $result['date_inscription'];
                                $_SESSION['id_adresse'] = $result['id_adresse'];
                                $_SESSION['mdp'] = $result['mdp'];

                                

                                // recuperation des preferences de l'utilisateur
                                $_SESSION['preference']=[];
                                foreach($liste_preference as $value){
                                    if($value !='null'){
                                        array_push($_SESSION['preference'],$value);
                                    }
                                };
                                header('Location: utilisateur.php');
                            }else{
                                // echo "Le mot de passe n'est pas correct";
                                ?>
                                <h4>Le mot de passe n'est pas correct</h4>
                                <?php
                                
                            }
                        }else{
                            // echo "le compte portant l'email ". $lemail." n'existe pas ! ";
                            ?>
                            <h4>Email inconnu</h4>
                            <?php
                        }
                    }
                }
                    if ($type_connexion=="association"){
                        if(!empty($lemail) && !empty($lpassword)){
                            // vérification que le user existe bien
                            $q = $db->prepare("SELECT * FROM asso_connexion NATURAL JOIN association WHERE email = :email");
                            $q->execute(['email' => $lemail]);
                            // stockage du résultat de la requête pour l'afficher. fetch crée un tableau
                            $result= $q->fetch();
                            if ($result == true)
                            {
                                // le compte existe
                                // vérification que le mdp entrée correspond au mdp crypté
                                $hashpassword = $result['mdp'];
                                // console.log($hashpassword);
                                if (password_verify($lpassword, $hashpassword)){
                                    
                                    // echo "Le mot de passe est bon, connexion en cours";
                                    ?>
                                    <h4>Le mot de passe est bon, connexion en cours</h4>
                                    <?php
                                    // sleep(1);
                                    // récupération d'éléments de session
                                    $_SESSION['nom_asso'] = $result['titre'];                                   
                                    $_SESSION['email'] = $result['email'];
                                    $_SESSION['date_creation_asso'] = $result['date_creat'];
                                    $_SESSION['description'] = $result['objet'];
                                    $_SESSION['site_web'] = $result['siteweb'];
                                    $_SESSION['id_cate'] = $result['id_cate'];
                                    $_SESSION['courriel'] = $result['courriel'];
                                    $_SESSION['telephone'] = $result['telephone'];
                                    $_SESSION['mdp'] = $result['mdp'];
                                    $_SESSION['id_asso'] = $result['id_asso'];

                                    header('Location: association.php');
                                }else{
                                    // echo "Le mot de passe n'est pas correct";
                                    ?>
                                    <h4>Le mot de passe n'est pas correct</h4>
                                    <?php
                                    }
                                }else{
                                    // echo "le compte portant l'email ". $lemail." n'existe pas";
                                    ?>
                                    <h4>Email inconnu</h4>
                                    <?php
                            }
                    }
                }    
                }else{
                    // echo "Veuillez complèter l'ensemble des champs";
                    ?>
                    <h4>Veuillez compléter l'ensemble des champs</h4>
                    <?php
                }
                

                ?>
                <?php
                    if(isset($_SESSION['email'])){ ?>
                    <p>Votre e-mail : <?= $_SESSION['email']; ?></p>
                    <p>Votre date d'inscription : <?= $_SESSION['date_inscription']; ?></p>
                    <?php   } else {
                        // echo "Veuillez entrer l'adresse mail correspondante";
                } ?>

                <h2>Créer un compte</h2>        
                <div class="clic">
                    <a href ='nouvel_utilisateur.php' class="bouton" >Créer un compte utilisateur</a>
                    <br>
                    <br><a href ='nouvelle_association.php'class="bouton" >Créer un compte assocation</a>
                </div>
            </form>
        </div>
    </body>
</html>