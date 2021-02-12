<?php
// Connexion à la BDD
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

$lat = $_POST['lat'];
$lng = $_POST['lng'];

$query = "INSERT INTO point (geom) VALUES (ST_SetSRID( ST_Point( " . strval($lng) . ", " . strval($lat) . "), 4326));";

$db -> exec($query);

echo ('coordonnées ajoutées');