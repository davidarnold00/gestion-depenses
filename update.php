<?php
include 'db.php';

if(isset($_POST['id'], $_POST['montant'], $_POST['categorie'], $_POST['description'], $_POST['date']) &&
   !empty($_POST['montant']) && !empty($_POST['categorie']) && !empty($_POST['description']) && !empty($_POST['date'])) {

    $id = (int) $_POST['id'];
    $montant = $_POST['montant'];
    $categorie = $_POST['categorie'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("UPDATE depenses SET montant=?, categorie=?, description=?, date_depense=? WHERE id=?");
    $stmt->bind_param("dsssi", $montant, $categorie, $description, $date, $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: index.php");
exit();
?>