<?php
// Connexion à la BDD
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

//Préparation de la requête :
$q = $db->prepare("    
    SELECT json_build_object (
        'type', 'FeatureCollection',
        'features', json_agg(ST_AsGeoJSON(ambassadeur_mdp.*)::json)
    ) as ambassadeur
    from ambassadeur_mdp;"
);

//exécution de la requête 
$q->execute();

//récupération du résultat de la requête dans une variable :
$result = $q->fetch();

//Renvoi du résultat : nous extrayons la première ligne du tableau (même si il n'y en a qu'une en réalité)
echo ($result['ambassadeur']);
?>