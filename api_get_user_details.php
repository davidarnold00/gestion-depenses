<?php
include 'db.php';
header('Content-Type: application/json'); // On dit au navigateur : "C'est du JSON"

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT login, role FROM utilisateurs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

echo json_encode($user); // On transforme le résultat en format texte JSON
?>