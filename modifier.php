<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Récupération de la dépense à modifier
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $id = (int) $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM depenses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if(!$row){
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la dépense - SpendWise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">💰 SpendWise</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark fw-bold">
                    Modifier la dépense n°<?= $row['id'] ?>
                </div>
                <div class="card-body">
                    <form action="update.php" method="POST">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        
                        <div class="mb-3">
                             <label class="form-label small">Montant (€)</label>
                             <input type="number" step="0.01" name="montant" class="form-control" value="<?= $row['Montant'] ?>" required>
                       </div>

                        <div class="mb-3">
                            <label class="form-label">Catégorie</label>
                            <select name="categorie" class="form-select" required>
                                <?php 
                                $cats = ["Alimentation", "Loisirs", "Transport", "Loyer/Charges", "Autre"];
                                foreach($cats as $cat) {
                                    $selected = ($cat == $row['categorie']) ? "selected" : "";
                                    echo "<option value='$cat' $selected>$cat</option>";
                                }
                                ?>
                            </select>
                        </div>

                                  <div class="mb-3">
                                         <label class="form-label small">Description</label>
                                      <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($row['Description']) ?>" required>
                                     </div>

                        <div class="mb-3">
                            <label class="form-label">Date (optionnel)</label>
                            <input type="datetime-local" name="date" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($row['date_depense'])) ?>">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>