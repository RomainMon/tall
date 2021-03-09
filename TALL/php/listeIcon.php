<?php
// Connexion à la BDD
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;



$q = $db->prepare("SELECT distinct(id_type_equip) FROM equipement ORDER by id_type_equip;");
$q->execute();
//récupération du résultat de la requête dans une variable :
$liste_equip= $q->fetchAll();

foreach($liste_equip as $value){ 
                  


$q = $db->prepare("SELECT * FROM CATEGORIE ORDER by id_cate;");
$q->execute();
//récupération du résultat de la requête dans une variable :
$liste_cate= $q->fetchAll();

foreach($liste_cate as $value){ 

?>