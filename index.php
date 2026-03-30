<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

/** 1. RÉCUPÉRATION DES DONNÉES SELON LE RÔLE **/
if ($user_role === 'admin') {
    // JOINTURE pour voir le nom de l'utilisateur sur chaque dépense
    $query = "SELECT d.*, u.login as proprietaire 
              FROM depenses d 
              JOIN utilisateurs u ON d.user_id = u.id 
              ORDER BY d.date_depense DESC";
    $result = $conn->query($query);
    
    $total_query = $conn->query("SELECT SUM(montant) as total FROM depenses");
    $total_general = $total_query->fetch_assoc()['total'] ?? 0;

    $sql_stats = "SELECT categorie, SUM(montant) as somme FROM depenses GROUP BY categorie";
    $stats_result = $conn->query($sql_stats);

    // Liste des utilisateurs pour le panneau d'administration
    $users_list = $conn->query("SELECT id, login, role FROM utilisateurs ORDER BY id ASC");
} else {
    // Mode utilisateur classique (Filtré)
    $stmt = $conn->prepare("SELECT * FROM depenses WHERE user_id = ? ORDER BY date_depense DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_stmt = $conn->prepare("SELECT SUM(montant) as total FROM depenses WHERE user_id = ?");
    $total_stmt->bind_param("i", $user_id);
    $total_stmt->execute();
    $total_general = $total_stmt->get_result()->fetch_assoc()['total'] ?? 0;

    $stats_stmt = $conn->prepare("SELECT categorie, SUM(montant) as somme FROM depenses WHERE user_id = ? GROUP BY categorie");
    $stats_stmt->bind_param("i", $user_id);
    $stats_stmt->execute();
    $stats_result = $stats_stmt->get_result();
}

/** 2. PRÉPARATION DES STATS POUR LE GRAPHIQUE **/
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
    <title>GeeWise - <?= ($user_role === 'admin') ? 'Administration' : 'Mon Budget' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-wallet2 me-2"></i>GeeWise</a>
        <div class="navbar-nav ms-auto text-white align-items-center">
            <span class="badge bg-<?= ($user_role === 'admin') ? 'danger' : 'primary' ?> me-3">
                <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user_login']) ?> (<?= $user_role ?>)
            </span>
            <a href="logout.php" class="btn btn-sm btn-outline-light">Déconnexion</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <?php if ($user_role !== 'admin'): ?>
            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-header bg-primary text-white fw-bold">Ajouter une dépense</div>
                <div class="card-body">
                    <form action="ajouter.php" method="POST">
                        <div class="mb-2"><input type="number" step="0.01" name="montant" class="form-control" placeholder="Montant (€)" required></div>
                        <div class="mb-2">
                            <select name="categorie" class="form-select" required>
                                <option value="Alimentation">Alimentation</option>
                                <option value="Transport">Transport</option>
                                <option value="Loisirs">Loisirs</option>
                                <option value="Santé">Santé</option>
                            </select>
                        </div>
                        <div class="mb-3"><input type="text" name="description" class="form-control" placeholder="Description" required></div>
                        <button type="submit" class="btn btn-primary w-100">Ajouter</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <div class="card shadow-sm mb-4 border-success">
                <div class="card-header bg-success text-white fw-bold">Répartition <?= ($user_role === 'admin') ? 'Globale' : 'Personnelle' ?></div>
                <div class="card-body text-center">
                    <canvas id="myChart" style="max-height: 250px;"></canvas>
                    <h4 class="mt-3 text-success fw-bold">Total : <?= number_format($total_general, 2, ',', ' ') ?> €</h4>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold border-0 pt-3">📋 Historique des dépenses</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-3">DATE</th>
                                <?php if ($user_role === 'admin'): ?><th>UTILISATEUR</th><?php endif; ?>
                                <th>DESCRIPTION</th>
                                <th class="text-end">MONTANT</th>
                                <th class="text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-3 small"><?= date('d/m/Y', strtotime($row['date_depense'])) ?></td>
                                <?php if ($user_role === 'admin'): ?>
                                    <td class="small text-uppercase">
                                     <a href="view_user.php?id=<?= $row['user_id'] ?>" class="fw-bold text-primary text-decoration-none">
                                     <i class="bi bi-search me-1"></i><?= htmlspecialchars($row['proprietaire']) ?>
                                      </a>
                                    </td>                
                                <?php endif; ?>
                                <td>
                                    <strong><?= htmlspecialchars($row['description']) ?></strong><br>
                                    <span class="badge bg-light text-secondary border small" style="font-size: 0.7rem;"><?= htmlspecialchars($row['categorie']) ?></span>
                                </td>
                                <td class="text-end fw-bold"><?= number_format($row['montant'], 2, ',', ' ') ?> €</td>
                                <td class="text-center">
                                    <a href="modifier.php?id=<?= $row['id'] ?>" class="btn btn-link text-warning p-1"><i class="bi bi-pencil-square"></i></a>
                                    <a href="supprimer.php?id=<?= $row['id'] ?>" class="btn btn-link text-danger p-1" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if ($user_role === 'admin'): ?>
            <div class="card shadow-sm border-danger mt-4 mb-5">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🛡️ Gestion des Utilisateurs</h5>
                    <a href="inscription.php" class="btn btn-sm btn-light">Créer un compte</a>
                </div>
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>LOGIN</th>
                            <th>RÔLE</th>
                            <th class="text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($u = $users_list->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-3">#<?= $u['id'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($u['login']) ?></td>
                            <td><span class="badge <?= ($u['role'] === 'admin') ? 'bg-danger' : 'bg-secondary' ?>"><?= $u['role'] ?></span></td>
                            <td class="text-center">
                                <?php if($u['login'] !== $_SESSION['user_login']): ?>
                                <button class="btn btn-outline-danger btn-sm" onclick="supprimerUser(<?= $u['id'] ?>, this)"> <i class="bi bi-trash"></i> Bannir cet utilisateur</button>                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
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
                borderWidth: 0
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } }, cutout: '70%' }
    });
</script>

<script>
function supprimerUser(userId, bouton) {
    if (confirm("Voulez-vous vraiment supprimer cet utilisateur ?")) {
        
        // On appelle ton fichier PHP (L'API)
        fetch('supprimer_user.php?id=' + userId)
        .then(response => {
            if (response.ok) {
                // Si la base de données a répondu OK
                // On trouve la ligne (<tr>) et on la fait disparaître visuellement
                const ligne = bouton.closest('tr');
                ligne.style.transition = "all 0.6";
                ligne.style.opacity = "0";
                ligne.style.transform = "translateX(20px)";
                
                setTimeout(() => {
                    ligne.remove(); // On retire la ligne du HTML après l'animation
                }, 500);
            } else {
                alert("Erreur lors de la suppression.");
            }
        })
        .catch(error => console.error('Erreur API:', error));
    }
}
</script>
</body>
</html>