<?php
session_start();
include 'db.php';

// Si l'utilisateur est déjà connecté, on le renvoie à l'accueil
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $role = 'user'; // ON FORCE LE RÔLE À USER ICI (Sécurité)

    // Vérifier si le login existe déjà
    $check = $conn->prepare("SELECT id FROM utilisateurs WHERE login = ?");
    $check->bind_param("s", $login);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $error = "Désolé, ce nom d'utilisateur est déjà pris.";
    } else {
        // Hachage du mot de passe
        $pass_hache = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO utilisateurs (login, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $login, $pass_hache, $role);

        if ($stmt->execute()) {
            $success = "Compte créé ! Tu peux maintenant <a href='login.php'>te connecter</a>.";
        } else {
            $error = "Une erreur est survenue lors de l'inscription.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Inscription - SpendWise</title>
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container border p-4 bg-white shadow-sm rounded" style="max-width: 400px;">
        <h2 class="text-center mb-4">📝 Créer un compte</h2>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <input type="text" name="login" class="form-control" placeholder="Nom d'utilisateur" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
        </form>
        <div class="text-center mt-3">
            <small>Déjà un compte ? <a href="login.php">Connexion</a></small>
        </div>
    </div>
</body>
</html>