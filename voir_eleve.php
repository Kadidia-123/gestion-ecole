<?php
require_once './config.php';
require_once './functions.php';

// Vérifier si l'ID de l'élève est présent
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlash('danger', 'ID élève invalide');
    header('Location: eleves.php');
    exit();
}

$eleveId = intval($_GET['id']);
$eleve = null;
$error = null;
$flash = getFlash();

try {
    // Récupérer les informations de l'élève
    $sql = "SELECT eleves.*, classes.nom AS classe_nom 
            FROM eleves 
            LEFT JOIN classes ON eleves.classe_id = classes.id
            WHERE eleves.id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $eleveId, PDO::PARAM_INT);
    $stmt->execute();
    $eleve = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$eleve) {
        setFlash('danger', 'Élève non trouvé');
        header('Location: eleves.php');
        exit();
    }
} catch (PDOException $e) {
    $error = "Erreur de base de données : " . $e->getMessage();
} catch (Exception $e) {
    $error = "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Détails Élève | SchoolAdmin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #4361ee;
      --primary-dark: #3a56d4;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --warning: #f8961e;
      --danger: #f72585;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
      --border-radius: 12px;
      --box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background-color: #f5f7ff;
      color: #333;
      line-height: 1.6;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
    }

    .card {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      overflow: hidden;
      margin-bottom: 30px;
    }

    .card-header {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      padding: 20px 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header-title {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .header-actions {
      display: flex;
      gap: 10px;
    }

    .card-header h2 {
      margin: 0;
      font-weight: 600;
      font-size: 1.5rem;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 10px 16px;
      border-radius: 8px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      border: none;
      font-size: 0.95rem;
    }

    .btn-sm {
      padding: 6px 12px;
      font-size: 0.85rem;
    }

    .btn-primary {
      background-color: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
    }

    .btn-success {
      background-color: var(--success);
      color: white;
    }

    .btn-warning {
      background-color: var(--warning);
      color: white;
    }

    .btn-danger {
      background-color: var(--danger);
      color: white;
    }

    .btn-secondary {
      background-color: var(--gray);
      color: white;
    }

    .btn-back {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .btn-back:hover {
      background: rgba(255, 255, 255, 0.3);
    }

    .alert {
      padding: 12px 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 500;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
    }

    .alert-info {
      background-color: #d1ecf1;
      color: #0c5460;
    }

    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
    }

    .profile-container {
      display: flex;
      flex-direction: column;
      gap: 30px;
      padding: 25px;
    }

    .profile-header {
      display: flex;
      gap: 30px;
      align-items: center;
    }

    .profile-photo {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid #e9ecef;
    }

    .profile-photo-placeholder {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      background-color: #e9ecef;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #adb5bd;
      font-size: 3rem;
      border: 5px solid #e9ecef;
    }

    .profile-info {
      flex: 1;
    }

    .profile-name {
      font-size: 1.8rem;
      font-weight: 600;
      margin-bottom: 5px;
      color: var(--dark);
    }

    .profile-meta {
      display: flex;
      gap: 20px;
      margin-bottom: 15px;
      color: var(--gray);
    }

    .profile-badge {
      background: #e3f2fd;
      color: #1976d2;
      padding: 6px 12px;
      border-radius: 20px;
      font-weight: 500;
      font-size: 0.9rem;
    }

    .profile-details {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }

    .detail-card {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 20px;
    }

    .detail-card h3 {
      margin-top: 0;
      margin-bottom: 15px;
      color: var(--primary);
      font-size: 1.2rem;
      border-bottom: 1px solid #dee2e6;
      padding-bottom: 10px;
    }

    .detail-row {
      display: flex;
      margin-bottom: 10px;
    }

    .detail-label {
      font-weight: 500;
      color: var(--gray);
      min-width: 120px;
    }

    .detail-value {
      flex: 1;
    }

    /* Dark mode */
    .dark-mode {
      background-color: #121212;
      color: #e0e0e0;
    }

    .dark-mode .card {
      background-color: #1e1e1e;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }

    .dark-mode .profile-name {
      color: #e0e0e0;
    }

    .dark-mode .detail-card {
      background-color: #2d2d2d;
    }

    .dark-mode .detail-card h3 {
      border-bottom-color: #444;
    }

    .dark-mode .profile-photo-placeholder {
      background-color: #333;
      color: #666;
    }

    @media (max-width: 768px) {
      .profile-header {
        flex-direction: column;
        text-align: center;
      }
      
      .profile-meta {
        justify-content: center;
      }
      
      .profile-actions {
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="card-header">
        <div class="header-title">
          <a href="eleves.php" class="btn btn-back" title="Retour à la liste">
            <i class="fas fa-arrow-left"></i>
          </a>
          <h2><i class="fas fa-user-graduate"></i> Détails de l'Élève</h2>
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
        
        <?php if ($eleve): ?>
        <div class="profile-container">
          <div class="profile-header">
            <?php 
            $photoUrl = !empty($eleve['photo']) ? getStudentPhotoUrl($eleve['photo']) : null;
            if ($photoUrl): ?>
              <img src="<?= htmlspecialchars($photoUrl) ?>" class="profile-photo" alt="Photo de <?= htmlspecialchars($eleve['prenom'] ?? '') ?>"
                   onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
              <div class="profile-photo-placeholder" style="display: none;">
                <i class="fas fa-user-graduate"></i>
              </div>
            <?php else: ?>
              <div class="profile-photo-placeholder">
                <i class="fas fa-user-graduate"></i>
              </div>
            <?php endif; ?>
            
            <div class="profile-info">
              <h1 class="profile-name"><?= htmlspecialchars(trim(($eleve['prenom'] ?? '') . ' ' . ($eleve['nom'] ?? ''))) ?></h1>
              
              <div class="profile-meta">
                <span><i class="fas fa-id-card"></i> <?= !empty($eleve['matricule']) ? htmlspecialchars($eleve['matricule']) : 'N/A' ?></span>
                <?php if (!empty($eleve['classe_nom'])): ?>
                  <span class="profile-badge">
                    <i class="fas fa-users"></i> <?= htmlspecialchars($eleve['classe_nom']) ?>
                  </span>
                <?php endif; ?>
              </div>
              
              <div class="profile-actions">
                <a href="modifier_eleves.php?id=<?= $eleve['id'] ?>" class="btn btn-warning">
                  <i class="fas fa-edit"></i> Modifier
                </a>
                <a href="supprimer_eleves.php?id=<?= $eleve['id'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élève ? Cette action est irréversible.')">
                  <i class="fas fa-trash-alt"></i> Supprimer
                </a>
              </div>
            </div>
          </div>
          
          <div class="profile-details">
            <div class="detail-card">
              <h3><i class="fas fa-info-circle"></i> Informations Personnelles</h3>
              
              <div class="detail-row">
                <span class="detail-label">Date de Naissance:</span>
                <span class="detail-value"><?= $eleve['date_naissance'] ? htmlspecialchars(date('d/m/Y', strtotime($eleve['date_naissance']))) : 'Non renseignée' ?></span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Lieu de Naissance:</span>
                <span class="detail-value"><?= !empty($eleve['lieu_naissance']) ? htmlspecialchars($eleve['lieu_naissance']) : 'Non renseigné' ?></span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Genre:</span>
                <span class="detail-value">
                  <?php 
                    if ($eleve['genre'] === 'M') echo 'Masculin';
                    elseif ($eleve['genre'] === 'F') echo 'Féminin';
                    else echo 'Non renseigné';
                  ?>
                </span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Nationalité:</span>
                <span class="detail-value"><?= !empty($eleve['nationalite']) ? htmlspecialchars($eleve['nationalite']) : 'Non renseignée' ?></span>
              </div>
            </div>
            
            <div class="detail-card">
              <h3><i class="fas fa-address-book"></i> Contact</h3>
              
              <div class="detail-row">
                <span class="detail-label">Adresse:</span>
                <span class="detail-value"><?= !empty($eleve['adresse']) ? htmlspecialchars($eleve['adresse']) : 'Non renseignée' ?></span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Téléphone:</span>
                <span class="detail-value"><?= !empty($eleve['telephone']) ? htmlspecialchars($eleve['telephone']) : 'Non renseigné' ?></span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value"><?= !empty($eleve['email']) ? htmlspecialchars($eleve['email']) : 'Non renseigné' ?></span>
              </div>
            </div>
            
            <div class="detail-card">
              <h3><i class="fas fa-school"></i> Scolarité</h3>
              
              <div class="detail-row">
                <span class="detail-label">Date d'inscription:</span>
                <span class="detail-value"><?= $eleve['date_inscription'] ? htmlspecialchars(date('d/m/Y', strtotime($eleve['date_inscription']))) : 'Non renseignée' ?></span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Statut:</span>
                <span class="detail-value">
                  <?php 
                    if ($eleve['statut'] === 'actif') echo '<span style="color: green;">Actif</span>';
                    elseif ($eleve['statut'] === 'inactif') echo '<span style="color: red;">Inactif</span>';
                    else echo 'Non renseigné';
                  ?>
                </span>
              </div>
              
              <?php if (!empty($eleve['remarques'])): ?>
              <div class="detail-row">
                <span class="detail-label">Remarques:</span>
                <span class="detail-value"><?= htmlspecialchars($eleve['remarques']) ?></span>
              </div>
              <?php endif; ?>
            </div>
            
            <?php if (!empty($eleve['nom_pere']) || !empty($eleve['nom_mere']) || !empty($eleve['tel_parent'])): ?>
            <div class="detail-card">
              <h3><i class="fas fa-users"></i> Parents</h3>
              
              <?php if (!empty($eleve['nom_pere'])): ?>
              <div class="detail-row">
                <span class="detail-label">Père:</span>
                <span class="detail-value"><?= htmlspecialchars($eleve['nom_pere']) ?></span>
              </div>
              <?php endif; ?>
              
              <?php if (!empty($eleve['nom_mere'])): ?>
              <div class="detail-row">
                <span class="detail-label">Mère:</span>
                <span class="detail-value"><?= htmlspecialchars($eleve['nom_mere']) ?></span>
              </div>
              <?php endif; ?>
              
              <?php if (!empty($eleve['tel_parent'])): ?>
              <div class="detail-row">
                <span class="detail-label">Tél. Parents:</span>
                <span class="detail-value"><?= htmlspecialchars($eleve['tel_parent']) ?></span>
              </div>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    // Gestion du thème sombre
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    
    // Vérifier le thème stocké
    if (localStorage.getItem('darkMode') === 'true') {
      body.classList.add('dark-mode');
      themeToggle.innerHTML = '<i class="fas fa-sun"></i> Thème clair';
    }
    
    // Basculer le thème
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