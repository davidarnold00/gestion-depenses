<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_saisi = $_POST['login'];
    $password_saisi = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, login, password, role FROM utilisateurs WHERE login = ?");
    $stmt->bind_param("s", $login_saisi);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password_saisi, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_login'] = $user['login'];
        $_SESSION['role'] = $user['role']; 
        
        header("Location: index.php");
        exit();
    } else {
        if (!$user) {
            $error = "Utilisateur inconnu.";
        } else {
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
    <link rel="stylesheet" href="style.css">
    <title>Connexion - GeeWise</title>
</head>
<body class="page-login d-flex align-items-center vh-100">

         <div id="geewise-loader" class="loader-overlay d-none">
            <div class="loader-content">
            <h1 class="animate-flicker"><span class="logo-gee">Gee</span>Wise</h1>
            <div class="loader-text">Vérification...</div>
            </div>
        </div>
    </div>

    <div class="container border p-4 bg-white shadow-sm rounded" style="max-width: 400px; z-index: 10;">
        <h2 class="text-center mb-4"> <span class="logo-gee">Gee</span>Wise</h2>
        
        <form method="POST" id="loginForm">
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

    <script>
        //on cible le formulaire par son ID
    const loginForm = document.getElementById('loginForm');
    //on ecoute l'evenement
    loginForm.addEventListener('submit', function(event) {
        // 1. On empêche l'envoi immédiat pour laisser l'animation tourner
        event.preventDefault();
        
       // BOUTON : On le bloque et on change le texte
        const btn = this.querySelector('button');
        btn.disabled = true; // Empêche de recliquer pendant les 2 secondes
        btn.innerHTML = 'Vérification...';
        // 2. On affiche le loader
        document.getElementById('geewise-loader').classList.remove('d-none');
        
        // 3. On attend 2000ms (2 secondes) avant d'envoyer le formulaire
        setTimeout(() => {
            loginForm.submit(); 
        }, 2000);
    });
</script>

</body>
</html>