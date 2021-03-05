<?php session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Profil Asso</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- appel de l'api google jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- appel du script js jquery -->
    <script src='js/jquery_site.js'></script>
    <!-- <link rel='stylesheet' type='text/css' media='screen' href='main.css'>
    <script src='main.js'></script> -->
     <!-- Icônes -->
		<script src="https://kit.fontawesome.com/3b2bc082a4.js" crossorigin="anonymous"></script>
<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    <!-- JQuery -->
<!--     <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.3/css/bootstrap.css'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'><link rel="stylesheet" href="css/style_asso.css">

</head>
<body>    
<!-- Menu  -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light"> <!-- On utilise le template light = couleur -->     
        <div class="container-fluid"> <!-- le conteneur "fluide" utilise toute la page -->
            <a class="navbar-brand" href="#"><img src = "img/logo.svg" alt = "logo"></a> <!-- On ajoute le logo et on gère sa taille dans le css -->
            <!-- Devient responsive : Permet d'avoir un bouton lorsqu'on replie la page (smartphone, tablette) --> 
            <button class="navbar-toggler" type="button" data-toggle="collapse" 
            data-target="#navbarResponsive" aria-controls="navbarResponsive"
            aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="association.php">Accueil
                            <span class="sr-only">(current)</span>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="deconnexion.php">Déconnexion</a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="info.html">Contact</a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="#">Profil</a>
                    </li>
                </ul>
            </div>    
        </div>
    </nav>

   
<!-- Menu profil -->

<div id="tabs-container">   
            
            <ul class="tabs-menu">
                <li class="current"><a href="#tab-1"><i class="fas fa-user mr-3"></i>Mon profil</a></li>
                <li><a href="#tab-2"><i class="fas fa-address-card mr-3"></i>Mes informations</a></li>
                <li><a href="#tab-3"><i class="fas fa-map-pin mr-3"></i>Mon potentiel</a></li>
                
                <li><a href="#tab-4"><i class="fas fa-trash mr-3"></i>Quitter TALL</a></li>

            </ul> 
        </div>    
</div>


        <?php
        // connexion à la db
        include 'include/database.php';
        //  recupération de la varibable db pour faire des requêtes
        global $db;
        ?> 
            <!-- récupération des éléments de session utilisateur pour les afficher  -->
        <!-- TAB 1  -->
    <div class="tab">
        <div id="tab-1" class="tab-content">
            <h1>Mon profil <br></h1>
            <p>Nom : <?= $_SESSION['nom_asso']; ?></p></br>
            <p>Date création de l'association : <?= $_SESSION['date_creation_asso'] ?></p></br>
            <p>Email d'authentification : <?= $_SESSION['email']; ?></p></br>
            <p>Téléphone de l'association : <?php if($_SESSION['telephone'] != NULL){echo $_SESSION['telephone'];}else{echo "numéro non renseigné";} ?></p></br>
            <p>Description de l'association : <?= $_SESSION['description']; ?></p></br>
            <p>URL de votre site web : <?php if($_SESSION['site_web'] != '#N/A'){echo $_SESSION['site_web'];}else{echo "non renseigné";} ?></p></br>
            <p>Courriel visible par les particuliers : <?php if($_SESSION['courriel'] != NULL){echo $_SESSION['courriel'];}else{echo "courriel non renseigné";} ?></p>

            <?php
        // requête pour récupérer l'adresse utilisateur et l'afficher
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
            // var_dump($adresse_user);
            $num_rue = $value['adrs_numvo'];
            $complement_addresse = $value['adrs_typev'];
            $nom_rue = $value['adrs_libvo'];
            $nom_com = $value['nom_com'];
            $code_postale = $value['code_post']
        ?>
        <p>Mon adresse : <?= $value['adrs_numvo']; ?> <?= $value['adrs_typev']; ?> <?= $value['adrs_libvo']; ?> <?= $value['code_post']; ?> <?= $value['nom_com']; ?></p>
        <?php
        }
        ?>

        <?php
        // requête pour récupérer le domaine de l'asso
        $q = $db->prepare("
        select nom_cate from categorie
        where id_cate = :id_cate ;
        ");
        $q->execute([
            'id_cate'=> $_SESSION['id_cate']
        ]);                   
        //récupération du résultat de la requête dans une variable :
        while($cate_asso= $q->fetch()){
            // var_dump($cate_asso);
        ?>
        <p>Domaine de l'association : <?= $cate_asso['nom_cate']; ?></p>
        <?php
        }
        ?>
        </div>
    
    
    <!-- TAB 2 -->

        <div id="tab-2" class="tab-content">   
        <h1>Mes informations</h1>
        <h2> N'hésitez pas à modifier vos coordonnées ci-dessous pour que votre compte TALL soit parfaitement à jour.</h2>
            
        <form method='post'>
            <!-- les variables d'adresse restent en commentaire pour le moment -->

            <!-- <input type="number" name="num_rue" id="num_rue" value=<?= $num_rue; ?> required>

            <input type="text" name="complement_adresse" id="complement_adresse" value=<?= $complement_addresse; ?> required>

            <input type="text" name="nom_rue" id="nom_rue" value=<?= $nom_rue; ?> required>

            <input type="text" name="nom_com" id="nom_com" value=<?= $nom_com; ?> required> -->

            <!-- <input type="text" name="code_post" id="code_post" value=<?= $code_postale; ?> required> -->


            <!-- le type text pour le nom d'utilisateur -->
            <input type="text" name="nom" id="nom" value=<?= $_SESSION['nom_asso']; ?> required>
            <!-- le type email contraint l'utilisateur d'insérer un text avec un @ dedans -->
            <input type="email" name="email" id="email" value=<?= $_SESSION['email']; ?> required>
            <!-- descriptif de l'asso -->
            <label for="descriptif">Description (300 caractères max):</label>
            <input type="text" name="descriptif" id="descriptif1" size="50" maxlength="300" spellcheck="true" value='<?= $_SESSION['description']; ?>' required>
            <!-- descriptif de l'asso -->
            <input type="text" name="site_web" id="site_web" value=<?= $_SESSION['site_web']; ?>>
            <!-- courriel visible pas les particuliers-->
            <input type="email" name="courriel_for_user" id="courriel_for_user" value=<?= $_SESSION['courriel']; ?>>
            <!-- numéro de téléphone -->
            <input type="tel" name="telephone" id="telephone" value=<?= $_SESSION['telephone']; ?>><br>

            <!-- le type submit permet de soumettre le formulaire, génère un bouton envoyer -->
            <input type="submit" name="formsend" id="formsend" value="Sauvegarder"><br>

        </form>

        <?php
            // création d'une condition qui vérifie que le formulaire est rempli avant l'envoi. isset() permet de vérifier si un élément a été placé.
                if(isset($_POST['formsend'])){
                    // permet d'éviter la répétition de $_POST dans le code
                    extract($_POST);

                    if (!empty($email)){
                        if ($_SESSION['email']!=$email){
                            // je vérifie que l'email entré par l'utilisateur n'existe pas déjà
                            $c = $db->prepare("SELECT email FROM asso_connexion where email = :email ");
                            $c->execute(['email' => $email]);
                            // permet de compter le résultat de la requête précédente
                            $result = $c->rowCount();

                            // la condition suivante s'execute si l'email n'existe pas
                            if($result == 0){
                                 // je fais une requête préparé pour des questions de sécurité
                                 $q = $db->prepare("UPDATE association
                                 set titre=:nom, telephone=:telephone, objet=:descriptif, siteweb=:site_web, courriel=:courriel_for_user
                                 where id_asso=:id_asso");
                                $q -> execute ([
                                    'id_asso'=> $_SESSION['id_asso'],
                                    'nom' => $nom,
                                    'telephone'=> $telephone,
                                    'descriptif' => $descriptif,
                                    'site_web' => $site_web,
                                    'courriel_for_user' => $courriel_for_user

                                ]);

                                // je fais une requête préparé pour des questions de sécurité
                                $q = $db->prepare("UPDATE asso_connexion
                                set email=:email
                                where id_asso=:id_asso");
                                $q -> execute ([
                                'id_asso'=> $_SESSION['id_asso'],
                                'email' => $email

                                ]);

                            }else{
                                echo "Cet email existe déjà";
                            }

                        }else{
                                // je fais une requête préparé pour des questions de sécurité
                                $q = $db->prepare("UPDATE association
                                    set titre=:nom, telephone=:telephone, objet=:descriptif, siteweb=:site_web, courriel=:courriel_for_user
                                    where id_asso=:id_asso");
                                $q -> execute ([
                                    'id_asso'=> $_SESSION['id_asso'],
                                    'nom' => $nom,
                                    'telephone'=> $telephone,
                                    'descriptif' => $descriptif,
                                    'site_web' => $site_web,
                                    'courriel_for_user' => $courriel_for_user

                                ]);


                                // je fais une requête préparé pour des questions de sécurité
                                $q = $db->prepare("UPDATE asso_connexion
                                set email=:email
                                where id_asso=:id_asso");
                                $q -> execute ([
                                'id_asso'=> $_SESSION['id_asso'],
                                'email' => $email

                                ]);
                        }
                    }else{
                        echo "les champs ne sont pas tous remplis";
                    }
                    
                }
            ?>
        </div>    

    <!-- TAB 4 -->

        <div id="tab-4" class="tab-content">   
        <div id='formulaire_suppression'>
            <form method='post'>
                <!-- Choix d'une association -->
                <h1>Quitter TALL</h1>
                <h2> Si vous souhaiter supprimer votre compte, merci d'indiquer votre mot de passe, la suppression sera automatique et votre compte définitivement supprimé de notre base de données.</h2>
                <h2> Attention cette action est définitive, si vous changez d'avis, il faudra recréer un compte.</h2>
                
                 <!-- ici on a un type password -->
                 <input type="password" name="lpassword" id="lpassword" placeholder="Votre mot de passe" required>

                 <!-- envoi du formulaire en html -->
                <input type="submit" name="formsend_3" id="formsend2" value="Supprimer mon compte"><br>
            </form>

            <?php
            // création d'une condition qui vérifie que le formulaire est rempli avant l'envoi. isset() permet de vérifier si un élément a été placé.
                if(isset($_POST['formsend_3'])){
                    // permet d'éviter la répétition de $_POST dans le code
                    extract($_POST);                   
                    if(!empty($lpassword)){
                    $hashpassword = $_SESSION['mdp'];
                    if (password_verify($lpassword, $hashpassword)){
                            // je fais une requête préparé pour des questions de sécurité
                            $q = $db->prepare("DELETE FROM asso_connexion
                                where id_asso = :id_asso;");
                            $q -> execute ([
                                'id_asso'=> $_SESSION['id_asso']                 
                            ]);
                        }
                    }
                }
                      
            ?>
        </div>
    </div>    
<!-- partial -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script><script  src="js/page_user.js"></script>


</body>
</html>