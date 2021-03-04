<?php
session_start();
// connexion à la db
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

$w = $db -> prepare("select st_AsGeojson((st_dump(st_linemerge(st_union(geom)))).geom) as itineraire from test;");
$w->execute();
$geom_iti = $w->fetch();

echo($geom_iti['itineraire']);

?>