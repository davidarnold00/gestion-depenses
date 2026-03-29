<?php
session_start();
require_once 'db.php';

// Je vérifie d'abord que l'utilisateur est bien connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['montant'], $_POST['categorie'], $_POST['description'])) {
    $montant = $_POST['montant'];
    $categorie = $_POST['categorie'];
    $description = $_POST['description'];
    
    // J'identifie l'auteur de la dépense via sa session
    $user_id = $_SESSION['user_id']; 

    // J'insère les données en utilisant les noms de colonnes normalisés (minuscules)
    $stmt = $conn->prepare("INSERT INTO depenses (montant, categorie, description, user_id) VALUES (?, ?, ?, ?)");
    
    // "dssi" : double pour montant, string pour catégorie/description, int pour user_id
    $stmt->bind_param("dssi", $montant, $categorie, $description, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Dépense enregistrée avec succès !";
    }
    $stmt->close();
}

header("Location: index.php");
exit();
?>