<?php
include 'db.php';

// Vérifie que l'id est passé et est un nombre
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $id = (int) $_GET['id'];

    // Prepared statement pour supprimer la dépense
    $stmt = $conn->prepare("DELETE FROM depenses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: index.php");
exit();
?>