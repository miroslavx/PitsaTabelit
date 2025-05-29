<?php
// Andmebaasi ühenduse andmed
$db_host = "d133840.mysql.zonevs.eu";
$db_user = "d133840_burdyga"; 
$db_pass = "Baikal23051982";
$db_name = "d133840_pizzeriadb"; 

// Loob ühenduse MySQL andmebaasiga
$yhendus = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Kontrollib ühenduse õnnestumist
if ($yhendus->connect_error) {
    die("Ühenduse viga: " . $yhendus->connect_error);
}

// Määrab UTF-8 kodeeringu
$yhendus->set_charset("utf8mb4");
?>