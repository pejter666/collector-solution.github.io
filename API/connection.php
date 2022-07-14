<?php
$host = "localhost";
$dbname = "id19271996_wp_c5979a5ab21bebeeb192f9a1e69929c0";
$username = "id19271996_wp_c5979a5ab21bebeeb192f9a1e69929c0";
$password = "62=[Npsi3?A&G9~c";
try{
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
}catch(Exception $e){
    die("Fatal error: ".$e->getMessage());
}
?>
