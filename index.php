<?php
include 'db.php';

$result = $conn->query("SELECT * FROM depenses ORDER BY date_depense DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Dépenses</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Gestion de Dépenses</h1>

<h2>Ajouter une dépense</h2>
<form action="ajouter.php" method="POST">
    <input type="number" step="0.01" name="montant" placeholder="Montant" required>
    <input type="text" name="categorie" placeholder="Catégorie" required>
    <input type="text" name="description" placeholder="Description" required>
    <input type="date" name="date" required>
    <button type="submit">Ajouter</button>
</form>

<h2>Liste des dépenses</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Montant</th>
            <th>Catégorie</th>
            <th>Description</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo isset($row['montant']) ? number_format($row['montant'], 2, ',', ' ') : '0,00'; ?> €</td>
            <td><?php echo isset($row['categorie']) ? htmlspecialchars($row['categorie']) : ''; ?></td>
            <td><?php echo isset($row['description']) ? htmlspecialchars($row['description']) : ''; ?></td>
            <td><?php echo isset($row['date_depense']) ? $row['date_depense'] : ''; ?></td>
            <td>
                <a href="modifier.php?id=<?php echo $row['id']; ?>">Modifier</a> |
                <a href="supprimer.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Voulez-vous vraiment supprimer cette dépense ?');">Supprimer</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>