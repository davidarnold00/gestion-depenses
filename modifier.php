<?php
include 'db.php';

if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $id = (int) $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM depenses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if(!$row){
        echo "Dépense introuvable";
        exit();
    }
} else {
    echo "ID invalide";
    exit();
}
?>

<h2>Modifier la dépense</h2>
<form action="update.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
    <input type="number" step="0.01" name="montant" value="<?php echo $row['montant']; ?>" required>
    <input type="text" name="categorie" value="<?php echo $row['categorie']; ?>" required>
    <input type="text" name="description" value="<?php echo $row['description']; ?>" required>
    <input type="date" name="date" value="<?php echo $row['date_depense']; ?>" required>
    <button type="submit">Modifier</button>
</form>