<?php
// Connexion à la BDD
$dbconn = pg_connect("host=localhost port=5432 dbname=asso_test_2 user=postgres password=Romainduris")
or die('Could not connect: '. preg_last_error());
//Préparation de la requête :
$query = "SELECT * ,st_AsGeojson(ST_Transform(geom, 4326)) as geojson from commune;";
//récupération du résultat de la requête dans une variable :
$result = pg_query($query) or die('Query failed: ' . pg_last_error());
//Traitement de la requête pour récupérer un tableau
$array = pg_fetch_all($result);
//Envoi du tableau
echo json_encode($array);
?>