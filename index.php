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
$montants[] = $row['somme'];
    $stats_rows[] = $row; 
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Budget - SpendWise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-wallet2 me-2"></i>GeeWise</a>
        <div class="navbar-nav ms-auto text-white align-items-center">
            <span class="me-3 small"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user_login']) ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger">Déconnexion</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">Ajouter une dépense</div>
                <div class="card-body">
                    <form action="ajouter.php" method="POST">
                        <div class="mb-2">
                            <input type="number" step="0.01" name="montant" class="form-control" placeholder="Montant (€)" required>
                        </div>
                        <div class="mb-2">
                            <select name="categorie" class="form-select" required>
                                <option value="Alimentation">Alimentation</option>
                                <option value="Transport">Transport</option>
                                <option value="Loisirs">Loisirs</option>
                                <option value="Santé">Santé</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="description" class="form-control" placeholder="Description" required>
                        </div>
                        <button type="submit" class="btn btn-add-expense text-white w-100">Ajouter la dépense</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Répartition des frais</div>
                <div class="card-body">
                    <div class="mb-4">
                        <canvas id="myChart"></canvas>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach($stats_rows as $s): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center small">
                            <?= htmlspecialchars($s['categorie']) ?> <span class="badge bg-light text-dark border"><?= number_format($s['somme'], 2) ?> €</span>
                        </li>
                        <?php endforeach; ?>
                        <li class="list-group-item text-center fw-bold text-success fs-5 pt-3">
                            Total : <?= number_format($total_general, 2) ?> €
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm overflow-hidden">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Date</th>
                            <th>Description</th>
                            <th class="text-end">Montant</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="align-middle">
                            <td class="ps-3 small text-muted"><?= date('d/m/Y', strtotime($row['date_depense'])) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row['Description']) ?></strong>
                                <span class="badge bg-secondary-subtle text-secondary small"><?= htmlspecialchars($row['categorie']) ?></span> </td>
                            <td class="text-end fw-bold"><?= number_format($row['Montant'], 2) ?> €</td>
                            <td class="text-center">
                                <a href="modifier.php?id=<?= $row['id'] ?>" class="btn btn-link text-warning p-1"><i class="bi bi-pencil-square"></i></a>
                                <a href="supprimer.php?id=<?= $row['id'] ?>" class="btn btn-link text-danger p-1" onclick="return confirm('Supprimer cette dépense ?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('myChart');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($categories) ?>,
            datasets: [{
                data: <?= json_encode($montants) ?>,
                backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6610f2', '#fd7e14'],
                hoverOffset: 4
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
</body>
</html>