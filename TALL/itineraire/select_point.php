<?php
session_start();
// connexion à la db
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

$lat = $_POST['lat'];
$lng = $_POST['lng'];
$pt_arrivee = "POINT (" . $lng . " " . $lat . ")";
$id_util = $_SESSION['id_utilisateur'];

// echo ($pt_arrivee);

$q = $db->prepare("SELECT st_AsText(geom) FROM utilisateur WHERE id_utilisateur = :id_utilisateur;");                   
$q->execute(['id_utilisateur'=>$id_util]);
$pt_depart = $q->fetch();
// echo ($pt_depart[0]);


$p = $db->prepare("DROP VIEW IF EXISTS test;");
$p -> execute();

$u = $db-> prepare("SELECT node_id FROM noeud_reseau_lyon
WHERE ST_Expand(ST_GeomFromText(:pt_depart,4326),1000)&&geom
ORDER BY ST_Distance(geom,ST_GeomFromText(:pt_depart,4326))LIMIT 1;");
$u->execute(['pt_depart'=> $pt_depart[0]]);
$pt_dep = $u->fetch();
$ptdep = $pt_dep[0];

$v = $db-> prepare("SELECT node_id FROM noeud_reseau_lyon
WHERE ST_Expand(ST_GeomFromText(:pt_arrivee,4326),1000)&&geom
ORDER BY ST_Distance(geom,ST_GeomFromText(:pt_arrivee,4326))LIMIT 1;");
$v->execute(['pt_arrivee'=> $pt_arrivee]);
$pt_arr = $v->fetch();


$text_sql = "'select id_route as id,
start_node as  source,
end_node as target,
longueur as cost from public.reseau_lyon'";
// echo($text_sql);

// ['textsql' => $text_sql, 'pt_dep' => $pt_dep[0], 'pt_arr' => $pt_arr[0]]

$i = $db->prepare("create view test as
WITH dijkstra AS (SELECT * FROM pgr_dijkstra($text_sql,
$pt_dep[0],
$pt_arr[0],
false
))
select id_route as id, geom
from reseau_lyon cross join dijkstra
where id_route = dijkstra.edge;");
$i->execute();

?>