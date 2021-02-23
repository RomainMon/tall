<?php
// connexion à la db
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;
?>
<?php
$commune = $_POST["commune"];
$q = $db->prepare("SELECT distinct(nom_1) FROM vue_adresse where nom_com = :commune ORDER by nom_1;");                   
$q->execute(['commune'=>$commune]);
//récupération du résultat de la requête dans une variable :
$liste_rue= $q->fetchAll();

// Iterating through the product array
foreach($liste_rue as $value){
?>
<option value="<?php print($value[0]); ?>"><?php print($value[0]); ?></option>
<br>
<?php
}
?>

