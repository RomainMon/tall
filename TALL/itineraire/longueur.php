<?php

// session_start();
// // connexion à la db
include '../include/database.php';
// //  recupération de la varibable db pour faire des requêtes
global $db;

$x = $db -> prepare("select st_length(st_transform((st_dump(st_linemerge(st_union(geom)))).geom,2154))from test;");
$x->execute();
$longueur_iti = $x->fetch();

$longueur = round($longueur_iti[0]) . " mètres";

if (round($longueur_iti[0] * 0.012) < 60){        
    $temps = round($longueur_iti[0] * 0.012) . " minutes";
}
else{
    $temps = floor($longueur_iti[0] * 0.012 /60) . " heure " . round($longueur_iti[0] * 0.012)%60 . " minutes";
}

$result_iti = array("longueur"=>$longueur,"temps"=>$temps);


?>
<p class = "poulpy">La distance est de : <?php print($result_iti["longueur"]) ?> </p>
<p class = "poulpy">La durée est de : <?php print($result_iti["temps"]) ?> </p>
<?php
?>