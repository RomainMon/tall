<?php
// Connexion à la BDD
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

//Préparation de la requête :
$q = $db->prepare("SELECT * ,st_AsGeojson(ST_Transform(geom, 4326)) as geojson from commune;");
$q->execute();
//récupération du résultat de la requête dans une variable :
$result= $q->fetchAll(); 
//Envoi du tableau
echo json_encode($result);
?>


