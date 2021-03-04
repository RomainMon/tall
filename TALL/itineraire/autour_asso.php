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
$q = $db->prepare("with 
                asso_id as (
                select association.*, categorie.nom_cate from association join categorie on (association.id_cate = categorie.id_cate)
                ),
                asso_com as (   
                SELECT asso_id.*, commune.nom_com, commune.code_post from asso_id join commune on (asso_id.adrs_codei = commune.insee_com)    
                ),
                asso_dist as (
                SELECT *, ST_Distance(ST_Transform(geom,2154),ST_Transform((select utilisateur.geom from utilisateur where id_utilisateur = :id_utilisateur), 2154))
                FROM asso_com)    
                SELECT json_build_object (
                    'type', 'FeatureCollection',
                    'features', json_agg(ST_AsGeoJSON(asso_dist.*)::json)
                ) as distance
                from asso_dist;"
);

//exécution de la requête 
$q->execute(['id_utilisateur'=>$id_util]);

//récupération du résultat de la requête dans une variable :
$result = $q->fetch();

//Renvoi du résultat : nous extrayons la première ligne du tableau (même si il n'y en a qu'une en réalité)
echo ($result['distance']);
?>