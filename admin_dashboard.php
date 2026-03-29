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