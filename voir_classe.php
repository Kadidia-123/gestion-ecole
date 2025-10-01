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
$eleves = [];
$error = null;
$flash = getFlash();

try {
    // Récupérer les informations de la classe
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([$id]);
    $classe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$classe) {
        setFlash('warning', 'Classe non trouvée');
        header("Location: classes.php");
        exit();
    }

    // Récupérer les élèves de cette classe
    $stmt = $pdo->prepare("SELECT * FROM eleves WHERE classe_id = ? ORDER BY nom, prenom");
    $stmt->execute([$id]);
    $eleves = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Erreur de base de données : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Détails de la Classe | SchoolAdmin</title>
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
      max-width: 1200px;
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
      gap: 0.75rem;
      align-items: center;
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

    .btn-warning {
      background-color: var(--warning);
      color: white;
      box-shadow: 0 4px 6px rgba(248, 150, 30, 0.2);
    }

    .btn-warning:hover {
      background-color: #e67e22;
      transform: translateY(-2px);
      box-shadow: 0 6px 8px rgba(248, 150, 30, 0.3);
    }

    .btn-sm {
      padding: 0.5rem 1rem;
      font-size: 0.85rem;
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

    .class-info {
      background: white;
      border-radius: var(--border-radius-lg);
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: var(--box-shadow);
      border: 1px solid var(--light-gray);
    }

    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }

    .info-item {
      padding: 1.5rem;
      background: var(--light);
      border-radius: var(--border-radius);
      border-left: 4px solid var(--primary);
      transition: var(--transition);
    }

    .info-item:hover {
      transform: translateY(-3px);
      box-shadow: var(--box-shadow);
    }

    .info-label {
      font-size: 0.9rem;
      color: var(--gray);
      margin-bottom: 0.5rem;
      font-weight: 500;
    }

    .info-value {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--dark);
    }

    .section-title {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1.5rem;
      font-size: 1.25rem;
      color: var(--dark);
    }

    .badge {
      background: var(--primary);
      color: white;
      border-radius: 50%;
      width: 2rem;
      height: 2rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.9rem;
      font-weight: 600;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      margin: 1.5rem 0;
    }

    .table th {
      background-color: var(--primary-light);
      color: var(--primary-dark);
      padding: 1rem;
      text-align: left;
      font-weight: 600;
      position: sticky;
      top: 0;
    }

    .table td {
      padding: 1rem;
      border-bottom: 1px solid var(--light-gray);
    }

    .table tr:last-child td {
      border-bottom: none;
    }

    .table tr:hover td {
      background-color: rgba(67, 97, 238, 0.05);
    }

    .actions {
      display: flex;
      gap: 0.5rem;
    }

    .empty-state {
      text-align: center;
      padding: 3rem 2rem;
      color: var(--gray);
      background: white;
      border-radius: var(--border-radius-lg);
      box-shadow: var(--box-shadow);
      margin: 2rem 0;
    }

    .empty-state i {
      font-size: 3.5rem;
      margin-bottom: 1.5rem;
      color: var(--light-gray);
    }

    .empty-state h3 {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
      color: var(--dark);
    }

    .empty-state p {
      margin-bottom: 1.5rem;
      font-size: 1.1rem;
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

    /* Dark mode */
    body.dark-mode {
      background-color: #1a1a2e;
      color: #f8f9fa;
    }

    body.dark-mode .card,
    body.dark-mode .class-info,
    body.dark-mode .empty-state {
      background-color: #16213e;
      border-color: #1f4068;
    }

    body.dark-mode .info-item {
      background-color: #1f4068;
    }

    body.dark-mode .info-value,
    body.dark-mode .empty-state h3 {
      color: #f8f9fa;
    }

    body.dark-mode .table th {
      background-color: #1f4068;
      color: white;
    }

    body.dark-mode .table td {
      border-bottom-color: #1f4068;
    }

    body.dark-mode .table tr:hover td {
      background-color: rgba(31, 64, 104, 0.5);
    }

    @media (max-width: 768px) {
      .card-body {
        padding: 1.5rem;
      }
      
      .info-grid {
        grid-template-columns: 1fr;
      }
      
      .header-title h2 {
        font-size: 1.25rem;
      }
      
      .table {
        display: block;
        overflow-x: auto;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="card-header">
        <div class="header-title">
          <a href="classes.php" class="btn btn-back" title="Retour à la liste des classes">
            <i class="fas fa-arrow-left"></i>
          </a>
          <h2><i class="fas fa-door-open"></i> Détails de la Classe</h2>
        </div>
        <div class="header-actions">
          <a href="modifier_classe.php?id=<?= $id ?>" class="btn btn-warning">
            <i class="fas fa-edit"></i> Modifier
          </a>
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

        <div class="class-info">
          <div class="info-grid">
            <div class="info-item">
              <div class="info-label">ID</div>
              <div class="info-value"><?= htmlspecialchars($classe['id']) ?></div>
            </div>
            <div class="info-item">
              <div class="info-label">Nom</div>
              <div class="info-value"><?= htmlspecialchars($classe['nom']) ?></div>
            </div>
            <div class="info-item">
              <div class="info-label">Niveau</div>
              <div class="info-value"><?= htmlspecialchars($classe['niveau']) ?></div>
            </div>
          </div>
        </div>

        <div class="section-title">
          <i class="fas fa-users"></i>
          <span>Élèves de cette classe</span>
          <span class="badge"><?= count($eleves) ?></span>
        </div>

        <?php if (!empty($eleves)): ?>
          <div style="overflow-x: auto;">
            <table class="table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nom</th>
                  <th>Prénom</th>
                  <th>Date de naissance</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($eleves as $eleve): ?>
                <tr>
                  <td><?= htmlspecialchars($eleve['id']) ?></td>
                  <td><?= htmlspecialchars($eleve['nom']) ?></td>
                  <td><?= htmlspecialchars($eleve['prenom']) ?></td>
                  <td><?= date('d/m/Y', strtotime($eleve['date_naissance'])) ?></td>
                  <td>
                    <div class="actions">
                      <a href="voir_eleve.php?id=<?= $eleve['id'] ?>" class="btn btn-primary btn-sm" title="Voir détails">
                        <i class="fas fa-eye"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="empty-state">
            <i class="fas fa-user-graduate"></i>
            <h3>Aucun élève dans cette classe</h3>
            <p>Cette classe ne contient actuellement aucun élève.</p>
            <a href="ajouter_eleve.php?classe_id=<?= $id ?>" class="btn btn-primary" style="margin-top: 1rem;">
              <i class="fas fa-plus"></i> Ajouter un élève
            </a>
          </div>
        <?php endif; ?>
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