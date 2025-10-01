<?php
require_once 'config.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $role = $_POST['role'] ?? 'user';

    // Validation
    if (!$nom || !$email || !$password || !$confirm) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } elseif ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé.";
        } else {
            // Hashage du mot de passe
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, rôle) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nom, $email, $hash, $role])) {
                $_SESSION['success'] = "Inscription réussie, vous pouvez vous connecter.";
                header('Location: index.php');
                exit();
            } else {
                $error = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription utilisateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --success: #28a745;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #6c757d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h2 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .header p {
            opacity: 0.8;
        }
        
        .form-container {
            padding: 30px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }
        
        .alert i {
            margin-right: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            outline: none;
        }
        
        .form-group .input-icon {
            position: absolute;
            right: 15px;
            top: 40px;
            color: var(--gray);
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn i {
            margin-right: 10px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            color: var(--gray);
        }
        
        .footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        .password-strength {
            margin-top: 5px;
            height: 5px;
            background: #eee;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0%;
            background: #dc3545;
            transition: all 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-user-plus"></i> Créer un compte</h2>
            <p>Rejoignez notre plateforme dès maintenant</p>
        </div>
        
        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="post" id="registerForm">
                <div class="form-group">
                    <label for="nom">Nom complet</label>
                    <input type="text" name="nom" id="nom" required placeholder="Votre nom complet">
                    <i class="fas fa-user input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" name="email" id="email" required placeholder="exemple@domaine.com">
                    <i class="fas fa-envelope input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" required placeholder="••••••••">
                    <i class="fas fa-lock input-icon"></i>
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm">Confirmer le mot de passe</label>
                    <input type="password" name="confirm" id="confirm" required placeholder="••••••••">
                    <i class="fas fa-lock input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="role">Rôle</label>
                    <select name="role" id="role">
                        <option value="user">Utilisateur</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </button>
            </form>
            
            <div class="footer">
                Déjà inscrit ? <a href="index.php">Connectez-vous ici</a>
            </div>
        </div>
    </div>

    <script>
        // Indicateur de force du mot de passe
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthBar = document.getElementById('strengthBar');
            let strength = 0;
            
            if (password.length > 0) strength += 1;
            if (password.length >= 8) strength += 1;
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            // Mettre à jour la barre de force
            const width = strength * 20;
            strengthBar.style.width = width + '%';
            
            // Changer la couleur selon la force
            if (strength <= 2) {
                strengthBar.style.backgroundColor = '#dc3545';
            } else if (strength <= 4) {
                strengthBar.style.backgroundColor = '#ffc107';
            } else {
                strengthBar.style.backgroundColor = '#28a745';
            }
        });
        
        // Confirmation du mot de passe
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas');
            }
        });
    </script>
</body>
</html>