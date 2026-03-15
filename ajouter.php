<?php
include 'db.php';

// Vérification des champs obligatoires
if(isset($_POST['montant'], $_POST['categorie'], $_POST['description'], $_POST['date']) 
   && !empty($_POST['montant']) && !empty($_POST['categorie']) && !empty($_POST['description']) && !empty($_POST['date'])) {

    $montant = $_POST['montant'];
    $categorie = $_POST['categorie'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    // Prepared statement pour éviter les injections SQL
    $stmt = $conn->prepare("INSERT INTO depenses (montant, categorie, description, date_depense) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("dsss", $montant, $categorie, $description, $date);
    $stmt->execute();
    $stmt->close();
}

header("Location: index.php");
exit();
?>