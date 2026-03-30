<?php
session_start();
include 'db.php';

// 1. SÉCURITÉ : Seul l'admin peut supprimer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 2. RÉCUPÉRATION de l'ID de l'utilisateur à supprimer
if (isset($_GET['id'])) {
    $id_a_supprimer = $_GET['id'];

    // EMPÊCHER l'admin de se supprimer lui-même par erreur
    if ($id_a_supprimer == $_SESSION['user_id']) {
        header("Location: index.php?error=auto_delete");
        exit();
    }

    // 3. SUPPRESSION EN CASCADE (Optionnel mais recommandé)
    // On commence par supprimer toutes les dépenses de cet utilisateur
    $stmt1 = $conn->prepare("DELETE FROM depenses WHERE user_id = ?");
    $stmt1->bind_param("i", $id_a_supprimer);
    $stmt1->execute();

    // 4. SUPPRESSION de l'utilisateur lui-même
    $stmt2 = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt2->bind_param("i", $id_a_supprimer);

    if ($stmt2->execute()) {
        // Succès ! On retourne à l'accueil
        header("Location: index.php?msg=user_deleted");
    } else {
        echo "Erreur lors de la suppression : " . $conn->error;
    }
} else {
    header("Location: index.php");
}
?>