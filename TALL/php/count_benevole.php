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


$t = $db->prepare("with utilisateur_buffer as
(select p1.*
from utilisateur p1, point p2
where ST_Within(p1.geom, ST_Transform(ST_buffer(ST_Transform(p2.geom,2154),:distance),4326))
and p2.id_point = (SELECT MAX(id_point) FROM point)),
utilisateur_buffer_cate as (
select * from utilisateur_buffer where id_cate_1 = :id_cate
or id_cate_2 = :id_cate
or id_cate_3 = :id_cate
or id_cate_4 = :id_cate
or id_cate_5 = :id_cate)
select count(benevole) from utilisateur_buffer_cate where benevole ='oui'
;");
$t->execute(['distance'=>$distance,'id_cate'=>$id_cate]);

$u = $db->prepare("with utilisateur_buffer as
(select p1.*
from utilisateur p1, point p2
where ST_Within(p1.geom, ST_Transform(ST_buffer(ST_Transform(p2.geom,2154),:distance),4326))
and p2.id_point = (SELECT MAX(id_point) FROM point)),
utilisateur_buffer_cate as (
select * from utilisateur_buffer where id_cate_1 = :id_cate
or id_cate_2 = :id_cate
or id_cate_3 = :id_cate
or id_cate_4 = :id_cate
or id_cate_5 = :id_cate)
select count(contact_asso) from utilisateur_buffer_cate where contact_asso ='oui'
;");
$u->execute(['distance'=>$distance,'id_cate'=>$id_cate]);

$resultInteret= $s->fetch(); 
$resultBene = $t->fetch();
$resultContact = $u->fetch();



?>
<p class = "popInteret">Le nombre de particuliers intéressés par votre domaine est de : <?php print($resultInteret[0]) ?> </p>
<p class = "popBene">Le nombre de bénévoles potentiels est de : <?php print($resultBene[0]) ?> </p>
<p class = "popContact"><?php print($resultContact[0]) ?> particuliers souhaitent être contacté(s)</p>
<?php
?>
