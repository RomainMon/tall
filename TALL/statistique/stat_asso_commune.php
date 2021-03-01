<?php
// connexion à la db
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

// création d'une fonction qui renvoi du texte
function retourCateCount($nom_cate, $count,$id_cate) {
    return "{$nom_cate}:{$count}:{$id_cate}";
}

$commune = $_POST["commune"];

$q = $db->prepare("with
asso_id as (select association.*, categorie.nom_cate from association join categorie on (association.id_cate = categorie.id_cate)),
asso_com as (SELECT asso_id.*, commune.nom_com, commune.code_post from asso_id join commune on (asso_id.adrs_codei = commune.insee_com)
where nom_com = :nom_com),
asso_count as (SELECT nom_cate, count(*) from asso_com group by nom_cate)
SELECT asso_count.nom_cate, asso_count.count, id_cate from asso_count left join categorie on(asso_count.nom_cate = categorie.nom_cate);"
);
$q->execute(['nom_com'=>$commune]);


// appel du résultat avec la fonction de création de texte
$liste_asso = $q->fetchAll(PDO::FETCH_FUNC,"retourCateCount");

foreach ($liste_asso as $value){
    ?>
    <p hidden class="nom_cate"  ><?php echo($value)?></p>
        
<?php
}
?>
