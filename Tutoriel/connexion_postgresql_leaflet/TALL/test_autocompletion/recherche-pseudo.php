<?php
$term = $_GET[ "term" ];//les 2 premiers caractères saisis dans l'input du form
//connexion à la bp avec PDO
//extraire tous les pseudos commençant par les caractères $_GET[ "term" ]
// connexion à la db
include '../include/database.php';
//  recupération de la varibable db pour faire des requêtes
global $db;

$getPseudoQuery = $db->query("SELECT titre FROM association WHERE titre LIKE '$term%'");
$tblPseudos = array();
while($row=$getPseudoQuery ->fetch() ){
//la clé value correspond à la valeur qui peuplera l'input du form et la clé label correspond à la valeur affichée dans la liste des suggestions
  $tblPseudos[] = array( "label"=> $row['titre'], "value"=>$row['titre'] );
}
//on souhaite obtenir du json selon le modèle suivant: [{"label":"toto","value":"toto"}, {"label":"tata","value":"tata"}]
echo json_encode($tblPseudos); 