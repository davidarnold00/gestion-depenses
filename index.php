<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
include 'db.php';

/** 1. RÉCUPÉRATION DES DONNÉES POUR LE TABLEAU **/
$result = $conn->query("SELECT * FROM depenses ORDER BY date_depense DESC");

/** 2. CALCUL DU TOTAL GÉNÉRAL **/
$total_query = $conn->query("SELECT SUM(Montant) as total FROM depenses");
$total_general = $total_query->fetch_assoc()['total'] ?? 0;

/** 3. RÉCUPÉRATION DES STATS POUR LA LISTE ET LE GRAPHIQUE **/
$sql_stats = "SELECT categorie, SUM(Montant) as somme FROM depenses GROUP BY categorie";
$stats_result = $conn->query($sql_stats);

$categories = [];
$montants = [];
$stats_rows = [];

while($row = $stats_result->fetch_assoc()) {
    $categories[] = $row['categorie']; 
