<?php
session_start();
// Connexion à la BDD
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

$distance = $_POST['distance'];
$id_cate = $_POST['id_cate'];

$s = $db->prepare("with utilisateur_buffer as
(select p1.*
from utilisateur p1, point p2
where ST_Within(p1.geom, ST_Transform(ST_buffer(ST_Transform(p2.geom,2154),:distance),4326))
and p2.id_point = (SELECT MAX(id_point) FROM point))
select count(*) from utilisateur_buffer where id_cate_1 = :id_cate
or id_cate_2 = :id_cate
or id_cate_3 = :id_cate
or id_cate_4 = :id_cate
or id_cate_5 = :id_cate
;");
$s->execute(['distance'=>$distance,'id_cate'=>$id_cate]);


$t = $db->prepare("select sum(p1.pop)
from bati_bd_topo p1, point p2
where ST_Within(p1.geom, ST_Transform(ST_buffer(ST_Transform(p2.geom,2154),:distance),4326))
and p2.id_point = (SELECT MAX(id_point) FROM point);"
);
$t->execute(['distance'=>$distance]);

$resultBene= $s->fetch(); 
$resultPop = $t->fetch();

$poparrondi = round($resultPop[0]);
$percent = round(($resultBene[0]/$poparrondi) * 100,2) . " %";


?>
<p class = "popbene">Le nombre de potentiels bénévoles est : <?php print($resultBene[0]) ?> </p>
<p class = "popbene">La population totale est de : <?php print($poparrondi) ?> </p>
<p class = "popbene">Soit un pourcentage de : <?php print($percent) ?> </p>
<?php
?>
