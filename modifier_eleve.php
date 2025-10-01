<?php
require_once './config.php';
require_once './functions.php';

// Vérifier si l'ID de l'élève est présent
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlash('danger', 'ID élève invalide');
    header('Location: eleve.php');
    exit();
}

$eleveId = intval($_GET['id']);
$error = null;
$success = false;
$flash = getFlash();

// Récupérer les classes pour le select
$classes = [];
try {
    $stmt = $pdo->query("SELECT id, nom FROM classes ORDER BY nom");
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des classes : " . $e->getMessage();
}

// Récupérer les informations actuelles de l'élève
try {
    $stmt = $pdo->prepare("SELECT * FROM eleves WHERE id = ?");
    $stmt->execute([$eleveId]);
    $eleve = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$eleve) {
        setFlash('danger', 'Élève non trouvé');
        header('Location: eleve.php');
        exit();
    }
} catch (PDOException $e) {
    $error = "Erreur de base de données : " . $e->getMessage();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupération et validation des données
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $matricule = trim($_POST['matricule']);
        $dateNaissance = !empty($_POST['date_naissance']) ? $_POST['date_naissance'] : null;
        $lieuNaissance = trim($_POST['lieu_naissance']);
        $sexe = $_POST['genre'];
        $nationalite = trim($_POST['nationalite']);
        $adresse = trim($_POST['adresse']);
        $telephone = trim($_POST['telephone']);
        $email = trim($_POST['email']);
        $classeId = !empty($_POST['classe_id']) ? $_POST['classe_id'] : null;
        $statut = $_POST['statut'];
        $nomPere = trim($_POST['nom_pere']);
        $nomMere = trim($_POST['nom_mere']);
        $telParent = trim($_POST['tel_parent']);
        $remarques = trim($_POST['remarques']);

        // Validation minimale
        if (empty($nom) || empty($prenom) || empty($matricule)) {
            throw new Exception("Les champs Nom, Prénom et Matricule sont obligatoires");
        }

        // Gestion de l'upload de photo
        $photo = $eleve['photo'];
        if (!empty($_FILES['photo']['name'])) {
            $uploadDir = 'uploads/eleves/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
            $targetPath = $uploadDir . $fileName;

            // Vérifier le type de fichier
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['photo']['type'];
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Seuls les fichiers JPEG, PNG et GIF sont autorisés");
            }

            // Déplacer le fichier uploadé
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                // Supprimer l'ancienne photo si elle existe
                if (!empty($photo) && file_exists($photo)) {
                    unlink($photo);
                }
                $photo = $targetPath;
            } else {
                throw new Exception("Erreur lors de l'upload de la photo");
            }
        }

        // Mise à jour dans la base de données
        $sql = "UPDATE eleves SET 
                nom = :nom,
                prenom = :prenom,
                matricule = :matricule,
                date_naissance = :date_naissance,
                lieu_naissance = :lieu_naissance,
                genre = :genre,
                nationalite = :nationalite,
                adresse = :adresse,
                telephone = :telephone,
                email = :email,
                classe_id = :classe_id,
                statut = :statut,
                nom_pere = :nom_pere,
                nom_mere = :nom_mere,
                tel_parent = :tel_parent,
                remarques = :remarques,
                photo = :photo
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':matricule' => $matricule,
            ':date_naissance' => $dateNaissance,
            ':lieu_naissance' => $lieuNaissance,
            ':genre' => $genre,
            ':nationalite' => $nationalite,
            ':adresse' => $adresse,
            ':telephone' => $telephone,
            ':email' => $email,
            ':classe_id' => $classeId,
            ':statut' => $statut,
            ':nom_pere' => $nomPere,
            ':nom_mere' => $nomMere,
            ':tel_parent' => $telParent,
            ':remarques' => $remarques,
            ':photo' => $photo,
            ':id' => $eleveId
        ]);

        setFlash('success', 'Élève mis à jour avec succès');
        header("Location: voir_eleve.php?id=$eleveId");
        exit();
    } catch (Exception $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier Élève | SchoolAdmin</title>
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

    .form-container {
      padding: 25px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
    }

    .form-control {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 1rem;
      transition: border-color 0.2s;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary);
    }

    .form-select {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 1rem;
      background-color: white;
    }

    .photo-upload {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
      margin-bottom: 20px;
    }

    .photo-preview {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid #e9ecef;
    }

    .photo-placeholder {
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

    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
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

    .dark-mode .form-control,
    .dark-mode .form-select {
      background-color: #2d2d2d;
      border-color: #444;
      color: #e0e0e0;
    }

    .dark-mode .photo-placeholder {
      background-color: #333;
      color: #666;
    }

    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="card-header">
        <div class="header-title">
          <a href="voir_eleve.php?id=<?= $eleveId ?>" class="btn btn-back" title="Retour aux détails">
            <i class="fas fa-arrow-left"></i>
          </a>
          <h2><i class="fas fa-user-edit"></i> Modifier Élève</h2>
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
        <div class="form-container">
          <form method="POST" enctype="multipart/form-data">
            <div class="photo-upload">
              <?php if (!empty($eleve['photo'])): ?>
                <img src="<?= htmlspecialchars($eleve['photo']) ?>" id="photoPreview" class="photo-preview" alt="Photo de l'élève">
              <?php else: ?>
                <div id="photoPreview" class="photo-placeholder">
                  <i class="fas fa-user-graduate"></i>
                </div>
              <?php endif; ?>
              <input type="file" id="photoInput" name="photo" accept="image/*" style="display: none;">
              <button type="button" class="btn btn-secondary" onclick="document.getElementById('photoInput').click()">
                <i class="fas fa-camera"></i> Changer la photo
              </button>
            </div>

            <div class="form-grid">
              <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($eleve['nom']) ?>" required>
              </div>
              
              <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($eleve['prenom']) ?>" required>
              </div>
              
              <div class="form-group">
                <label for="matricule">Matricule *</label>
                <input type="text" id="matricule" name="matricule" class="form-control" value="<?= htmlspecialchars($eleve['matricule']) ?>" required>
              </div>
              
              <div class="form-group">
                <label for="date_naissance">Date de Naissance</label>
                <input type="date" id="date_naissance" name="date_naissance" class="form-control" value="<?= !empty($eleve['date_naissance']) ? htmlspecialchars($eleve['date_naissance']) : '' ?>">
              </div>
              
              <div class="form-group">
                <label for="lieu_naissance">Lieu de Naissance</label>
                <input type="text" id="lieu_naissance" name="lieu_naissance" class="form-control" value="<?= !empty($eleve['lieu_naissance']) ? htmlspecialchars($eleve['lieu_naissance']) : '' ?>">
              </div>
              
              <div class="form-group">
                <label for="genre">Genre</label>
                <select id="genre" name="genre" class="form-select">
                  <option value="">-- Sélectionner --</option>
                  <option value="M" <?= $eleve['genre'] === 'M' ? 'selected' : '' ?>>Masculin</option>
                  <option value="F" <?= $eleve['genre'] === 'F' ? 'selected' : '' ?>>Féminin</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="nationalite">Nationalité</label>
                <input type="text" id="nationalite" name="nationalite" class="form-control" value="<?= !empty($eleve['nationalite']) ? htmlspecialchars($eleve['nationalite']) : '' ?>">
              </div>
              
              <div class="form-group">
                <label for="adresse">Adresse</label>
                <textarea id="adresse" name="adresse" class="form-control" rows="2"><?= !empty($eleve['adresse']) ? htmlspecialchars($eleve['adresse']) : '' ?></textarea>
              </div>
              
              <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" class="form-control" value="<?= !empty($eleve['telephone']) ? htmlspecialchars($eleve['telephone']) : '' ?>">
              </div>
              
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= !empty($eleve['email']) ? htmlspecialchars($eleve['email']) : '' ?>">
              </div>
              
              <div class="form-group">
                <label for="classe_id">Classe</label>
                <select id="classe_id" name="classe_id" class="form-select">
                  <option value="">-- Non affecté --</option>
                  <?php foreach ($classes as $classe): ?>
                    <option value="<?= $classe['id'] ?>" <?= $eleve['classe_id'] == $classe['id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($classe['nom']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <div class="form-group">
                <label for="statut">Statut</label>
                <select id="statut" name="statut" class="form-select" required>
                  <option value="actif" <?= $eleve['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                  <option value="inactif" <?= $eleve['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="nom_pere">Nom du Père</label>
                <input type="text" id="nom_pere" name="nom_pere" class="form-control" value="<?= !empty($eleve['nom_pere']) ? htmlspecialchars($eleve['nom_pere']) : '' ?>">
              </div>
              
              <div class="form-group">
                <label for="nom_mere">Nom de la Mère</label>
                <input type="text" id="nom_mere" name="nom_mere" class="form-control" value="<?= !empty($eleve['nom_mere']) ? htmlspecialchars($eleve['nom_mere']) : '' ?>">
              </div>
              
              <div class="form-group">
                <label for="tel_parent">Téléphone Parent</label>
                <input type="tel" id="tel_parent" name="tel_parent" class="form-control" value="<?= !empty($eleve['tel_parent']) ? htmlspecialchars($eleve['tel_parent']) : '' ?>">
              </div>
            </div>
            
            <div class="form-group">
              <label for="remarques">Remarques</label>
              <textarea id="remarques" name="remarques" class="form-control" rows="3"><?= !empty($eleve['remarques']) ? htmlspecialchars($eleve['remarques']) : '' ?></textarea>
            </div>
            
            <div class="form-actions">
              <a href="voir_eleve.php?id=<?= $eleveId ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
              </button>
            </div>
          </form>
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

    // Prévisualisation de la photo
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    
    photoInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
          if (photoPreview.tagName === 'IMG') {
            photoPreview.src = event.target.result;
          } else {
            // Remplacer le placeholder par une image
            const img = document.createElement('img');
            img.id = 'photoPreview';
            img.className = 'photo-preview';
            img.src = event.target.result;
            photoPreview.replaceWith(img);
            photoPreview = img; // Mettre à jour la référence
          }
        };
        reader.readAsDataURL(file);
      }
    });
  </script>
</body>
</html>