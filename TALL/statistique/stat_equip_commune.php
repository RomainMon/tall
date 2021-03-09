<?php
// connexion à la db
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

// création d'une fonction qui renvoi du texte
function retourEquipCount($id_type_equip, $count,$type_equip) {
    return "{$id_type_equip}:{$count}:{$type_equip}";
}

$commune = $_POST["commune"];

$q = $db->prepare("with
equip_com as (SELECT equipement.*, commune.nom_com, commune.code_post from equipement join commune on (equipement.insee_com = commune.insee_com)
WHERE nom_com = :nom_com)
SELECT type_equip, count(*),id_type_equip from equip_com group by id_type_equip,type_equip
;"
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
