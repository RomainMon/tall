<?php 
//Démarrage de la session
session_start();
// lancement de la fonction ob_start() qui permet de faire un header (lancement d'une autre page après avoir placé du html ou un print ou un echo)
ob_start();
?>


    <head>
       <meta charset="utf-8">
       <title>TALL</title>
        <link rel="shortcut icon" type="image/ico" href="img/favicon.ico"/>
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
            <h1>Création de votre compte</h1>            
                <h3>Sélectionner votre association</h3>
                    <select name ="choix_asso" id="choix_asso" required>
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
                <h3>Rentrez votre email</h3>
                <!-- le type email contraint l'utilisateur d'insérer un text avec un @ dedans -->
                <input type="email" name="email" id="email" placeholder="Votre email" required>

                <h3>Choisissez un mot de passe</h3> 
                    <input type="password" name="password" id="password" placeholder="Votre mot de passe" required>
                    <!-- confirmation du MDP -->
                    <input type="password" name="cpassword" id="cpassword" placeholder="Confirmer votre mot de passe" required>
           
                <!-- le type submit permet de soumettre le formulaire, génère un bouton envoyer -->
                <input class="btn" type="submit" name="formsend" id="formsend" value="S'inscrire"><br>
                
            </form>

            <?php
            // création d'une condition qui vérifie que le formulaire est rempli avant l'envoi. isset() permet de vérifier si un élément a été placé.
                if(isset($_POST['formsend'])){
                    // permet d'éviter la répétition de $_POST dans le code
                    extract($_POST);
                    
                    if (!empty($password) && !empty($cpassword)){
                        // vérification que le mdp corresponde en à la confirmation du mdp
                        if($password == $cpassword){
                            // cryptage des mots de passe
                            $options = [
                                'cost' => 12,
                            ];
                            $hashpass = password_hash($password, PASSWORD_BCRYPT, $options);

                            // je vérifie que le compte connexion association n'existe pas déjà
                            $c = $db->prepare("SELECT email FROM asso_connexion where email = :email");
                            $c->execute(['email' => $email]);
                            // permet de compter le résultat de la requête précédente
                            $result = $c->rowCount();
                            // la condition suivante s'execute si le compte n'existe pas
                            if($result == 0){

                                // je fais une requête préparé pour des questions de sécurité
                                $q = $db->prepare("INSERT INTO asso_connexion(
                                    id_asso,mdp,email)
                                    VALUES(:id_asso,:mdp,:email)");
                                $q -> execute ([
                                    'id_asso' => $choix_asso,
                                    'mdp' => $hashpass,
                                    'email'=>$email                                                                       
                                ]);
                                //affichage d'un message pour dire que le compte a été créé
                                //echo "Le compte a été créée";
                                // $message='Le compte a été créé';                                
                                // echo '<script>alert("Le compte a été créé") ; </script>';                                
                                // Récupération des données de la session
                                // $_SESSION['nom'] = $nom;
                                // $_SESSION['prenom'] = $prenom;
                                // $_SESSION['email'] = $email;
                                // sleep(1);
                                //Renvoi vers la page utilisateur :
                                header('Location: connexion.php',TRUE);
                            }else{
                                echo "Cette association possède déjà un compte";
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