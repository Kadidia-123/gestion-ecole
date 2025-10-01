<?php
require_once './config.php';
require_once './functions.php';

// Initialisation des variables
$search = $_GET['search'] ?? '';
$classes = [];
$total = 0;
$totalPages = 1;
$error = null;
$flash = getFlash();

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $nom = trim($_POST['nom']);
    $niveau = trim($_POST['niveau']);

    try {
        $stmt = $pdo->prepare("INSERT INTO classes (nom, niveau) VALUES (?, ?)");
        $stmt->execute([$nom, $niveau]);
        setFlash('success', 'Classe ajoutée avec succès');
        header("Location: classes.php");
        exit();
    } catch (PDOException $e) {
        $error = "Erreur lors de l'ajout : " . $e->getMessage();
    }
}

// Traitement de la suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    try {
        // Vérifier d'abord si la classe a des élèves
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM eleves WHERE classe_id = ?");
        $stmt->execute([$id]);
        $hasStudents = $stmt->fetchColumn();
        
        if ($hasStudents) {
            setFlash('warning', 'Impossible de supprimer : cette classe contient des élèves');
        } else {
            $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'Classe supprimée avec succès');
        }
        header("Location: classes.php");
        exit();
    } catch (PDOException $e) {
        $error = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Récupération des classes
try {
    $whereClause = '';
    $params = [];

    if (!empty($search)) {
        $whereClause = "WHERE nom LIKE :search OR niveau LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    // Requête pour le total
    $countSql = "SELECT COUNT(*) FROM classes $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();
    $totalPages = ceil($total / $limit);

    // Requête pour récupérer les classes
    if ($total > 0) {
        $sql = "SELECT * FROM classes 
                $whereClause
                ORDER BY niveau, nom
                LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error = "Erreur de base de données : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des Classes | SchoolAdmin</title>
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
    }

    .header-title {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .header-title h2 {
      font-weight: 600;
      margin: 0;
    }

    .header-actions {
      display: flex;
      gap: 0.5rem;
    }

    .card-body {
      padding: 1.5rem;
    }

    .card-footer {
      padding: 1rem 1.5rem;
      background: var(--light);
      border-top: 1px solid var(--light-gray);
      font-size: 0.9rem;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.5rem 1rem;
      border-radius: var(--border-radius);
      border: none;
      cursor: pointer;
      font-weight: 500;
      transition: var(--transition);
      text-decoration: none;
    }

    .btn i {
      font-size: 0.9em;
    }

    .btn-sm {
      padding: 0.4rem 0.8rem;
      font-size: 0.85rem;
    }

    .btn-primary {
      background-color: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background-color: var(--primary-dark);
    }

    .btn-success {
      background-color: #2ecc71;
      color: white;
    }

    .btn-success:hover {
      background-color: #27ae60;
    }

    .btn-warning {
      background-color: var(--warning);
      color: white;
    }

    .btn-warning:hover {
      background-color: #e67e22;
    }

    .btn-danger {
      background-color: var(--danger);
      color: white;
    }

    .btn-danger:hover {
      background-color: #e91e63;
    }

    .btn-secondary {
      background-color: var(--gray);
      color: white;
    }

    .btn-secondary:hover {
      background-color: #5a6268;
    }

    .btn-back {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      width: 2rem;
      height: 2rem;
      border-radius: 50%;
      padding: 0;
    }

    .search-container {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }

    .search-input {
      flex: 1;
      min-width: 250px;
      padding: 0.6rem 1rem;
      border: 1px solid var(--light-gray);
      border-radius: var(--border-radius);
      transition: var(--transition);
    }

    .search-input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }

    .form-control {
      width: 100%;
      padding: 0.6rem 1rem;
      border: 1px solid var(--light-gray);
      border-radius: var(--border-radius);
      transition: var(--transition);
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      margin: 1rem 0;
    }

    .table th {
      background-color: var(--primary-light);
      color: var(--primary-dark);
      padding: 0.75rem 1rem;
      text-align: left;
      font-weight: 600;
    }

    .table td {
      padding: 0.75rem 1rem;
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

    .alert {
      padding: 1rem;
      border-radius: var(--border-radius);
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .alert i {
      font-size: 1.2em;
    }

    .alert-success {
      background-color: var(--success-light);
      color: #0c5460;
      border-left: 4px solid var(--success);
    }

    .alert-danger {
      background-color: var(--danger-light);
      color: #721c24;
      border-left: 4px solid var(--danger);
    }

    .alert-warning {
      background-color: var(--warning-light);
      color: #856404;
      border-left: 4px solid var(--warning);
    }

    .alert-info {
      background-color: #e7f5ff;
      color: #0c5460;
      border-left: 4px solid #4cb5f5;
    }

    .pagination {
      display: flex;
      gap: 0.5rem;
      margin-top: 1.5rem;
      justify-content: center;
    }

    .page-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 2.5rem;
      height: 2.5rem;
      border-radius: 50%;
      background-color: white;
      color: var(--dark);
      border: 1px solid var(--light-gray);
      text-decoration: none;
      transition: var(--transition);
    }

    .page-link:hover {
      background-color: var(--primary-light);
      color: var(--primary);
      border-color: var(--primary);
    }

    .page-link.active {
      background-color: var(--primary);
      color: white;
      border-color: var(--primary);
    }

    .stats {
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: var(--gray);
    }

    /* Dark mode */
    body.dark-mode {
      background-color: #1a1a2e;
      color: #f8f9fa;
    }

    body.dark-mode .card {
      background-color: #16213e;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
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

    body.dark-mode .search-input,
    body.dark-mode .form-control {
      background-color: #1f4068;
      border-color: #1f4068;
      color: white;
    }

    body.dark-mode .card-footer {
      background-color: #1f4068;
      border-top-color: #1f4068;
    }

    body.dark-mode .page-link {
      background-color: #1f4068;
      border-color: #1f4068;
      color: white;
    }

    body.dark-mode .page-link:hover {
      background-color: var(--primary);
    }

    @media (max-width: 768px) {
      .search-container {
        flex-direction: column;
      }
      
      .header-title h2 {
        font-size: 1.2rem;
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
          <a href="dashboard.php" class="btn btn-back" title="Retour au tableau de bord">
            <i class="fas fa-arrow-left"></i>
          </a>
          <h2><i class="fas fa-door-open"></i> Gestion des Classes</h2>
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
        
        <div class="search-container">
          <form method="GET" style="flex: 1; display: flex; gap: 10px;">
            <input type="text" name="search" class="search-input" placeholder="Rechercher par nom ou niveau..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-search"></i> Rechercher
            </button>
          </form>
          
          <button type="button" class="btn btn-success" onclick="document.getElementById('addClassForm').style.display='block'">
            <i class="fas fa-plus"></i> Nouvelle classe
          </button>
        </div>

        <!-- Formulaire d'ajout (caché par défaut) -->
        <div id="addClassForm" style="display: none; margin: 20px 0; padding: 20px; background: var(--light); border-radius: var(--border-radius-lg);">
          <form method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
              <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Nom de la classe</label>
                <input type="text" name="nom" class="form-control" placeholder="Ex: 3ème A" required>
              </div>
              <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Niveau</label>
                <input type="text" name="niveau" class="form-control" placeholder="Ex: 3ème" required>
              </div>
            </div>
            <div style="display: flex; gap: 10px;">
              <button type="submit" name="add" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
              </button>
              <button type="button" class="btn btn-secondary" onclick="document.getElementById('addClassForm').style.display='none'">
                <i class="fas fa-times"></i> Annuler
              </button>
            </div>
          </form>
        </div>
        
        <?php if (!empty($classes)): ?>
        <div style="overflow-x: auto;">
          <table class="table">
            <thead>
              <tr>
                <th>Nom</th>
                <th>Niveau</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($classes as $classe): ?>
              <tr>
                <td><?= htmlspecialchars($classe['nom']) ?></td>
                <td><?= htmlspecialchars($classe['niveau']) ?></td>
                <td>
                  <div class="actions">
                    <a href="voir_classe.php?id=<?= $classe['id'] ?>" class="btn btn-primary btn-sm" title="Voir détails">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="modifier_classe.php?id=<?= $classe['id'] ?>" class="btn btn-warning btn-sm" title="Modifier">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="classes.php?delete=<?= $classe['id'] ?>" class="btn btn-danger btn-sm" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette classe ?')">
                      <i class="fas fa-trash-alt"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
          <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="page-link" title="Première page">
              <i class="fas fa-angle-double-left"></i>
            </a>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="page-link" title="Page précédente">
              <i class="fas fa-angle-left"></i>
            </a>
          <?php endif; ?>
          
          <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="page-link <?= $i == $page ? 'active' : '' ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>
          
          <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="page-link" title="Page suivante">
              <i class="fas fa-angle-right"></i>
            </a>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>" class="page-link" title="Dernière page">
              <i class="fas fa-angle-double-right"></i>
            </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
          <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Aucune classe trouvée. <?= !empty($search) ? 'Essayez avec un autre terme de recherche.' : '' ?>
          </div>
        <?php endif; ?>
      </div>
      
      <div class="card-footer">
        <div class="stats">
          <div>
            Affichage de <strong><?= count($classes) ?></strong> sur <strong><?= $total ?></strong> classes
          </div>
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