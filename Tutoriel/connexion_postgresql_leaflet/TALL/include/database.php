<?php
// paramètrage de la connexion vers la DB
    $host = 'localhost';
    $dbname = 'TALL';
    $username = 'postgres';
    $password = 'Romainduris';
    $port = '5432';

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$username;password=$password";
    
// je teste la connexion et je renvoie un message d'erreur en cas d'erreur de connexion  
    try{
        $db = new PDO($dsn);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Connecté à $dbname avec succès!";

        } catch (PDOException $e){
        echo $e->getMessage();
    }
?>