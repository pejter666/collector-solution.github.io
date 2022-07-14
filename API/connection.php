<?php
$host = "localhost";
$dbname = "id19271996_wp_da14abae1749e302c784f2418a290356";
$username = "id19271996_wp_da14abae1749e302c784f2418a290356";
$password = "H!))=Gi%uuABpt2q";
try{
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
}catch(Exception $e){
    die("Fatal error: ".$e->getMessage());
}
?>
