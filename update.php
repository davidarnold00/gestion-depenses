<?php
session_start();
include 'db.php';

if(isset($_POST['id'], $_POST['montant'], $_POST['categorie'], $_POST['description'], $_POST['date'])) {

    $id = (int) $_POST['id'];
    $montant = $_POST['montant'];
    $categorie = $_POST['categorie'];
    $description = $_POST['description'];
    $date = $_POST['date']; // Format récupéré du datetime-local

    $stmt = $conn->prepare("UPDATE depenses SET montant=?, categorie=?, description=?, date_depense=? WHERE id=?");
    $stmt->bind_param("dsssi", $montant, $categorie, $description, $date, $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: index.php");
exit();
?>