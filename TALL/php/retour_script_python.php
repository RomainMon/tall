<?php session_start();
// connexion à la db
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

$q = $db->prepare("with resultat as(
    SELECT result_amc.* from result_amc)
    SELECT json_build_object (
        'type', 'FeatureCollection',
        'features', json_agg(ST_AsGeoJSON(resultat.*)::json)
    ) as resultat
    from resultat;"
);

$q->execute();

$resultat= $q->fetch();

echo $resultat['resultat'];

?>

