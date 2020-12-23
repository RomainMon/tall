<?php
// Connexion à la BDD
$dbconn = pg_connect("host=localhost port=5432 dbname=asso_test_2 user=postgres password=Romainduris")
or die('Could not connect: '. preg_last_error());
//Préparation de la requête :
$query = "with equip_com as(
    SELECT equip_4326.*, commune.nom_com, commune.code_post from equip_4326 join commune on (equip_4326.insee_com = commune.insee_com))    
    SELECT json_build_object(
        'type', 'FeatureCollection',
        'features', json_agg(ST_AsGeoJSON(equip_com.*)::json)
    ) 
    from equip_com;";

//récupération du résultat de la requête dans une variable :
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

//Renvoi du résultat : nous extrayons la première ligne du tableau (même si il n'y en a qu'une en réalité)
echo pg_fetch_result($result, 0, 0);

?>