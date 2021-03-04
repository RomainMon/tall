<?php
session_start();
// Connexion à la BDD
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

$id_util = $_SESSION['id_utilisateur'];
// $temps = $_POST["temps"];

// $distance = 5000 * ($temps/60);

//Préparation de la requête :
$q = $db->prepare("with equip_com as(
    SELECT equipement.*, commune.nom_com, commune.code_post from equipement join commune on (equipement.insee_com = commune.insee_com)),
	equip_dist as (
	SELECT *, ST_Distance(ST_Transform(geom,2154),ST_Transform((select utilisateur.geom from utilisateur where id_utilisateur = :id_utilisateur), 2154))
	FROM equip_com)
    SELECT json_build_object (
        'type', 'FeatureCollection',
        'features', json_agg(ST_AsGeoJSON(equip_dist.*)::json)
    ) as distance
    from equip_dist;"
);

//exécution de la requête 
$q->execute(['id_utilisateur'=>$id_util]);

//récupération du résultat de la requête dans une variable :
$result = $q->fetch();

//Renvoi du résultat : nous extrayons la première ligne du tableau (même si il n'y en a qu'une en réalité)
echo ($result['distance']);
?>