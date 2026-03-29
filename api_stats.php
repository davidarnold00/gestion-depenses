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

