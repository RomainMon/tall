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
            <h2>Création d'un nouveau compte</h2>
            <!-- création du formulaire d'inscription -->
            <!-- la method dans form définit la méthode d'envoie du formulaire. post : envoie les données d'une page à l'autre (méthode recommandé). get : envoie les infos par URL. -->
            <form method="post" id="container">
                <!-- le type text pour le nom d'utilisateur -->
                <input type="text" name="nom" id="nom" placeholder="Votre nom" required>
                <!-- le type text pour le prénom d'utilisateur -->
                <input type="text" name="prenom" id="prenom" placeholder="Votre prénom" required>
                <!-- le type email contraint l'utilisateur d'insérer un text avec un @ dedans -->
                <input type="email" name="email" id="email" placeholder="Votre email" required>
                <!-- ici on a un type password -->
                <input type="password" name="password" id="password" placeholder="Votre mot de passe" required>
                <!-- confirmation du MDP -->
                <input type="password" name="cpassword" id="cpassword" placeholder="Confirmer votre mot de passe" required>
                <!-- le type submit permet de soumettre le formulaire, génère un bouton envoyer -->
                <input type="tel" name="telephone" id="telephone" placeholder="Vore Numéro de telephone"><br>
                <!-- choix des catégories la liste des catégories est récupérées à partir de la base de données comme ça si on change dans la BD ça changera ici aussi-->
                <?php
                $q = $db->prepare("SELECT * FROM CATEGORIE ORDER by id_cate;");
                $q->execute();
                //récupération du résultat de la requête dans une variable :
                $liste_cate= $q->fetchAll();                
                ?>
                <input type="checkbox" name="cate_1" id="cate_1" value =<?php print($liste_cate[0][0]) ?>> 
                <?php print($liste_cate[0][1]) ?><br>
                <input type="checkbox" name="cate_2" id="cate_2" value =<?php print($liste_cate[1][0]) ?>> 
                <?php print($liste_cate[1][1]) ?><br>
                <input type="checkbox" name="cate_3" id="cate_3" value =<?php print($liste_cate[2][0]) ?>> 
                <?php print($liste_cate[2][1]) ?><br>
                <input type="checkbox" name="cate_4" id="cate_4" value =<?php print($liste_cate[3][0]) ?>> 
                <?php print($liste_cate[3][1]) ?><br>
                <input type="checkbox" name="cate_5" id="cate_5" value =<?php print($liste_cate[14][0]) ?>> 
                <?php print($liste_cate[14][1]) ?><br>

                <!-- le type submit permet de soumettre le formulaire, génère un bouton envoyer -->
                <input type="submit" name="formsend" id="formsend" value="S'inscrire"><br>
                
            </form>

            <?php
            // création d'une condition qui vérifie que le formulaire est rempli avant l'envoi. isset() permet de vérifier si un élément a été placé.
                if(isset($_POST['formsend'])){
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
                        $cate_5 ='null';
                    }
                    else{                        
                    }
                    if (empty ($cate_5)){
                        $cate_5 ='null';
                    }
                    else{                        
                    }

                    if (!empty($password) && !empty($cpassword) && !empty($email)){
                        // vérification que le mdp corresponde en à la confirmation du mdp
                        if($password == $cpassword){
                            // cryptage des mots de passe
                            $options = [
                                'cost' => 12,
                            ];
                            $hashpass = password_hash($password, PASSWORD_BCRYPT, $options);

                            // je vérifie que l'email entré par l'utilisateur n'existe pas déjà
                            $c = $db->prepare("SELECT email FROM utilisateur where email = :email");
                            $c->execute(['email' => $email]);
                            // permet de compter le résultat de la requête précédente
                            $result = $c->rowCount();
                            // la condition suivante s'execute si l'email n'existe pas
                            if($result == 0){
                                // je fais une requête préparé pour des questions de sécurité
                                $q = $db->prepare("INSERT INTO utilisateur(
                                    nom,prenom,email,mdp,telephone,id_cate_1,id_cate_2,id_cate_3,id_cate_4,id_cate_5)
                                    VALUES(:nom,:prenom,:email,:mdp,:telephone,:id_cate_1,:id_cate_2,:id_cate_3,:id_cate_4,:id_cate_5)");
                                $q -> execute ([
                                    'nom' => $nom,
                                    'prenom' => $prenom,
                                    'email' => $email,
                                    'mdp' => $hashpass,
                                    'telephone'=>$telephone,
                                    'id_cate_1' =>$cate_1,                                    
                                    'id_cate_2' =>$cate_2,
                                    'id_cate_3' =>$cate_3,
                                    'id_cate_4' =>$cate_4,
                                    'id_cate_5' =>$cate_5,
                                    
                                ]);
                                //affichage d'un message pour dire que le compte a été créé
                                //echo "Le compte a été créée";
                                $message='Le compte a été créé';                                
                                echo '<script type="text/javascript">window.alert("'.$message.'");</script>';                                
                                //Récupération des données de la session
                                //$_SESSION['nom'] = $nom;
                                //$_SESSION['prenom'] = $prenom;
                                //$_SESSION['email'] = $email;                                
                                sleep(1);
                                //Renvoi vers la page utilisateur :
                                header('Location: connexion.php');
                            }else{
                                echo "Cet email existe déjà";
                            }

                        }
                    }else{
                        echo "les champs ne sont pas tous remplis";
                    }
                    
                }
            ?>
        </div>
    </body>
</html>