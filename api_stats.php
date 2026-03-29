<?php
// Je déclare que ce fichier renvoie du JSON et non du HTML
header('Content-Type: application/json');
require_once 'db.php';
session_start();

// Sécurité : Seul un utilisateur connecté peut interroger mon API
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Accès non autorisé"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// J'ai conçu cette requête pour regrouper les dépenses par catégorie
// C'est parfait pour alimenter un diagramme circulaire
$sql = "SELECT categorie, SUM(montant) as total FROM depenses WHERE user_id = ? GROUP BY categorie";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

