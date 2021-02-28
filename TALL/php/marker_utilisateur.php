<?php
// Connexion à la BDD
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

//Préparation de la requête :
$q = $db->prepare("SELECT json_build_object(
    'type', 'FeatureCollection',
    'features', json_agg(ST_AsGeoJSON(utilisateur.*)::json)
    ) as utilisateur
from utilisateur;"
);

$q->execute();
//récupération du résultat de la requête dans une variable :
$result= $q->fetch(); 
//Envoi du tableau
echo ($result['utilisateur']);
?>