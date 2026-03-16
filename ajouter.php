<?php
session_start();
// Sécurité : on vérifie que l'utilisateur est bien connecté avant d'ajouter
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// On vérifie que les champs nécessaires sont présents et non vides
if (isset($_POST['montant'], $_POST['categorie'], $_POST['description']) && 
    !empty($_POST['montant']) && !empty($_POST['categorie']) && !empty($_POST['description'])) {

    $montant = $_POST['montant'];
    $categorie = $_POST['categorie'];
    $description = $_POST['description'];

    // Note : on ne passe plus la date ici, la BDD mettra la date/heure actuelle
    $stmt = $conn->prepare("INSERT INTO depenses (montant, categorie, description) VALUES (?, ?, ?)");
    
    // "dss" signifie : double (nombre décimal), string, string
    $stmt->bind_param("dss", $montant, $categorie, $description);
    
    if ($stmt->execute()) {
        // Succès : on peut ajouter un message en session si on veut (optionnel)
        $_SESSION['message'] = "Dépense ajoutée avec succès !";
    }
    
    $stmt->close();
}

// Redirection vers l'accueil pour voir le résultat
header("Location: index.php");
exit();
?>