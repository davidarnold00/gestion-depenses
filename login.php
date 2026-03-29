<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_saisi = $_POST['login'];
    $password_saisi = $_POST['password'];

    // J'utilise une requête préparée pour éviter les injections SQL
    // Je récupère aussi le 'role' pour savoir si c'est un admin ou un user
    $stmt = $conn->prepare("SELECT id, password, role FROM utilisateurs WHERE login = ?");
    $stmt->bind_param("s", $login_saisi);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Je vérifie si l'utilisateur existe ET si le mot de passe correspond au hachage en base
    if ($user && password_verify($password_saisi, $user['password'])) {
        // Si c'est bon, je crée la session avec les informations importantes
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_login'] = $login_saisi;
        $_SESSION['role'] = $user['role']; 
        
        header("Location: index.php");
        exit();
    } else {
        $error = "Identifiants incorrects.";
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
        <h2 class="text-center mb-4">Connexion</h2>
        <form method="POST">
            <input type="text" name="login" class="form-control mb-3" placeholder="Login" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Mot de passe" required>
            <button type="submit" class="btn btn-primary w-100">Entrer</button>
        </form>
        <?php if(isset($error)) echo "<div class='alert alert-danger mt-3'>$error</div>"; ?>
    </div>
</body>
</html>