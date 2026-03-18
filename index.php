<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
include 'db.php';

/** 1. RÉCUPÉRATION DES DONNÉES POUR LE TABLEAU **/
$result = $conn->query("SELECT * FROM depenses ORDER BY date_depense DESC");

