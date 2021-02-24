<?php
// connexion à la db
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;
?>
<?php
$rue = $_POST["rue"];
$commune = $_POST["commune"];
$q = $db->prepare("SELECT distinct(numero) FROM vue_adresse where nom_1 = :rue and nom_com = :commune ORDER by numero;");                   
$q->execute(['rue'=>$rue,'commune'=>$commune]);
//récupération du résultat de la requête dans une variable :
$liste_numero= $q->fetchAll();

// Iterating through the product array
foreach($liste_numero as $value){
?>
<option value="<?php print($value[0]); ?>"><?php print($value[0]); ?></option>
<br>
<?php
}
?>

