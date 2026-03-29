<?php
// J'active le rapport d'erreurs pour voir immédiatement si ma base de données a un souci
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$user = "root";
$password = "";
$dbname = "gestion_depenses";

// J'initialise la connexion avec MySQL
$conn = new mysqli($host, $user, $password, $dbname);

// Je force l'encodage en UTF-8 pour que les accents (catégories, descriptions) s'affichent bien
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}
?>