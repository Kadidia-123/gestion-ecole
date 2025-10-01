<?php
// logout.php
session_start();

// Stocker le nom d'utilisateur pour l'affichage
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Détruire proprement la session
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

session_destroy();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --danger: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .logout-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .logout-icon {
            font-size: 3rem;
            color: var(--danger);
            margin-bottom: 1.5rem;
        }

        h1 {
            color: var(--dark);
            margin-bottom: 1rem;
        }

        p {
            color: #6c757d;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: white;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .username {
            font-weight: 600;
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <h1>Déconnexion réussie</h1>
        <?php if (!empty($username)): ?>
            <p>Au revoir <span class="username"><?= htmlspecialchars($username) ?></span>, vous avez été déconnecté avec succès.</p>
        <?php else: ?>
            <p>Vous avez été déconnecté avec succès.</p>
        <?php endif; ?>
        <a href="index.php" class="btn">
            <i class="fas fa-home"></i> Retour à l'accueil
        </a>
    </div>

    <!-- Redirection automatique après 5 secondes -->
    <script>
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 5000);
    </script>
</body>
</html>