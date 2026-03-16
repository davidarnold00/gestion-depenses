<?php
include 'db.php';

$login = "admin";
$pass_clair = "1234"; 

// On hache le mot de passe (sécurité BTS SIO obligatoire)
$pass_hache = password_hash($pass_clair, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO utilisateurs (login, password) VALUES (?, ?)");
$stmt->bind_param("ss", $login, $pass_hache);

if($stmt->execute()) {
    echo "Compte créé avec succès !<br>";
    echo "Identifiant : <b>admin</b><br>";
    echo "Mot de passe : <b>1234</b>";
} else {
    echo "Erreur : " . $conn->error;
}
?>