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
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - SpendWise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container bg-white p-4 shadow rounded">
        <h1>Dashboard Administrateur</h1>
        <hr>
        <div class="alert alert-success">
            Dépenses totales sur la plateforme : <strong><?= number_format($total_global, 2) ?> €</strong>
        </div>

        <h3>Liste des comptes utilisateurs</h3>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Login</th>
                    <th>Rôle</th>
                    <th>Dépenses enregistrées</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($u = $users_res->fetch_assoc()): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['login']) ?></td>
                    <td><span class="badge bg-info"><?= $u['role'] ?></span></td>
                    <td><?= $u['nb_depenses'] ?></td>
                    <td>
                        <?php if($u['id'] != $_SESSION['user_id']): ?>
                            <a href="delete_user.php?id=<?= $u['id'] ?>" class="btn btn-danger btn-sm">Bannir</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-secondary">Retour à l'application</a>
    </div>
</body>
</html>