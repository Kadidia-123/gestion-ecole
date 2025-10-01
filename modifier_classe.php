<?php
require_once './config.php';
require_once './functions.php';

// Vérifier si l'ID de la classe est présent
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: classes.php");
    exit();
}

$id = (int)$_GET['id'];
$classe = [];
$error = null;
$flash = getFlash();

// Récupérer les informations de la classe
try {
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([$id]);
    $classe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$classe) {
        setFlash('warning', 'Classe non trouvée');
        header("Location: classes.php");
        exit();
    }
} catch (PDOException $e) {
    $error = "Erreur de base de données : " . $e->getMessage();
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $nom = trim($_POST['nom']);
    $niveau = trim($_POST['niveau']);

    try {
        $stmt = $pdo->prepare("UPDATE classes SET nom = ?, niveau = ? WHERE id = ?");
        $stmt->execute([$nom, $niveau, $id]);
        setFlash('success', 'Classe modifiée avec succès');
        header("Location: voir_classe.php?id=$id");
        exit();
    } catch (PDOException $e) {
        $error = "Erreur lors de la modification : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier la Classe | SchoolAdmin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #eef2ff;
      --primary-dark: #3a56d4;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --success-light: #e6f7ff;
      --warning: #f8961e;
      --warning-light: #fff4e6;
      --danger: #f72585;
      --danger-light: #ffe6ef;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
      --light-gray: #e9ecef;
      --border-radius: 8px;
      --border-radius-lg: 12px;
      --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      --transition: all 0.3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #f5f7fb;
      color: var(--dark);
      line-height: 1.6;
    }

    .container {
      max-width: 800px;
      margin: 2rem auto;
      padding: 0 1rem;
    }

    .card {
      background: white;
      border-radius: var(--border-radius-lg);
      box-shadow: var(--box-shadow);
      overflow: hidden;
    }

    .card-header {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      padding: 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
    }

    .card-header::before {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--danger));
    }

    .header-title {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .header-title h2 {
      font-weight: 600;
      margin: 0;
      font-size: 1.5rem;
    }

    .header-actions {
      display: flex;
      gap: 0.5rem;
    }

    .card-body {
      padding: 2rem;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.75rem 1.5rem;
      border-radius: var(--border-radius);
      border: none;
      cursor: pointer;
      font-weight: 500;
      transition: var(--transition);
      text-decoration: none;
      font-size: 1rem;
    }

    .btn i {
      font-size: 0.9em;
    }

    .btn-primary {
      background-color: var(--primary);
      color: white;
      box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
    }

    .btn-primary:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 6px 8px rgba(67, 97, 238, 0.3);
    }

    .btn-secondary {
      background-color: white;
      color: var(--gray);
      border: 1px solid var(--light-gray);
    }

    .btn-secondary:hover {
      background-color: var(--light);
    }

    .btn-back {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      width: 2.5rem;
      height: 2.5rem;
      border-radius: 50%;
      padding: 0;
      transition: var(--transition);
    }

    .btn-back:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: translateX(-3px);
    }

    .form-container {
      background: white;
      border-radius: var(--border-radius-lg);
      padding: 2rem;
      box-shadow: var(--box-shadow);
    }

    .form-group {
      margin-bottom: 1.75rem;
    }

    .form-label {
      display: block;
      margin-bottom: 0.75rem;
      font-weight: 500;
      color: var(--dark);
      font-size: 1rem;
    }

    .form-control {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid var(--light-gray);
      border-radius: var(--border-radius);
      transition: var(--transition);
      font-size: 1rem;
      background-color: white;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }

    .alert {
      padding: 1rem 1.25rem;
      border-radius: var(--border-radius);
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      border-left: 4px solid transparent;
    }

    .alert i {
      font-size: 1.25em;
    }

    .alert-danger {
      background-color: var(--danger-light);
      color: #721c24;
      border-left-color: var(--danger);
    }

    .alert-success {
      background-color: var(--success-light);
      color: #0c5460;
      border-left-color: var(--success);
    }

    .alert-warning {
      background-color: var(--warning-light);
      color: #856404;
      border-left-color: var(--warning);
    }

    .alert-info {
      background-color: #e7f5ff;
      color: #0c5460;
      border-left-color: #4cb5f5;
    }

    .action-buttons {
      display: flex;
      gap: 1rem;
      margin-top: 2.5rem;
      justify-content: flex-end;
    }

    /* Dark mode */
    body.dark-mode {
      background-color: #1a1a2e;
      color: #f8f9fa;
    }

    body.dark-mode .card,
    body.dark-mode .form-container {
      background-color: #16213e;
    }

    body.dark-mode .form-control {
      background-color: #1f4068;
      border-color: #1f4068;
      color: white;
    }

    body.dark-mode .form-control:focus {
      border-color: var(--primary);
    }

    body.dark-mode .form-label {
      color: #f8f9fa;
    }

    body.dark-mode .btn-secondary {
      background-color: #1f4068;
      color: white;
      border-color: #1f4068;
    }

    body.dark-mode .btn-secondary:hover {
      background-color: #1a3360;
    }

    @media (max-width: 768px) {
      .card-body {
        padding: 1.5rem;
      }
      
      .action-buttons {
        flex-direction: column;
      }
      
      .btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="card-header">
        <div class="header-title">
          <a href="voir_classe.php?id=<?= $id ?>" class="btn btn-back" title="Retour aux détails">
            <i class="fas fa-arrow-left"></i>
          </a>
          <h2><i class="fas fa-edit"></i> Modifier la Classe</h2>
        </div>
        <div class="header-actions">
          <button id="themeToggle" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white;">
            <i class="fas fa-moon"></i> Thème
          </button>
        </div>
      </div>
      
      <div class="card-body">
        <?php if ($error): ?>
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <?php if ($flash): ?>
          <div class="alert alert-<?= $flash['type'] ?>">
            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'danger' ? 'exclamation-circle' : 'info-circle') ?>"></i>
            <?= htmlspecialchars($flash['message']) ?>
          </div>
        <?php endif; ?>

        <div class="form-container">
          <form method="POST">
            <div class="form-group">
              <label for="nom" class="form-label">Nom de la classe</label>
              <input type="text" id="nom" name="nom" class="form-control" 
                     value="<?= htmlspecialchars($classe['nom']) ?>" required>
            </div>
            
            <div class="form-group">
              <label for="niveau" class="form-label">Niveau</label>
              <input type="text" id="niveau" name="niveau" class="form-control" 
                     value="<?= htmlspecialchars($classe['niveau']) ?>" required>
            </div>
            
            <div class="action-buttons">
              <a href="voir_classe.php?id=<?= $id ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
              </a>
              <button type="submit" name="update" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Gestion du thème sombre
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    
    if (localStorage.getItem('darkMode') === 'true') {
      body.classList.add('dark-mode');
      themeToggle.innerHTML = '<i class="fas fa-sun"></i> Thème clair';
    }
    
    themeToggle.addEventListener('click', () => {
      body.classList.toggle('dark-mode');
      const isDark = body.classList.contains('dark-mode');
      localStorage.setItem('darkMode', isDark);
      themeToggle.innerHTML = isDark 
        ? '<i class="fas fa-sun"></i> Thème clair' 
        : '<i class="fas fa-moon"></i> Thème sombre';
    });
  </script>
</body>
</html>