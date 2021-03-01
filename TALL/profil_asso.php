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
</head>
<body>
        <?php
        // connexion à la db
        include 'include/database.php';
        //  recupération de la varibable db pour faire des requêtes
        global $db;
        ?> 
            <!-- récupération des éléments de session utilisateur pour les afficher  -->
        <h1>Mon profil association</h1>
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
        
        <h3>Modification du profil</h3>
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
            <input type="text" name="descriptif" id="descriptif" size="50" maxlength="300" spellcheck="true" value='<?= $_SESSION['description']; ?>' required>
            <!-- descriptif de l'asso -->
            <input type="text" name="site_web" id="site_web" value=<?= $_SESSION['site_web']; ?>>
            <!-- courriel visible pas les particuliers-->
            <input type="email" name="courriel_for_user" id="courriel_for_user" value=<?= $_SESSION['courriel']; ?>>
            <!-- numéro de téléphone -->
            <input type="tel" name="telephone" id="telephone" value=<?= $_SESSION['telephone']; ?>><br>

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


</body>
</html>