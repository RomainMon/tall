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
</head>
<body>
            <?php
            // connexion à la db
            include 'include/database.php';
            //  recupération de la varibable db pour faire des requêtes
            global $db;
            ?> 
              <!-- récupération des éléments de session utilisateur pour les afficher  -->
            <h1>Mon profil utilisateur</h1>
            <p>Nom : <?= $_SESSION['nom']; ?></p></br>
            <p>Prenom : <?= $_SESSION['prenom']; ?></p></br>
            <p>Email : <?= $_SESSION['email']; ?></p></br>
            <p>Mon numéro : <?= $_SESSION['telephone']; ?></p></br>
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
            <p>Mon adresse : <?= $value['numero']; ?> <?= $value['rep']; ?> <?= $value['nom_1']; ?> <?= $value['code_post']; ?> <?= $value['nom_com']; ?></p>
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
                <p>Mes centres d'intérêts : <p>
            <?php

            foreach($cate_user as $value_cate){
            ?>
            <p><?php if (isset($value_cate)){echo $value_cate;}else{echo "Vous n'avez pas choisi d'association";}?> </p>
            <?php
            }
            ?>

            <div id="container">         
            <!-- création du formulaire d'inscription -->
            <!-- la method dans form définit la méthode d'envoie du formulaire. post : envoie les données d'une page à l'autre (méthode recommandé). get : envoie les infos par URL. -->
            <form method="post" id="container">
            <h2>Modification des informations relatives à mon profil</h2>
                <div id="civilite"> 
                    <h4>Vos coordonnées</h4>
                    <!-- le type text pour le nom d'utilisateur -->
                    <input type="text" name="nom" id="nom" value=<?= $_SESSION['nom']; ?> required>
                    <!-- le type text pour le prénom d'utilisateur -->
                    <input type="text" name="prenom" id="prenom" value=<?= $_SESSION['prenom']; ?> required>
                    <!-- le type email contraint l'utilisateur d'insérer un text avec un @ dedans -->
                    <input type="email" name="email" id="email" value=<?= $_SESSION['email']; ?> required>
                    <!-- numéro de téléphone -->
                    <input type="tel" name="telephone" id="telephone" value=<?= $_SESSION['telephone']; ?>><br>
                <!-- Commune -->
                </div>
                <div id = "adresse">   
                    <h5>Commune</h5>            
                    <select name ="choix_commune" id="choix_commune">
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
                    <h5>Nom de rue</h5>   
                    <br>
                    <!-- adresse -->
                    <!-- une partie de la liste déroulante s'execute avec une requete jquery ajax -->
                    <select name ="choix_adresse" id="choix_adresse">
                        <option selected="selected" value=<?php $nom_rue; ?>><?= $nom_rue; ?></option>                
                    <br>                    
                    </select>
                    <h5>Numéro de rue</h5> 
                    <!-- numero -->
                    <!-- une partie de la liste déroulante s'execute avec une requete jquery ajax -->
                    <select name ="choix_numero" id="choix_numero">
                        <option selected="selected" value=<?php $num_rue; ?>><?= $num_rue; ?></option>                   
                    <br>
                    </select>
                    <h5>Complément d'adresse</h5> 
                    <!-- une partie de la liste déroulante s'execute avec une requete jquery ajax -->
                    <select name ="choix_rep" id="choix_rep">
                        <option selected="selected" value=<?php $complement_addresse; ?>><?= $complement_addresse; ?></option>                    
                    <br>
                    </select>
                </div>
                <!-- le type submit permet de soumettre le formulaire, génère un bouton envoyer -->
                <input type="submit" name="formsend" id="formsend" value="Mettre à jour"><br>
                
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
        </div>
        <div id='formulaire_cate_asso'>
            <form method="post">
            <h4>Quels sont les domaines qui vous intéressent ?</h4>
                    <?php
                    $q = $db->prepare("SELECT * FROM CATEGORIE ORDER by id_cate;");
                    $q->execute();
                    //récupération du résultat de la requête dans une variable :
                    $liste_cate= $q->fetchAll();                
                    ?>
                    <br>
                    <input type="checkbox" class="cm-toggle" name="cate_1" id="cate_1" value =<?php print($liste_cate[0][0]) ?>> 
                    <?php print($liste_cate[0][1]) ?><br>
                    <input type="checkbox" class="cm-toggle" name="cate_2" id="cate_2" value =<?php print($liste_cate[1][0]) ?>> 
                    <?php print($liste_cate[1][1]) ?><br>
                    <input type="checkbox" class="cm-toggle" name="cate_3" id="cate_3" value =<?php print($liste_cate[2][0]) ?>> 
                    <?php print($liste_cate[2][1]) ?><br>
                    <input type="checkbox" class="cm-toggle" name="cate_4" id="cate_4" value =<?php print($liste_cate[3][0]) ?>> 
                    <?php print($liste_cate[3][1]) ?><br>
                    <input type="checkbox" class="cm-toggle" name="cate_5" id="cate_5" value =<?php print($liste_cate[4][0]) ?>> 
                    <?php print($liste_cate[4][1]) ?><br>
                <!-- le type submit permet de soumettre le formulaire, génère un bouton envoyer -->
                <input type="submit" name="formsend_1" id="formsend_1" value="Mettre à jour"><br>
                
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

        <div id='formulaire_adherent_asso'>
            <form method='post'>
                <!-- Choix d'une association -->
                <p>Etes-vous membre d'une association ?</p>
                    <select name ="choix_asso" id="choix_asso">
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
                <input type="submit" name="formsend_2" id="formsend_2" value="Mettre à jour"><br>
                
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

        
        <div id='formulaire_suppression'>
            <form method='post'>
                <!-- Choix d'une association -->
                <p>Suppression du compte</p>
                 <!-- ici on a un type password -->
                 <input type="password" name="lpassword" id="lpassword" placeholder="Votre mot de passe" required>

                 <!-- envoi du formulaire en html -->
                <input type="submit" name="formsend_3" id="formsend_3" value="Supprimer mon compte"><br>
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

</body>
</html>
