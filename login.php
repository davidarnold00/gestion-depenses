<?php
// 1. On force l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php'; // On utilise require_once pour être sûr que db.php est là

echo "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_saisi = $_POST['login'];
    $password_saisi = $_POST['password'];

    echo "Tentative de connexion pour : " . htmlspecialchars($login_saisi) . "<br>";

    // Requête préparée
    $stmt = $conn->prepare("SELECT id, password FROM utilisateurs WHERE login = ?");
    if (!$stmt) {
        die("Erreur de préparation SQL : " . $conn->error);
    }

    $stmt->bind_param("s", $login_saisi);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        echo "Utilisateur trouvé en base ! Vérification du mot de passe...<br>";
        
             if ($password_saisi === $user['password']) {
                echo "Mot de passe correct ! Redirection...";
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_login'] = $login_saisi;
            header("Location: index.php");
            exit();
        } else {
            echo "ÉCHEC : Le mot de passe ne correspond pas au hachage.<br>";
            $error = "Mot de passe incorrect.";
        }
    } else {
        echo "ÉCHEC : Aucun utilisateur trouvé avec ce login.<br>";
        $error = "Identifiant inconnu.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Test Connexion</title>
</head>
<body class="p-5">
    <div class="container border p-4" style="max-width: 400px;">
        <form method="POST">
            <input type="text" name="login" class="form-control mb-2" placeholder="Login" required>
            <input type="password" name="password" class="form-control mb-2" placeholder="Pass" required>
            <button type="submit" class="btn btn-primary w-100">Tester</button>
        </form>
        <?php if(isset($error)) echo "<div class='alert alert-danger mt-2'>$error</div>"; ?>
    </div>
</body>
</html>