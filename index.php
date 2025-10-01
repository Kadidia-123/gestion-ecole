<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Système Éducatif</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .header {
            background: #4a6fc7;
            color: white;
            text-align: center;
            padding: 25px 20px;
        }
        
        .header h2 {
            font-weight: 600;
            font-size: 24px;
        }
        
        .form-container {
            padding: 30px;
        }
        
        .input-group {
            margin-bottom: 20px;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .input-group input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .input-group input:focus {
            border-color: #4a6fc7;
            outline: none;
            box-shadow: 0 0 0 2px rgba(74, 111, 199, 0.2);
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            background: #4a6fc7;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #3b5aa6;
        }
        
        .message {
            text-align: center;
            margin-top: 20px;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .error {
            background: #ffebee;
            color: #d32f2f;
        }
        
        .success {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        
        .password-toggle {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #777;
            cursor: pointer;
        }
        
        .demo-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .demo-info h4 {
            margin-bottom: 10px;
            color: #1976d2;
        }
        
        .demo-account {
            margin: 8px 0;
            padding: 8px;
            background: #bbdefb;
            border-radius: 6px;
        }
        
        @media (max-width: 480px) {
            .container {
                border-radius: 8px;
            }
            
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Connexion au Système Éducatif</h2>
        </div>
        
        <div class="form-container">
            <form action="login.php" method="POST" id="loginForm">
                <div class="input-group">
                    <label for="email">Adresse Email</label>
                    <input type="email" id="email" name="email" required placeholder="votre@email.com">
                </div>
                
                <div class="input-group password-toggle">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required placeholder="Votre mot de passe">
                    <button type="button" class="toggle-password" id="togglePassword">👁️</button>
                </div>
                
                <button type="submit" class="btn">Se connecter</button>
                
                <?php
                // Afficher les messages d'erreur de session
                if (isset($_SESSION['error'])) {
                    echo '<div class="message error">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                
                // Afficher les messages de déconnexion
                if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
                    echo '<div class="message success">Vous avez été déconnecté avec succès</div>';
                }
                ?>
            </form>
            
            <div class="footer">
                <p>© 2023 Système Éducatif. Tous droits réservés.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            // Fonction pour afficher/masquer le mot de passe
            togglePassword.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    togglePassword.textContent = '🔒';
                } else {
                    passwordInput.type = 'password';
                    togglePassword.textContent = '👁️';
                }
            });
            
            // Validation basique du formulaire
            const loginForm = document.getElementById('loginForm');
            loginForm.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                
                if (!email || !password) {
                    e.preventDefault();
                    alert('Veuillez remplir tous les champs');
                    return false;
                }
                
                // Validation basique de l'email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    alert('Veuillez entrer une adresse email valide');
                    return false;
                }
            });
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - Gestion Scolaire</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <div class="login-container">
    <h1><i class="fas fa-graduation-cap"></i> Gestion Scolaire</h1>
    
    <form action="login.php" method="post">
      <div class="form-group">
        <label for="email"><i class="fas fa-envelope"></i> Email</label>
        <input type="email" id="email" name="email" class="form-control" required>
      </div>
      
      <div class="form-group">
        <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
        <input type="password" id="password" name="password" class="form-control" required>
      </div>
      
      <button type="submit" class="btn btn-primary" style="width: 100%;">
        <i class="fas fa-sign-in-alt"></i> Se connecter
      </button>
    </form>

    <a href="register.php" class="btn btn-link" style="display:block; margin-top:15px; text-align:center;">
      <i class="fas fa-user-plus"></i> Créer un compte
    </a>

    <button id="themeToggle" class="btn btn-secondary" style="margin-top: 20px; width: 100%;">
      <i class="fas fa-moon"></i> Thème sombre
    </button>
  </div>


  <script>
    document.getElementById('themeToggle').addEventListener('click', function() {
      document.body.classList.toggle('dark-theme');
      this.innerHTML = document.body.classList.contains('dark-theme') ? 
        '<i class="fas fa-sun"></i> Thème clair' : 
        '<i class="fas fa-moon"></i> Thème sombre';
      localStorage.setItem('darkTheme', document.body.classList.contains('dark-theme'));
    });

    if (localStorage.getItem('darkTheme') === 'true') {
      document.body.classList.add('dark-theme');
      document.getElementById('themeToggle').innerHTML = '<i class="fas fa-sun"></i> Thème clair';
    }
  </script>
</body>
</html>