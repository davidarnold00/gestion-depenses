<?php
session_start() ;
require_once 'db.php' ;
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Redirige les simples utilisateurs
    exit();
}
// je calcule la somme de TOUTES les dépenses de TOUS les utilisateurs dans ma db
$total_res = $conn->query("SELECT SUM(montant) as total_global FROM depenses");
$total_global = $total_res->fetch_assoc()['total_global'];

// J'ai fait une jointure pour lister les utilisateurs et leur nombre de dépenses respectif
$users_res = $conn->query("SELECT u.id, u.login, u.role, COUNT(d.id) as nb_depenses FROM utilisateurs u LEFT JOIN depenses d ON u.id = d.user_id GROUP BY u.id");
?>
