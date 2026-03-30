<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_saisi = $_POST['login'];
    $password_saisi = $_POST['password'];

    // J'ajoute 'login' dans mon SELECT pour ne plus avoir l'erreur Undefined key
    $stmt = $conn->prepare("SELECT id, login, password, role FROM utilisateurs WHERE login = ?");
    $stmt->bind_param("s", $login_saisi);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Je vérifie si l'utilisateur existe ET si le mot de passe est correct
    if ($user && password_verify($password_saisi, $user['password'])) {
        // SUCCÈS : Je remplis la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_login'] = $user['login']; // J'utilise la valeur de la base
        $_SESSION['role'] = $user['role']; 
        
        header("Location: index.php");
        exit();
    } else {
        // ÉCHEC : Je prépare le message d'erreur
        if (!$user) {
            $error = "Utilisateur inconnu.";
        } else {
            // Ici, $user['login'] existe maintenant car je l'ai ajouté dans le SELECT
            $error = "Mot de passe incorrect pour " . htmlspecialchars($user['login']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Connexion - SpendWise</title>
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container border p-4 bg-white shadow-sm rounded" style="max-width: 400px;">
        <h2 class="text-center mb-4">SpendWise</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nom d'utilisateur</label>
                <input type="text" name="login" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>

            <div class="text-center mt-3">
            <small>Pas encore de compte ? <a href="inscription.php">Créer un compte</a></small>
            </div>
        </form>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>