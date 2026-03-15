<?php
// Affiche toutes les erreurs MySQLi pour faciliter le debug
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$user = "root";
$password = "";
$dbname = "gestion_depenses";

$conn = new mysqli($host, $user, $password, $dbname);
$conn->set_charset("utf8"); // pour gérer correctement les accents

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
?>