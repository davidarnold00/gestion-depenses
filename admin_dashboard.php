<?php
session_start() ;
require_once 'db.php' ;
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Redirige les simples utilisateurs
    exit();
}
