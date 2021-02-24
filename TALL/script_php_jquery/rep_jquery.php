<?php
// connexion à la db
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;
?>
<?php
// recuperation des variables issues de la requete jQuery
$rue = $_POST["rue"];
$commune = $_POST["commune"];
$numero = $_POST["numero"];
// preparation de la requete
$q = $db->prepare("SELECT rep FROM vue_adresse where nom_1 = :rue and nom_com = :commune and numero = :numero ORDER by rep;");                   
$q->execute(['rue'=>$rue,'commune'=>$commune,'numero'=>$numero]);
//récupération du résultat de la requête dans une variable, d'abord pour compter puis liste des éléments:
$nbRep = $q->rowCount();
$liste_repere= $q->fetchAll();
//si 1 seul élément cela veut dire qu'il n' y a pas de reperes à cette adresse donc on l'affiche:
if ($nbRep == 1){    
        ?>
        <option value="">pas de repères à cette adresse</option>
        <br>
        <?php   
}
// sinon on recupere le repere
else {
        foreach($liste_repere as $value){
    ?>
    <option value="<?php print($value[0]); ?>"><?php print($value[0]); ?></option><br>
    <?php
    }
}
?> 