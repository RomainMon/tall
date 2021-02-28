<?php
// connexion à la db
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

// création d'une fonction qui renvoi du texte
function retourEquipCount($type_cate, $count) {
    return "{$type_cate}:{$count}";
}

$commune = $_POST["commune"];

$q = $db->prepare("with
equip_com as (SELECT equipement.*, commune.nom_com, commune.code_post from equipement join commune on (equipement.insee_com = commune.insee_com)
WHERE nom_com = :nom_com)
SELECT type_equip, count(*) from equip_com group by type_equip;"
);
$q->execute(['nom_com'=>$commune]);

// appel du résultat avec la fonction de création de texte
$liste_equip = $q->fetchAll(PDO::FETCH_FUNC,"retourEquipCount");

foreach ($liste_equip as $value){
    ?>
    <p class="nom_cate" hidden ><?php echo($value)?></p>
    <?php
};
?>
