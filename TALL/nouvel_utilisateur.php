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
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- appel de l'api google jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <!-- appel du script js jquery -->
        <script src='js/jquery_site.js'></script>
        <link rel="stylesheet" href="css/new_connect.css" media="screen" type="text/css" />
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
            <h1>Création d'un nouveau compte</h1>
                <div id="civilite"> 
                    <h3>Vos coordonnées</h3>
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
                    <h3>Quels sont les domaines qui vous intéressent ?</h3>
                    <?php
                    $q = $db->prepare("SELECT * FROM CATEGORIE ORDER by id_cate;");
                    $q->execute();
                    //récupération du résultat de la requête dans une variable :
                    $liste_cate= $q->fetchAll();                
                    ?>
                    <br>
                    <div class="horizontale_input">
                    <input  id="checkbox" type="checkbox" class="cm-toggle" name="cate_1" id="cate_1" value =<?php print($liste_cate[0][0]) ?>><label for="checkbox" ></label>
                    <h6 class="reponse_php"><?php print($liste_cate[0][1]) ?></h6><br>
                    </div>
                    <div class="horizontale_input">
                    <input id="checkbox1" type="checkbox" class="cm-toggle" name="cate_2" id="cate_2" value =<?php print($liste_cate[1][0]) ?>><label for="checkbox1" ></label> 
                    <h6 class="reponse_php"><?php print($liste_cate[1][1]) ?></h6><br>
                    </div>
                    <div class="horizontale_input">
                    <input id="checkbox2" type="checkbox" class="cm-toggle" name="cate_3" id="cate_3" value =<?php print($liste_cate[2][0]) ?>><label for="checkbox2" ></label> 
                    <h6 class="reponse_php"><?php print($liste_cate[2][1]) ?></h6><br>
                    </div>
                    <div class="horizontale_input">
                    <input id="checkbox3" type="checkbox"class="cm-toggle" name="cate_4" id="cate_4" value =<?php print($liste_cate[3][0]) ?>><label for="checkbox3" ></label> 
                    <h6 class="reponse_php"><?php print($liste_cate[3][1]) ?></h6><br>
                    </div>
                    <div class="horizontale_input">
                    <input id="checkbox4" type="checkbox"class="cm-toggle" name="cate_5" id="cate_5" value =<?php print($liste_cate[4][0]) ?>><label for="checkbox4" ></label> 
                    <h6 class="reponse_php"><?php print($liste_cate[4][1]) ?></h6><br>
                    </div>
                    
                    <!-- Choix d'une association -->
                    <h3>Etes-vous membre d'une association ?</h3>
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
                    <br>                 
                    <h3 id="membre_asso">Seriez-vous prêts à vous engager en tant que bénévole dans une association ?</h3>
                    <br>
                    <div class="horizontale_input">
                        <input id="benevole" type="checkbox"class="cm-toggle" name="benevole" value ="oui"><label for="benevole" ></label> 
                        <h6 class="reponse_php">oui</h6><br> 
                    </div>                   
                    <h3>Autorisez-vous Tall à communiquer votre adresse e-mail pour que les associations concernées par vos préférences vous contactent ?</h3>
                    <br>
                    <div class="horizontale_input">
                        <input id="contact_asso" type="checkbox"class="cm-toggle" name="contact" value ="oui"><label for="contact_asso" ></label> 
                        <h6 class="reponse_php">oui</h6><br>
                    </div>                    
                    <div class="horizontale_input" id="cgu_retour">
                        <input id="cgu_input" type="checkbox"class="cm-toggle" name="cgu" value ="oui" required><label for="cgu_input" ></label> 
                        <a href="#cgu_text">J'accepte les CGU</a>
                    </div>
                    
                <!-- le type submit permet de soumettre le formulaire, génère un bouton envoyer -->
                <input class="btn" type="submit" name="formsend" id="formsend" value="S'inscrire"><br>                  
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
                                    nom,prenom,email,mdp,telephone,id_cate_1,id_cate_2,id_cate_3,id_cate_4,id_cate_5,id_asso,id_adresse,benevole,contact_asso,geom)
                                    VALUES(:nom,:prenom,:email,:mdp,:telephone,:id_cate_1,:id_cate_2,:id_cate_3,:id_cate_4,:id_cate_5,:id_asso,:id_adresse,:benevole,:contact_asso,:geom)");
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
                                    'benevole'=>$benevole,
                                    'contact_asso'=>$contact_asso,
                                    'geom' => $adresse[1]                                    
                                ]);                                
                                //Renvoi vers la page utilisateur :
                                header('Location: connexion.php',TRUE);
                            }else{
                                // echo "Cet email existe déjà";
                                ?>
                                <h3>Cet email existe déjà</h3>
                                <?php
                            }
                        }else{
                            // echo "les champs ne sont pas tous remplis";
                            ?>                            
                            <h3>Les mots de passe sont différents</h3>
                            <?php                        
                        }
                    }
                    else{
                        // echo "les champs ne sont pas tous remplis";
                        ?>                        
                        <h3>les champs ne sont pas tous remplis</h3>
                        <?php 
                    }                
                }
            ?>
            <div id="cgu_text">

                <h1>Conditions Générales d'Utilisation (CGU)</h1>
                <h2>Définitions</h2>
                <p><b>Client :</b> tout professionnel ou personne physique capable au sens des articles 1123 et suivants du Code civil, ou personne morale, qui visite le Site objet des présentes conditions générales.<br>
                <b>Prestations et Services :</b> <a href="https://www.tall.fr">https://www.tall.fr</a> met à disposition des Clients :</p>

                <p><b>Contenu :</b> Ensemble des éléments constituants l’information présente sur le Site, notamment textes – images – vidéos.</p>

                <p><b>Informations clients :</b> Ci après dénommé « Information (s) » qui correspondent à l’ensemble des données personnelles susceptibles d’être détenues par <a href="https://www.tall.fr">https://www.tall.fr</a> pour la gestion de votre compte, de la gestion de la relation client et à des fins d’analyses et de statistiques.</p>


                <p><b>Utilisateur :</b> Internaute se connectant, utilisant le site susnommé.</p>
                <p><b>Informations personnelles :</b> « Les informations qui permettent, sous quelque forme que ce soit, directement ou non, l'identification des personnes physiques auxquelles elles s'appliquent » (article 4 de la loi n° 78-17 du 6 janvier 1978).</p>
                <p>Les termes « données à caractère personnel », « personne concernée », « sous traitant » et « données sensibles » ont le sens défini par le Règlement Général sur la Protection des Données (RGPD : n° 2016-679)</p>

                <h2>1. Présentation du site internet.</h2>
                <p>En vertu de l'article 6 de la loi n° 2004-575 du 21 juin 2004 pour la confiance dans l'économie numérique, il est précisé aux utilisateurs du site internet <a href="https://www.tall.fr">https://www.tall.fr</a> l'identité des différents intervenants dans le cadre de sa réalisation et de son suivi:
                </p><p><strong>Propriétaire</strong> :   Tall Errant   – 168 rue des pensées sombres 98950 Tallalala<br>
                              
                <strong>Responsable publication</strong> : Tall Errant – tall.errant@tall.fr<br>
                Le responsable publication est une personne physique ou une personne morale.<br>
                <strong>Webmaster</strong> : Tall Errant – tall.errant@tall.fr<br>
                <strong>Hébergeur</strong> : TALLESBROUETTES – 168 rue des pensées sombres 98950 Tallalala 6374652045<br>
                <strong>Délégué à la protection des données</strong> : Tall Errant – tall.errant@tall.fr<br>
                </p>

                



                <h2>2. Conditions générales d’utilisation du site et des services proposés.</h2>

                <p>Le Site constitue une œuvre de l’esprit protégée par les dispositions du Code de la Propriété Intellectuelle et des Réglementations Internationales applicables. 
                Le Client ne peut en aucune manière réutiliser, céder ou exploiter pour son propre compte tout ou partie des éléments ou travaux du Site.</p>

                <p>L’utilisation du site <a href="https://www.tall.fr">https://www.tall.fr</a> implique l’acceptation pleine et entière des conditions générales d’utilisation ci-après décrites. Ces conditions d’utilisation sont susceptibles d’être modifiées ou complétées à tout moment, les utilisateurs du site <a href="https://www.tall.fr">https://www.tall.fr</a> sont donc invités à les consulter de manière régulière.</p>

                <p>Ce site internet est normalement accessible à tout moment aux utilisateurs. Une interruption pour raison de maintenance technique peut être toutefois décidée par <a href="https://www.tall.fr">https://www.tall.fr</a>, qui s’efforcera alors de communiquer préalablement aux utilisateurs les dates et heures de l’intervention.
                Le site web <a href="https://www.tall.fr">https://www.tall.fr</a> est mis à jour régulièrement par <a href="https://www.tall.fr">https://www.tall.fr</a> responsable. De la même façon, les mentions légales peuvent être modifiées à tout moment : elles s’imposent néanmoins à l’utilisateur qui est invité à s’y référer le plus souvent possible afin d’en prendre connaissance.</p>

                <h2>3. Description des services fournis.</h2>

                <p>Le site internet <a href="https://www.tall.fr">https://www.tall.fr</a> a pour objet de fournir une information concernant l’ensemble des activités de la société.
                <a href="https://www.tall.fr">https://www.tall.fr</a> s’efforce de fournir sur le site <a href="https://www.tall.fr">https://www.tall.fr</a> des informations aussi précises que possible. Toutefois, il ne pourra être tenu responsable des oublis, des inexactitudes et des carences dans la mise à jour, qu’elles soient de son fait ou du fait des tiers partenaires qui lui fournissent ces informations.</p>

                <p>Toutes les informations indiquées sur le site <a href="https://www.tall.fr">https://www.tall.fr</a> sont données à titre indicatif, et sont susceptibles d’évoluer. Par ailleurs, les renseignements figurant sur le site <a href="https://www.tall.fr">https://www.tall.fr</a> ne sont pas exhaustifs. Ils sont donnés sous réserve de modifications ayant été apportées depuis leur mise en ligne.</p>

                <h2>4. Limitations contractuelles sur les données techniques.</h2>

                <p>Le site utilise la technologie JavaScript.

                Le site Internet ne pourra être tenu responsable de dommages matériels liés à l’utilisation du site. De plus, l’utilisateur du site s’engage à accéder au site en utilisant un matériel récent, ne contenant pas de virus et avec un navigateur de dernière génération mis-à-jour
                Le site <a href="https://www.tall.fr">https://www.tall.fr</a> est hébergé chez un prestataire sur le territoire de l’Union Européenne conformément aux dispositions du Règlement Général sur la Protection des Données (RGPD : n° 2016-679)</p>

                <p>L’objectif est d’apporter une prestation qui assure le meilleur taux d’accessibilité. L’hébergeur assure la continuité de son service 24 Heures sur 24, tous les jours de l’année. Il se réserve néanmoins la possibilité d’interrompre le service d’hébergement pour les durées les plus courtes possibles notamment à des fins de maintenance, d’amélioration de ses infrastructures, de défaillance de ses infrastructures ou si les Prestations et Services génèrent un trafic réputé anormal.</p>

                <p><a href="https://www.tall.fr">https://www.tall.fr</a> et l’hébergeur ne pourront être tenus responsables en cas de dysfonctionnement du réseau Internet, des lignes téléphoniques ou du matériel informatique et de téléphonie lié notamment à l’encombrement du réseau empêchant l’accès au serveur.</p>

                <h2>5. Propriété intellectuelle et contrefaçons.</h2>

                <p><a href="https://www.tall.fr">https://www.tall.fr</a> est propriétaire des droits de propriété intellectuelle et détient les droits d’usage sur tous les éléments accessibles sur le site internet, notamment les textes, images, graphismes, logos, vidéos, icônes et sons.
                Toute reproduction, représentation, modification, publication, adaptation de tout ou partie des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation écrite préalable de : <a href="https://www.tall.fr">https://www.tall.fr</a>.</p>

                <p>Toute exploitation non autorisée du site ou de l’un quelconque des éléments qu’il contient sera considérée comme constitutive d’une contrefaçon et poursuivie conformément aux dispositions des articles L.335-2 et suivants du Code de Propriété Intellectuelle.</p>

                <h2>6. Limitations de responsabilité.</h2>

                <p><a href="https://www.tall.fr">https://www.tall.fr</a> agit en tant qu’éditeur du site. <a href="https://www.tall.fr">https://www.tall.fr</a>  est responsable de la qualité et de la véracité du Contenu qu’il publie. </p>

                <p><a href="https://www.tall.fr">https://www.tall.fr</a> ne pourra être tenu responsable des dommages directs et indirects causés au matériel de l’utilisateur, lors de l’accès au site internet <a href="https://www.tall.fr">https://www.tall.fr</a>, et résultant soit de l’utilisation d’un matériel ne répondant pas aux spécifications indiquées au point 4, soit de l’apparition d’un bug ou d’une incompatibilité.</p>

                <p><a href="https://www.tall.fr">https://www.tall.fr</a> ne pourra également être tenu responsable des dommages indirects (tels par exemple qu’une perte de marché ou perte d’une chance) consécutifs à l’utilisation du site <a href="https://www.tall.fr">https://www.tall.fr</a>.
                Des espaces interactifs (possibilité de poser des questions dans l’espace contact) sont à la disposition des utilisateurs. <a href="https://www.tall.fr">https://www.tall.fr</a> se réserve le droit de supprimer, sans mise en demeure préalable, tout contenu déposé dans cet espace qui contreviendrait à la législation applicable en France, en particulier aux dispositions relatives à la protection des données. Le cas échéant, <a href="https://www.tall.fr">https://www.tall.fr</a> se réserve également la possibilité de mettre en cause la responsabilité civile et/ou pénale de l’utilisateur, notamment en cas de message à caractère raciste, injurieux, diffamant, ou pornographique, quel que soit le support utilisé (texte, photographie …).</p>

                <h2>7. Gestion des données personnelles.</h2>

                <p>Le Client est informé des réglementations concernant la communication marketing, la loi du 21 Juin 2014 pour la confiance dans l’Economie Numérique, la Loi Informatique et Liberté du 06 Août 2004 ainsi que du Règlement Général sur la Protection des Données (RGPD : n° 2016-679). </p>

                <h3>7.1 Responsables de la collecte des données personnelles</h3>

                <p>Pour les Données Personnelles collectées dans le cadre de la création du compte personnel de l’Utilisateur et de sa navigation sur le Site, le responsable du traitement des Données Personnelles est : Tall Errant. <a href="https://www.tall.fr">https://www.tall.fr</a>est représenté par Tall Errant, son représentant légal</p>

                <p>En tant que responsable du traitement des données qu’il collecte, <a href="https://www.tall.fr">https://www.tall.fr</a> s’engage à respecter le cadre des dispositions légales en vigueur. Il lui appartient notamment au Client d’établir les finalités de ses traitements de données, de fournir à ses prospects et clients, à partir de la collecte de leurs consentements, une information complète sur le traitement de leurs données personnelles et de maintenir un registre des traitements conforme à la réalité.
                Chaque fois que <a href="https://www.tall.fr">https://www.tall.fr</a> traite des Données Personnelles, <a href="https://www.tall.fr">https://www.tall.fr</a> prend toutes les mesures raisonnables pour s’assurer de l’exactitude et de la pertinence des Données Personnelles au regard des finalités pour lesquelles <a href="https://www.tall.fr">https://www.tall.fr</a> les traite.</p>
                 
                <h3>7.2 Finalité des données collectées</h3>
                 
                <p><a href="https://www.tall.fr">https://www.tall.fr</a> est susceptible de traiter tout ou partie des données : </p>

                <ul>
                  
                <li>pour permettre la navigation sur le Site et la gestion et la traçabilité des prestations et services commandés par l’utilisateur : données de connexion et d’utilisation du Site, facturation, historique des commandes, etc. </li>
                 
                <li>pour prévenir et lutter contre la fraude informatique (spamming, hacking…) : matériel informatique utilisé pour la navigation, l’adresse IP, le mot de passe (hashé) </li>
                 
                <li>pour améliorer la navigation sur le Site : données de connexion et d’utilisation </li>
                 
                <li>pour mener des enquêtes de satisfaction facultatives sur <a href="https://www.tall.fr">https://www.tall.fr</a> : adresse email </li>
                <li>pour mener des campagnes de communication (sms, mail) : numéro de téléphone, adresse email</li>


                </ul>

                <p><a href="https://www.tall.fr">https://www.tall.fr</a> ne commercialise pas vos données personnelles qui sont donc uniquement utilisées par nécessité ou à des fins statistiques et d’analyses.</p>
                 
                <h3>7.3 Droit d’accès, de rectification et d’opposition</h3>
                 
                <p>
                Conformément à la réglementation européenne en vigueur, les Utilisateurs de <a href="https://www.tall.fr">https://www.tall.fr</a> disposent des droits suivants : </p>
                 <ul>

                <li>droit d'accès (article 15 RGPD) et de rectification (article 16 RGPD), de mise à jour, de complétude des données des Utilisateurs droit de verrouillage ou d’effacement des données des Utilisateurs à caractère personnel (article 17 du RGPD), lorsqu’elles sont inexactes, incomplètes, équivoques, périmées, ou dont la collecte, l'utilisation, la communication ou la conservation est interdite </li>
                 
                <li>droit de retirer à tout moment un consentement (article 13-2c RGPD) </li>
                 
                <li>droit à la limitation du traitement des données des Utilisateurs (article 18 RGPD) </li>
                 
                <li>droit d’opposition au traitement des données des Utilisateurs (article 21 RGPD) </li>
                 
                <li>droit à la portabilité des données que les Utilisateurs auront fournies, lorsque ces données font l’objet de traitements automatisés fondés sur leur consentement ou sur un contrat (article 20 RGPD) </li>
                 
                <li>droit de définir le sort des données des Utilisateurs après leur mort et de choisir à qui <a href="https://www.tall.fr">https://www.tall.fr</a> devra communiquer (ou non) ses données à un tiers qu’ils aura préalablement désigné</li>
                 </ul>

                <p>Dès que <a href="https://www.tall.fr">https://www.tall.fr</a> a connaissance du décès d’un Utilisateur et à défaut d’instructions de sa part, <a href="https://www.tall.fr">https://www.tall.fr</a> s’engage à détruire ses données, sauf si leur conservation s’avère nécessaire à des fins probatoires ou pour répondre à une obligation légale.</p>
                 
                <p>Si l’Utilisateur souhaite savoir comment <a href="https://www.tall.fr">https://www.tall.fr</a> utilise ses Données Personnelles, demander à les rectifier ou s’oppose à leur traitement, l’Utilisateur peut contacter <a href="https://www.tall.fr">https://www.tall.fr</a> par écrit à l’adresse suivante : </p>
                 
                Tall Errant – DPO, Tall Errant <br>
                168 rue des pensées sombres 98950 Tallalala.
                 
                <p>Dans ce cas, l’Utilisateur doit indiquer les Données Personnelles qu’il souhaiterait que <a href="https://www.tall.fr">https://www.tall.fr</a> corrige, mette à jour ou supprime, en s’identifiant précisément avec une copie d’une pièce d’identité (carte d’identité ou passeport). </p>

                <p>
                Les demandes de suppression de Données Personnelles seront soumises aux obligations qui sont imposées à <a href="https://www.tall.fr">https://www.tall.fr</a> par la loi, notamment en matière de conservation ou d’archivage des documents. Enfin, les Utilisateurs de <a href="https://www.tall.fr">https://www.tall.fr</a> peuvent déposer une réclamation auprès des autorités de contrôle, et notamment de la CNIL (https://www.cnil.fr/fr/plaintes).</p>
                 
                <h3>7.4 Non-communication des données personnelles</h3>

                <p>
                <a href="https://www.tall.fr">https://www.tall.fr</a> s’interdit de traiter, héberger ou transférer les Informations collectées sur ses Clients vers un pays situé en dehors de l’Union européenne ou reconnu comme « non adéquat » par la Commission européenne sans en informer préalablement le client. Pour autant, <a href="https://www.tall.fr">https://www.tall.fr</a> reste libre du choix de ses sous-traitants techniques et commerciaux à la condition qu’il présentent les garanties suffisantes au regard des exigences du Règlement Général sur la Protection des Données (RGPD : n° 2016-679).</p>

                <p>
                <a href="https://www.tall.fr">https://www.tall.fr</a> s’engage à prendre toutes les précautions nécessaires afin de préserver la sécurité des Informations et notamment qu’elles ne soient pas communiquées à des personnes non autorisées. Cependant, si un incident impactant l’intégrité ou la confidentialité des Informations du Client est portée à la connaissance de <a href="https://www.tall.fr">https://www.tall.fr</a>, celle-ci devra dans les meilleurs délais informer le Client et lui communiquer les mesures de corrections prises. Par ailleurs <a href="https://www.tall.fr">https://www.tall.fr</a> ne collecte aucune « données sensibles ».</p>

                <p>
                Les Données Personnelles de l’Utilisateur peuvent être traitées par des filiales de <a href="https://www.tall.fr">https://www.tall.fr</a> et des sous-traitants (prestataires de services), exclusivement afin de réaliser les finalités de la présente politique.</p>
                <p>
                Dans la limite de leurs attributions respectives et pour les finalités rappelées ci-dessus, les principales personnes susceptibles d’avoir accès aux données des Utilisateurs de <a href="https://www.tall.fr">https://www.tall.fr</a> sont principalement les agents de notre service client.</p>
                
                <div ng-bind-html="rgpdHTML"><h3>7.5 Types de données collectées</h3><p>Concernant les utilisateurs d’un Site <a href="https://www.tall.fr">https://www.tall.fr</a>, nous collectons les données suivantes qui sont indispensables au fonctionnement du service&nbsp;, et qui seront conservées pendant une période maximale de 9 mois mois après la fin de la relation contractuelle:<br>Nom, prénom, email, adresse</p><p><a href="https://www.tall.fr">https://www.tall.fr</a> collecte en outre des informations qui permettent d’améliorer l’expérience utilisateur et de proposer des conseils contextualisés&nbsp;:<br>Intérêts pour les domaines écologiques</p><p> Ces &nbsp;données sont conservées pour une période maximale de 9 mois mois après la fin de la relation contractuelle</p></div>


                <h2>8. Notification d’incident</h2>
                <p>
                Quels que soient les efforts fournis, aucune méthode de transmission sur Internet et aucune méthode de stockage électronique n'est complètement sûre. Nous ne pouvons en conséquence pas garantir une sécurité absolue. 
                Si nous prenions connaissance d'une brèche de la sécurité, nous avertirions les utilisateurs concernés afin qu'ils puissent prendre les mesures appropriées. Nos procédures de notification d’incident tiennent compte de nos obligations légales, qu'elles se situent au niveau national ou européen. Nous nous engageons à informer pleinement nos clients de toutes les questions relevant de la sécurité de leur compte et à leur fournir toutes les informations nécessaires pour les aider à respecter leurs propres obligations réglementaires en matière de reporting.</p>
                <p>
                Aucune information personnelle de l'utilisateur du site <a href="https://www.tall.fr">https://www.tall.fr</a> n'est publiée à l'insu de l'utilisateur, échangée, transférée, cédée ou vendue sur un support quelconque à des tiers. Seule l'hypothèse du rachat de <a href="https://www.tall.fr">https://www.tall.fr</a> et de ses droits permettrait la transmission des dites informations à l'éventuel acquéreur qui serait à son tour tenu de la même obligation de conservation et de modification des données vis à vis de l'utilisateur du site <a href="https://www.tall.fr">https://www.tall.fr</a>.</p>

                <h3>Sécurité</h3>

                <p>
                Pour assurer la sécurité et la confidentialité des Données Personnelles et des Données Personnelles de Santé, <a href="https://www.tall.fr">https://www.tall.fr</a> utilise des réseaux protégés par des dispositifs standards tels que par pare-feu, la pseudonymisation, l’encryption et mot de passe. </p>
                 
                <p>
                Lors du traitement des Données Personnelles, <a href="https://www.tall.fr">https://www.tall.fr</a>prend toutes les mesures raisonnables visant à les protéger contre toute perte, utilisation détournée, accès non autorisé, divulgation, altération ou destruction.</p>
                 
                <h2>9. Liens hypertextes « cookies » et balises (“tags”) internet</h2>
                <p>
                Le site <a href="https://www.tall.fr">https://www.tall.fr</a> contient un certain nombre de liens hypertextes vers d’autres sites, mis en place avec l’autorisation de <a href="https://www.tall.fr">https://www.tall.fr</a>. Cependant, <a href="https://www.tall.fr">https://www.tall.fr</a> n’a pas la possibilité de vérifier le contenu des sites ainsi visités, et n’assumera en conséquence aucune responsabilité de ce fait.</p>
                Sauf si vous décidez de désactiver les cookies, vous acceptez que le site puisse les utiliser. Vous pouvez à tout moment désactiver ces cookies et ce gratuitement à partir des possibilités de désactivation qui vous sont offertes et rappelées ci-après, sachant que cela peut réduire ou empêcher l’accessibilité à tout ou partie des Services proposés par le site.
                <p></p>

                <h3>9.1. « COOKIES »</h3>
                 <p>
                Un « cookie » est un petit fichier d’information envoyé sur le navigateur de l’Utilisateur et enregistré au sein du terminal de l’Utilisateur (ex : ordinateur, smartphone), (ci-après « Cookies »). Ce fichier comprend des informations telles que le nom de domaine de l’Utilisateur, le fournisseur d’accès Internet de l’Utilisateur, le système d’exploitation de l’Utilisateur, ainsi que la date et l’heure d’accès. Les Cookies ne risquent en aucun cas d’endommager le terminal de l’Utilisateur.</p>
                 <p>
                <a href="https://www.tall.fr">https://www.tall.fr</a> est susceptible de traiter les informations de l’Utilisateur concernant sa visite du Site, telles que les pages consultées, les recherches effectuées. Ces informations permettent à <a href="https://www.tall.fr">https://www.tall.fr</a> d’améliorer le contenu du Site, de la navigation de l’Utilisateur.</p>
                 <p>
                Les Cookies facilitant la navigation et/ou la fourniture des services proposés par le Site, l’Utilisateur peut configurer son navigateur pour qu’il lui permette de décider s’il souhaite ou non les accepter de manière à ce que des Cookies soient enregistrés dans le terminal ou, au contraire, qu’ils soient rejetés, soit systématiquement, soit selon leur émetteur. L’Utilisateur peut également configurer son logiciel de navigation de manière à ce que l’acceptation ou le refus des Cookies lui soient proposés ponctuellement, avant qu’un Cookie soit susceptible d’être enregistré dans son terminal. <a href="https://www.tall.fr">https://www.tall.fr</a> informe l’Utilisateur que, dans ce cas, il se peut que les fonctionnalités de son logiciel de navigation ne soient pas toutes disponibles.</p>
                 <p>
                Si l’Utilisateur refuse l’enregistrement de Cookies dans son terminal ou son navigateur, ou si l’Utilisateur supprime ceux qui y sont enregistrés, l’Utilisateur est informé que sa navigation et son expérience sur le Site peuvent être limitées. Cela pourrait également être le cas lorsque <a href="https://www.tall.fr">https://www.tall.fr</a> ou l’un de ses prestataires ne peut pas reconnaître, à des fins de compatibilité technique, le type de navigateur utilisé par le terminal, les paramètres de langue et d’affichage ou le pays depuis lequel le terminal semble connecté à Internet.</p>
                 <p>
                Le cas échéant, <a href="https://www.tall.fr">https://www.tall.fr</a> décline toute responsabilité pour les conséquences liées au fonctionnement dégradé du Site et des services éventuellement proposés par <a href="https://www.tall.fr">https://www.tall.fr</a>, résultant (i) du refus de Cookies par l’Utilisateur (ii) de l’impossibilité pour <a href="https://www.tall.fr">https://www.tall.fr</a> d’enregistrer ou de consulter les Cookies nécessaires à leur fonctionnement du fait du choix de l’Utilisateur. Pour la gestion des Cookies et des choix de l’Utilisateur, la configuration de chaque navigateur est différente. Elle est décrite dans le menu d’aide du navigateur, qui permettra de savoir de quelle manière l’Utilisateur peut modifier ses souhaits en matière de Cookies.</p>
                 <p>
                À tout moment, l’Utilisateur peut faire le choix d’exprimer et de modifier ses souhaits en matière de Cookies. <a href="https://www.tall.fr">https://www.tall.fr</a> pourra en outre faire appel aux services de prestataires externes pour l’aider à recueillir et traiter les informations décrites dans cette section.</p>
                 <p>
                Enfin, en cliquant sur les icônes dédiées aux réseaux sociaux Twitter, Facebook, Linkedin et Google Plus figurant sur le Site de <a href="https://www.tall.fr">https://www.tall.fr</a> ou dans son application mobile et si l’Utilisateur a accepté le dépôt de cookies en poursuivant sa navigation sur le Site Internet ou l’application mobile de <a href="https://www.tall.fr">https://www.tall.fr</a>, Twitter, Facebook, Linkedin et Google Plus peuvent également déposer des cookies sur vos terminaux (ordinateur, tablette, téléphone portable).</p>
                 <p>
                Ces types de cookies ne sont déposés sur vos terminaux qu’à condition que vous y consentiez, en continuant votre navigation sur le Site Internet ou l’application mobile de <a href="https://www.tall.fr">https://www.tall.fr</a>. À tout moment, l’Utilisateur peut néanmoins revenir sur son consentement à ce que <a href="https://www.tall.fr">https://www.tall.fr</a> dépose ce type de cookies.</p>
                 
                <h3>Article 9.2. BALISES (“TAGS”) INTERNET</h3>
                 

                <p>

                <a href="https://www.tall.fr">https://www.tall.fr</a> peut employer occasionnellement des balises Internet (également appelées « tags », ou balises d’action, GIF à un pixel, GIF transparents, GIF invisibles et GIF un à un) et les déployer par l’intermédiaire d’un partenaire spécialiste d’analyses Web susceptible de se trouver (et donc de stocker les informations correspondantes, y compris l’adresse IP de l’Utilisateur) dans un pays étranger.</p>
                 
                <p>
                Ces balises sont placées à la fois dans les publicités en ligne permettant aux internautes d’accéder au Site, et sur les différentes pages de celui-ci. 
                 </p>
                <p>
                Cette technologie permet à <a href="https://www.tall.fr">https://www.tall.fr</a> d’évaluer les réponses des visiteurs face au Site et l’efficacité de ses actions (par exemple, le nombre de fois où une page est ouverte et les informations consultées), ainsi que l’utilisation de ce Site par l’Utilisateur. </p>
                 <p>
                Le prestataire externe pourra éventuellement recueillir des informations sur les visiteurs du Site et d’autres sites Internet grâce à ces balises, constituer des rapports sur l’activité du Site à l’attention de <a href="https://www.tall.fr">https://www.tall.fr</a>, et fournir d’autres services relatifs à l’utilisation de celui-ci et d’Internet.</p>
                 <p>
                </p><h2>10. Droit applicable et attribution de juridiction.</h2>  
                 <p>
                Tout litige en relation avec l’utilisation du site <a href="https://www.tall.fr">https://www.tall.fr</a> est soumis au droit français. 
                En dehors des cas où la loi ne le permet pas, il est fait attribution exclusive de juridiction aux tribunaux compétents de Tallalala</p>
            </div>
            
            <a href="#cgu_retour" id = "cgu_fin">Vraiment ? Vous avez tout lu ? Bravo vous pouvez revenir au formulaire!</a> 

            
        </div>
        <script src='https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js'></script>
        <script src ="js/jquery_site.js"></script>
    </body>
</html>