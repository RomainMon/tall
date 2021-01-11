<?php
// Connexion à la BDD
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

//Préparation de la requête :
$q = $db->prepare("with equip_com as(
    SELECT equipement.*, commune.nom_com, commune.code_post from equipement join commune on (equipement.insee_com = commune.insee_com))    
    SELECT json_build_object (
        'type', 'FeatureCollection',
        'features', json_agg(ST_AsGeoJSON(equip_com.*)::json)
    ) as equipement
    from equip_com;"
);

//exécution de la requête 
$q->execute();

//récupération du résultat de la requête dans une variable :
$result = $q->fetch();

//Renvoi du résultat : nous extrayons la première ligne du tableau (même si il n'y en a qu'une en réalité)
echo ($result['equipement']);
?>
