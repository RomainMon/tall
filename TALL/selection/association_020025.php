<?php session_start();

// Connexion à la BDD
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;


//Préparation de la requête :
$q = $db->prepare("with 
                asso_id as (
                select association.*, categorie.nom_cate from association join categorie on (association.id_cate = categorie.id_cate)
                where association.id_cate = '020025'),
                asso_com as (   
                SELECT asso_id.*, commune.nom_com, commune.code_post from asso_id join commune on (asso_id.adrs_codei = commune.insee_com)    
                )    
                SELECT json_build_object (
                    'type', 'FeatureCollection',
                    'features', json_agg(ST_AsGeoJSON(asso_com.*)::json)
                ) as association
                from asso_com;"
);

//exécution de la requête 
$q->execute();

//récupération du résultat de la requête dans une variable :
$result = $q->fetch();

//Renvoi du résultat : nous extrayons la première ligne du tableau (même si il n'y en a qu'une en réalité)
echo ($result['association']);
?>
