<?php
session_start();
include 'db.php';

// 1. SÉCURITÉ : Accès réservé à l'admin connecté
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 2. VÉRIFICATION : On vérifie qu'un ID est bien passé dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$target_user_id = $_GET['id'];

// 3. RÉCUPÉRATION DES INFOS DE L'UTILISATEUR CIBLÉ
$user_stmt = $conn->prepare("SELECT login, role FROM utilisateurs WHERE id = ?");
$user_stmt->bind_param("i", $target_user_id);
$user_stmt->execute();
$user_info = $user_stmt->get_result()->fetch_assoc();

if (!$user_info) {
    die("Erreur : Utilisateur introuvable dans la base de données.");
}

// 4. RÉCUPÉRATION DES DÉPENSES DE CET UTILISATEUR
$dep_stmt = $conn->prepare("SELECT * FROM depenses WHERE user_id = ? ORDER BY date_depense DESC");
$dep_stmt->bind_param("i", $target_user_id);
$dep_stmt->execute();
$result = $dep_stmt->get_result();

// 5. CALCUL DU TOTAL PERSONNEL
$total_stmt = $conn->prepare("SELECT SUM(montant) as total FROM depenses WHERE user_id = ?");
$total_stmt->bind_param("i", $target_user_id);
$total_stmt->execute();
$total_user = $total_stmt->get_result()->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails : <?= htmlspecialchars($user_info['login']) ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="user-header text-center">
    <div class="container">
        <div class="mb-2">
            <i class="bi bi-person-vcard fs-1"></i>
        </div>
        <h1 class="display-6 fw-bold text-uppercase"><?= htmlspecialchars($user_info['login']) ?></h1>
        <p class="badge bg-light text-primary px-3 py-2">Rôle : <?= strtoupper($user_info['role']) ?></p>
        <div class="mt-3">
            <a href="index.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-house-door me-1"></i> Retour au Dashboard
            </a>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row justify-content-center">
        
        <div class="col-md-5 mb-4">
            <div class="card stats-card p-4 text-center border-0 shadow-sm">
                <span class="text-muted small fw-bold">TOTAL DES FRAIS RÉUNIS</span>
                <div class="display-5 fw-bold text-primary my-2">
                    <?= number_format($total_user, 2, ',', ' ') ?> €
                </div>
                <div class="text-secondary small">
                    <i class="bi bi-calculator me-1"></i> Basé sur <?= $result->num_rows ?> dépense(s)
                </div>
            </div>
        </div>

        <div class="col-lg-10">
            <div class="table-container border-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted">DATE</th>
                            <th class="py-3">DESCRIPTION</th>
                            <th class="py-3">CATÉGORIE</th>
                            <th class="text-end pe-4 py-3">MONTANT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4 text-muted small">
                                    <?= date('d/m/Y', strtotime($row['date_depense'])) ?>
                                </td>
                                <td class="fw-bold"><?= htmlspecialchars($row['description']) ?></td>
                                <td>
                                    <span class="badge-cat bg-info-subtle text-info border border-info-subtle">
                                        <?= htmlspecialchars($row['categorie']) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4 fw-bold">
                                    <?= number_format($row['montant'], 2, ',', ' ') ?> €
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted italic">
                                    <i class="bi bi-info-circle me-2"></i> Cet utilisateur n'a saisi aucune dépense.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

</body>
</html>