<?php
// Connexion à la BDD
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

$lat = $_POST['lat'];
$lng = $_POST['lng'];
$point = "'POINT (" . $lng . " " . $lat . ")'";
$distance = $_POST['distance'];
// $id_cate = $_POST['id_cate'];

// echo ($point)

// ST_GeomFromText('POINT(-71.064544 42.28787)')
// $q =$db->prepare("drop view if exists point;");
// $q->execute();

$r =$db->prepare("INSERT INTO point(geom) values (ST_GeomFromText($point , 4326));");
$r->execute();



$s = $db->prepare("with buffer as
(SELECT ST_Transform(ST_buffer(ST_Transform(geom,2154), $distance),4326) from point where id_point = (SELECT MAX(id_point) FROM point))
SELECT json_build_object(
    'type', 'FeatureCollection',
    'features', json_agg(ST_AsGeoJSON(buffer.*)::json)
    ) as buffer
from buffer;"
);
$s->execute();


$result= $s->fetch(); 
// //Envoi du tableau
echo ($result['buffer']);




?>

