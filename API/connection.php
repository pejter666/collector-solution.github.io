<?php
$host = "localhost";
$dbname = "id19271996_wp_fe8207ec390e9f7f978acb1e80522c02";
$username = "id19271996_wp_fe8207ec390e9f7f978acb1e80522c02";
$password = "5}Ea$mMQGE/t661}";
try{
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
}catch(Exception $e){
    die("Fatal error: ".$e->getMessage());
}
?>
