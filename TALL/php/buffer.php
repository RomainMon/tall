<?php
session_start();
// Connexion à la BDD
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

$distance = $_POST['distance'];
$id_util = $_SESSION['id_utilisateur'];


//Préparation de la requête :
$q = $db->prepare("WITH buffer as 
(SELECT id_utilisateur,ST_Transform(St_buffer(ST_Transform(geom,2154), :distance),4326) from utilisateur 
where id_utilisateur = :id_utilisateur)
SELECT json_build_object(
    'type', 'FeatureCollection',
    'features', json_agg(ST_AsGeoJSON(buffer.*)::json)
    ) as buffer
from buffer;"
);

$q->execute(['distance'=>$distance,'id_utilisateur'=>$id_util]);
//récupération du résultat de la requête dans une variable :
$result= $q->fetch(); 
//Envoi du tableau
echo ($result['buffer']);
?>