<?php 
//Démarrage de la session
session_start();
// lancement de la fonction ob_start() qui permet de faire un header (lancement d'une autre page après avoir placé du html ou un print ou un echo)
ob_start();
?>


    <head>
       <meta charset="utf-8">
        <!-- importer le fichier de style -->
        <link rel="stylesheet" href="css/new_connect.css" media="screen" type="text/css" />
        <!-- appel de l'api google jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <!-- appel du script js jquery -->
        <script src='js/jquery_site.js'></script>
    </head>
    <body>
    
        <div id="container">
            <?php
            // connexion à la db
            include 'include/database.php';
            //  recupération de la varibable db pour faire des requêtes
            global $db;
            ?>            
            <!-- création du formulaire d'inscription -->
            <!-- la method dans form définit la méthode d'envoie du formulaire. post : envoie les données d'une page à l'autre (méthode recommandé). get : envoie les infos par URL. -->
            <form method="post" id="container">
            <h2>Création d'un nouveau compte</h2>
                <div id="civilite"> 
                    <h4>Vos coordonnées</h4>
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
                    <!-- numéro de téléphone -->
                    <input type="tel" name="telephone" id="telephone" placeholder="Votre Numéro de telephone"><br>
                <!-- Commune -->
                </div>
                <div id = "adresse">               
                    <select name ="choix_commune" id="choix_commune" required>
                        <option selected="selected">Commune</option>
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
                    <br>
                    <!-- adresse -->
                    <select name ="choix_adresse" id="choix_adresse" required>
                        <option selected="selected">Rue</option>                    
                    <br>
                    </select>
                    <!-- numero -->
                    <select name ="choix_numero" id="choix_numero" required>
                        <option selected="selected">Numero</option>                    
                    <br>
                    </select>
                    
                    <select name ="choix_rep" id="choix_rep">
                        <option selected="selected">Complément d'adresse</option>                    
                    <br>
                    </select>
                </div>    
                <!-- choix des catégories la liste des catégories est récupérées à partir de la base de données comme ça si on change dans la BD ça changera ici aussi-->
                <div id="choix_asso"> 
                    <h4>Quels sont les domaines qui vous intéressent ?</h4>
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
                    
                    <!-- Choix d'une association -->
                    <p>Etes-vous membre d'une association ?</p>
                    <select name ="choix_asso" id="choix_asso">
                        <option selected="selected" value="">Sélectionner une valeur</option>
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
                </div>
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
                        $cate_4 ='null';
                    }
                    else{                        
                    }
                    if (empty ($cate_5)){
                        $cate_5 ='null';
                    }
                    else{                        
                    }

                    if (!empty($password) && !empty($cpassword) && !empty($email)){
                        // vérification que le mdp corresponde à la confirmation du mdp
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

                                // recuperation des id_adresse et geom de la table vue_adresse

                                // pour eviter les conflits si une adresse n'a pas de reperes dans la requete sql on teste si la valeur renvoyee par choix_rep est vide
                                if ($choix_rep == ''){
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
                                $q = $db->prepare("INSERT INTO utilisateur(
                                    nom,prenom,email,mdp,telephone,id_cate_1,id_cate_2,id_cate_3,id_cate_4,id_cate_5,id_asso,id_adresse,geom)
                                    VALUES(:nom,:prenom,:email,:mdp,:telephone,:id_cate_1,:id_cate_2,:id_cate_3,:id_cate_4,:id_cate_5,:id_asso,:id_adresse,:geom)");
                                $q -> execute ([
                                    'nom' => $nom,
                                    'prenom' => $prenom,
                                    'email' => $email,
                                    'mdp' => $hashpass,
                                    'telephone'=> $telephone,
                                    'id_cate_1' => $cate_1,                                    
                                    'id_cate_2' => $cate_2,
                                    'id_cate_3' => $cate_3,
                                    'id_cate_4' => $cate_4,
                                    'id_cate_5' => $cate_5,
                                    'id_asso' => $choix_asso,
                                    'id_adresse'=> $adresse[0],
                                    'geom' => $adresse[1]                                    
                                ]);                                
                                //Renvoi vers la page utilisateur :
                                header('Location: connexion.php',TRUE);
                            }else{
                                // echo "Cet email existe déjà";
                                ?>
                                <h4>Cet email existe déjà</h4>
                                <?php
                            }
                        }else{
                            // echo "les champs ne sont pas tous remplis";
                            ?>                            
                            <h4>Les mots de passe sont différents</h4>
                            <?php                        
                        }
                    }
                    else{
                        // echo "les champs ne sont pas tous remplis";
                        ?>                        
                        <h4>les champs ne sont pas tous remplis</h4>
                        <?php 
                    }                
                }
            ?>
        </div>
        <script src ="js/jquery_site.js"></script>
    </body>
</html>