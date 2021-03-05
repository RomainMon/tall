<?php session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Profil User</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- appel de l'api google jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- appel du script js jquery -->
    <script src='js/jquery_site.js'></script>
    <!-- <link rel='stylesheet' type='text/css' media='screen' href='main.css'>
    <script src='main.js'></script> -->
    <!-- Icones -->
    <script src="https://kit.fontawesome.com/3b2bc082a4.js" crossorigin="anonymous"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    <!-- JQuery -->    
<!--     <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.3/css/bootstrap.css'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="css/style_user.css">

</head>
<body>    
<!-- Menu du haut navigation  -->
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
                        <a class="nav-link" href="utilisateur.php">Accueil
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

   
    <?php
    // connexion à la db
    include 'include/database.php';
    //  recupération de la varibable db pour faire des requêtes
    global $db;
    ?> 
<!-- Menu profil -->

<div id="tabs-container">   
            
            <ul class="tabs-menu">
                <li class="current"><a href="#tab-1"><i class="fas fa-user mr-3"></i>Mon profil</a></li>
                <li><a href="#tab-2"><i class="fas fa-address-card mr-3"></i>Mes informations</a></li>
                <li><a href="#tab-3"><i class="fas fa-map-pin mr-3"></i>Mes préférences</a></li>
                <li><a href="#tab-4"><i class="fas fa-trash mr-3"></i>Quitter TALL</a></li>

            </ul> 
        </div>    
</div>



<div class="tab">
<!-- TAB 1 Mon profil -->

    <div id="tab-1" class="tab-content current">
            <h1>Mon profil <br></h1>
            <p><br><?= $_SESSION['nom']; ?> <?= $_SESSION['prenom']; ?></p>
            <p><?= $_SESSION['email']; ?></p>
            <p><?= $_SESSION['telephone']; ?></p>
            <?php
            // requête pour récupérer l'adresse utilisateur et l'afficher
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
                // var_dump($adresse_user);
                $num_rue = $value['numero'];
                $complement_addresse = $value['rep'];
                $nom_rue = $value['nom_1'];
                $nom_com = $value['nom_com'];
            ?>
            <p><?= $value['numero']; ?> <?= $value['rep']; ?> <?= $value['nom_1']; ?> <?= $value['code_post']; ?> <?= $value['nom_com']; ?></p>
            <?php
            }
            ?>
            
            <?php
            // requête pour afficher l'association de l'utilisateur
            $q = $db->prepare("
                with asso_user as (select id_asso from utilisateur
                where id_utilisateur = :id_user)
                select ass.titre from association as ass, asso_user as assu
                where ass.id_asso = assu.id_asso;
            ");
            $q->execute([
                'id_user'=> $_SESSION['id_utilisateur']
            ]);                   
            //récupération du résultat de la requête dans une variable :
            $association_user= $q->fetchAll();
            foreach($association_user as $value_asso){
                // var_dump($value_asso);
            ?>
            <p>Je suis membre de l'association <?php if (isset($value_asso['titre'])){echo$value_asso['titre'];} ?></p>
            <?php
            }
            ?>


            <?php
            // création d'une fonction qui renvoi du texte car nous avons un tableau dans un tableau
            function retour_cate($nom_cate){
                return "{$nom_cate}";
            }
            //  je fais uen requete pour récupérer les centres d'intérets utilisateur afin de les afficher
            $q = $db->prepare("
            with pref_asso as (select id_cate_1, id_cate_2, id_cate_3, id_cate_4, id_cate_5 from utilisateur
            where id_utilisateur = :id_user)
            select nom_cate from categorie, pref_asso as pr
            where case When pr.id_cate_1 = id_cate then nom_cate is not null When pr.id_cate_3 = id_cate then nom_cate is not null
            when pr.id_cate_2 = id_cate then nom_cate is not null
            when pr.id_cate_3 = id_cate then nom_cate is not null
            when pr.id_cate_4 = id_cate then nom_cate is not null
            when pr.id_cate_5 = id_cate then nom_cate is not null
            end;");
            $q->execute([
                'id_user'=> $_SESSION['id_utilisateur']
            ]);                   
            //récupération du résultat de la requête dans une variable : en utilisant la fonction retour_cate
            $cate_user= $q->fetchAll(PDO::FETCH_FUNC, "retour_cate");
            ?>
                <h3>Préférences sélectionnées : </h3>
            <?php

            foreach($cate_user as $value_cate){
            ?>
            <p><?php if (isset($value_cate)){echo $value_cate;}else{echo "Vous n'avez pas choisi d'association";}?> </p>
            <?php
            }
            ?>
    </div>

<!-- TAB 2 Mes informations -->

    <div id="tab-2" class="tab-content">      
            <!-- création du formulaire d'inscription -->
            <!-- la method dans form définit la méthode d'envoie du formulaire. post : envoie les données d'une page à l'autre (méthode recommandé). get : envoie les infos par URL. -->
        <form method="post" id="container">
            <h1>Mes informations</h1>
            <h2> N'hésitez pas à modifier vos coordonnées ci-dessous pour que votre compte TALL soit parfaitement à jour.</h2>
                <div id="civilite"> 
                    <!-- le type text pour le nom d'utilisateur -->
                    <h3><br>Nom :</h3><input type="text" name="nom" id="choix" value=<?= $_SESSION['nom']; ?> required>
                    <!-- le type text pour le prénom d'utilisateur -->
                    <h3><br>Prénom :</h3><input type="text" name="prenom" id="choix" value=<?= $_SESSION['prenom']; ?> required>
                    <!-- le type email contraint l'utilisateur d'insérer un text avec un @ dedans -->
                    <h3><br>Adresse e-mail :</h3><input type="email" name="email" id="choix" value=<?= $_SESSION['email']; ?> required>
                    <!-- numéro de téléphone -->
                    <h3><br>Téléphone :</h3><input type="tel" name="telephone" id="choix" value=<?= $_SESSION['telephone']; ?>><br>
                <!-- Commune -->
                </div>
                <div id = "adresse">   
                    <h3><br>Commune :</h3>            
                    <select name ="choix_commune" id="choix">
                        <option selected="selected" value=<?php $nom_com; ?>><?= $nom_com; ?></option>
                        <?php
                        $q = $db->prepare("SELECT distinct(nom_com) FROM vue_adresse ORDER by nom_com;");
                        $q->execute();                    
                        //récupération du résultat de la requête dans une variable :
                        $liste_commune= $q->fetchAll();
            
                        // Iterating through the product array
                        foreach($liste_commune as $value){
                        ?>
                        <option value="<?php print($value[0]); ?>"><?php print($value[0]); ?></option>
                        <?php
                        }
                        ?>
                    <br>   
                    </select>
                    <h3><br>Rue :</h3>   
                    <!-- adresse -->
                    <!-- une partie de la liste déroulante s'execute avec une requete jquery ajax -->
                    <select name ="choix_adresse" id="choix">
                        <option selected="selected" value=<?php $nom_rue; ?>><?= $nom_rue; ?></option>                
                    <br>                    
                    </select>
                    <h3><br>Numéro :</h3> 
                    <!-- numero -->
                    <!-- une partie de la liste déroulante s'execute avec une requete jquery ajax -->
                    <select name ="choix_numero" id="choix">
                        <option selected="selected" value=<?php $num_rue; ?>><?= $num_rue; ?></option>                   
                    <br>
                    </select>
                    <h3><br>Complément d'adresse :</h3> 
                    <!-- une partie de la liste déroulante s'execute avec une requete jquery ajax -->
                    <select name ="choix_rep" id="choix">
                        <option selected="selected" value=<?php $complement_addresse; ?>><?= $complement_addresse; ?></option>                    
                    <br>
                    </select>
                </div>
                    <!-- le type submit permet de soumettre le formulaire, génère un bouton envoyer -->
                    <br><input type="submit" name="formsend" id="formsend" value="Sauvegarder"><br>
                
            </form>

            <?php
            // création d'une condition qui vérifie que le formulaire est rempli avant l'envoi. isset() permet de vérifier si un élément a été placé.
                if(isset($_POST['formsend'])){
                    // permet d'éviter la répétition de $_POST dans le code
                    extract($_POST);

                    if (!empty($email)){
                        if ($_SESSION['email']!=$email){
                            // je vérifie que l'email entré par l'utilisateur n'existe pas déjà
                            $c = $db->prepare("SELECT email FROM utilisateur where email = :email ");
                            $c->execute(['email' => $email]);
                            // permet de compter le résultat de la requête précédente
                            $result = $c->rowCount();

                            // la condition suivante s'execute si l'email n'existe pas
                            if($result == 0){
                                // ayant un problème de type à la récupération de la variable $choix_numero, je la converti en int pour qu'elle puisse être intégrer à la requete sql
                                settype($choix_numero, "integer");
                                // recuperation des id_adresse et geom de la table vue_adresse
                                // pour eviter les conflits si une adresse n'a pas de reperes dans la requete sql on teste si la valeur renvoyee par choix_rep est vide
                                if ($choix_rep == '' or $choix_rep == NULL){
                                    $a = $db->prepare("SELECT id_adresse, geom FROM vue_adresse where nom_1 = :rue and nom_com = :commune and numero = :numero and rep is null");
                                    $a->execute(['rue'=>$choix_adresse,'commune'=>$choix_commune,'numero'=>$choix_numero]);
                                    //récupération du résultat de la requête dans une variable :
                                    $adresse= $a->fetch();
                                }
                                else{
                                    $a = $db->prepare("SELECT id_adresse, geom FROM vue_adresse where nom_1 = :rue and nom_com = :commune and numero = :numero and rep=:rep");
                                    $a->execute(['rue'=>$choix_adresse,'commune'=>$choix_commune,'numero'=>$choix_numero,'rep'=>$choix_rep]);
                                    //récupération du résultat de la requête dans une variable :
                                    $adresse= $a->fetch();
                                }
                                

                                // je fais une requête préparé pour des questions de sécurité
                                $q = $db->prepare("UPDATE utilisateur
                                    set nom = :nom, prenom=:prenom, email=:email, telephone=:telephone, id_adresse=:id_adresse, geom=:geom
                                    where id_utilisateur=:id_user");
                                $q -> execute ([
                                    'id_user' => $_SESSION['id_utilisateur'],
                                    'nom' => $nom,
                                    'prenom' => $prenom,
                                    'email' => $email,
                                    'telephone'=> $telephone,
                                    'id_adresse'=> $adresse[0],
                                    'geom' => $adresse[1]                                    
                                ]);
                            }else{
                                echo "Cet email existe déjà";
                            }

                        }else{
                            // ayant un problème de type à la récupération de la variable $choix_numero, je la converti en int pour qu'elle puisse être intégrer à la requete sql
                            settype($choix_numero, "integer");
                                if ($choix_rep == '' or $choix_rep == NULL){
                                    $a = $db->prepare("SELECT id_adresse, geom FROM vue_adresse where nom_1 = :rue and nom_com = :commune and numero = :numero and rep is null");
                                    $a->execute(['rue'=>$choix_adresse,'commune'=>$choix_commune,'numero'=>$choix_numero]);
                                    //récupération du résultat de la requête dans une variable :
                                    $adresse= $a->fetch();
                                }
                                else{
                                    $a = $db->prepare("SELECT id_adresse, geom FROM vue_adresse where nom_1 = :rue and nom_com = :commune and numero = :numero and rep=:rep");
                                    $a->execute(['rue'=>$choix_adresse,'commune'=>$choix_commune,'numero'=>$choix_numero,'rep'=>$choix_rep]);
                                    //récupération du résultat de la requête dans une variable :
                                    $adresse= $a->fetch();
                                }

                                // je fais une requête préparé pour des questions de sécurité
                                $q = $db->prepare("UPDATE utilisateur
                                    set nom = :nom, prenom=:prenom, telephone=:telephone, id_adresse=:id_adresse, geom=:geom
                                    where id_utilisateur=:id_user");
                                $q -> execute ([
                                    'id_user' => $_SESSION['id_utilisateur'],
                                    'nom' => $nom,
                                    'prenom' => $prenom,
                                    'telephone'=> $telephone,
                                    'id_adresse'=> $adresse[0],
                                    'geom' => $adresse[1]                                    
                                ]);
                        }
                    }else{
                        echo "les champs ne sont pas tous remplis";
                    }
                    
                }
            ?>

        <div id='formulaire_adherent_asso'>
                    <form method='post'>
                        <!-- Choix d'une association -->
                        <h3><br>Êtes-vous membre d'une association ?</h3>
                            <select name ="choix_asso" id="choix">
                                <option selected="selected">Sélectionner une valeur</option>
                                <?php
                                $q = $db->prepare("SELECT titre,id_asso FROM association ORDER by titre;");
                                $q->execute();
                                //récupération du résultat de la requête dans une variable :
                                $liste_asso= $q->fetchAll();
                    
                                // Iterating through the product array
                                foreach($liste_asso as $value){
                                ?>
                                <option value="<?php print($value[1]); ?>"><?php print($value[0]); ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <!-- le type submit permet de soumettre le formulaire, génère un bouton envoyer -->
                        <input type="submit" name="formsend_2" id="formsend" value="Sauvegarder"><br>
                        
                        
                    </form>

                    <?php
                    // création d'une condition qui vérifie que le formulaire est rempli avant l'envoi. isset() permet de vérifier si un élément a été placé.
                        if(isset($_POST['formsend_2'])){
                            // permet d'éviter la répétition de $_POST dans le code
                            extract($_POST);
                            // je fais une requête préparé pour des questions de sécurité
                            $q = $db->prepare("UPDATE utilisateur
                                set id_asso = :id_asso
                                where id_utilisateur = :id_user;");
                            $q -> execute ([
                                'id_user' => $_SESSION['id_utilisateur'],
                                'id_asso' => $choix_asso                            
                            ]);
                        }      
                    ?>
                
            </div>
    </div> 

<!-- TAB 3 Mes préférences -->

        <div id="tab-3" class="tab-content">
            <div id='formulaire_cate_asso'>
                <form method="post">
                <h1>Mes préférences</h1>
                <h2> N'hésitez pas à modifier vos préférences ci-dessous pour que votre compte TALL soit parfaitement à jour.</h2>
                <h2> <br> <h/2>
                        <?php
                        $q = $db->prepare("SELECT * FROM CATEGORIE ORDER by id_cate;");
                        $q->execute();
                        //récupération du résultat de la requête dans une variable :
                        $liste_cate= $q->fetchAll();                
                        ?>
                        <div class="horizontale_input">
                        <input  id="checkbox" type="checkbox" class="cm-toggle" name="cate_1" id="cate_1" value =<?php print($liste_cate[0][0]) ?>><label for="checkbox" ></label>
                        <p class="reponse_php"><?php print($liste_cate[0][1]) ?></p><br>
                        </div>
                        <div class="horizontale_input">
                        <input id="checkbox1" type="checkbox" class="cm-toggle" name="cate_2" id="cate_2" value =<?php print($liste_cate[1][0]) ?>><label for="checkbox1" ></label> 
                        <p class="reponse_php"><?php print($liste_cate[1][1]) ?></p><br>
                        </div>
                        <div class="horizontale_input">
                        <input id="checkbox2" type="checkbox" class="cm-toggle" name="cate_3" id="cate_3" value =<?php print($liste_cate[2][0]) ?>><label for="checkbox2" ></label> 
                        <p class="reponse_php"><?php print($liste_cate[2][1]) ?></p><br>
                        </div>
                        <div class="horizontale_input">
                        <input id="checkbox3" type="checkbox"class="cm-toggle" name="cate_4" id="cate_4" value =<?php print($liste_cate[3][0]) ?>><label for="checkbox3" ></label> 
                        <p class="reponse_php"><?php print($liste_cate[3][1]) ?></p><br>
                        </div>
                        <div class="horizontale_input">
                        <input id="checkbox4" type="checkbox"class="cm-toggle" name="cate_5" id="cate_5" value =<?php print($liste_cate[4][0]) ?>><label for="checkbox4" ></label> 
                        <p class="reponse_php"><?php print($liste_cate[4][1]) ?></p><br>
                        </div>
                    <!-- le type submit permet de soumettre le formulaire, génère un bouton envoyer -->
                    <input type="submit" name="formsend_1" id="formsend" value="Sauvegarder"><br>
                    
                </form>

                <?php
                // création d'une condition qui vérifie que le formulaire est rempli avant l'envoi. isset() permet de vérifier si un élément a été placé.
                    if(isset($_POST['formsend_1'])){
                        // permet d'éviter la répétition de $_POST dans le code
                        extract($_POST);
                        //la valeur des catégories est remplacée par nulle si les cases ne sont pas cochées :
                        if (empty ($cate_1)){
                            $cate_1 ='null';
                        }
                        else{                        
                        }
                        if (empty ($cate_2)){
                            $cate_2 ='null';
                        }
                        else{                        
                        }
                        if (empty ($cate_3)){
                            $cate_3 ='null';
                        }
                        else{                        
                        }
                        if (empty ($cate_4)){
                            $cate_4 ='null';
                        }
                        else{                        
                        }
                        if (empty ($cate_5)){
                            $cate_5 ='null';
                        }
                        else{                        
                        }
            
                        // je fais une requête préparé pour des questions de sécurité
                        $q = $db->prepare("UPDATE utilisateur
                            set id_cate_1 = :id_cate_1, id_cate_2=:id_cate_2, id_cate_3=:id_cate_3, id_cate_4=:id_cate_4, id_cate_5=:id_cate_5
                            where id_utilisateur = :id_user");
                        $q -> execute ([
                            'id_user' => $_SESSION['id_utilisateur'],
                            'id_cate_1' => $cate_1,                                    
                            'id_cate_2' => $cate_2,
                            'id_cate_3' => $cate_3,
                            'id_cate_4' => $cate_4,
                            'id_cate_5' => $cate_5                                 
                        ]);
                }      
                ?>
            
            </div>
        </div>

<!--  TAB 4 Quitter TALL -->
        <div id='formulaire_suppression'>
            <div id="tab-4" class="tab-content">        
                <form method='post'>
                    <!-- Choix d'une association -->
                    <h1>Quitter TALL</h1>
                    <h2> Si vous souhaiter supprimer votre compte, merci d'indiquer votre mot de passe, la suppression sera automatique et votre compte définitivement supprimé de notre base de données.</h2>
                    <h2> Attention cette action est définitive, si vous changez d'avis, il faudra recréer un compte.</h2>
                    <!-- ici on a un type password -->
                        <input type="password" name="lpassword" id="choix" placeholder="Votre mot de passe" required>

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
                                $q = $db->prepare("DELETE FROM utilisateur
                                    where id_utilisateur = :id_user;");
                                $q -> execute ([
                                    'id_user' => $_SESSION['id_utilisateur']                  
                                ]);
                            }
                        }
                    }
                            
                ?>

                </div> 
        </div>

</div> 

<!-- partial -->

<!-- <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script> -->
<script  src="js/page_user.js"></script>


</body>
</html>
