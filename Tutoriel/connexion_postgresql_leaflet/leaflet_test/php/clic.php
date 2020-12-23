<?php
try{
    $dbconn = pg_connect('host=localhost dbname=asso_test_2 user=postgres password=Romainduris')
    or die('Could not connect: ' . pg_last_error());
} catch(PDOException $erreur){
    $erreur->getMessage();
    echo $erreur;
}

$lat = $_POST['lat'];
$lng = $_POST['lng'];

$query = "INSERT INTO point (geom) VALUES (ST_SetSRID( ST_Point( " . strval($lng) . ", " . strval($lat) . "), 4326));";

$result = pg_query($query) or die('Query failed: ' . pg_last_error());

echo ($query);