<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Récupération des classes de 1 à 9
try {
    $stmt = $pdo->query("SELECT id, nom FROM classes WHERE niveau BETWEEN '1' AND '9' ORDER BY nom");
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    setFlash('error', 'Erreur lors de la récupération des classes');
    $classes = [];
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $classe_id = (int)($_POST['classe_id'] ?? 0);
    
    // Validation
    $errors = [];
    
    if (empty($nom)) $errors[] = "Le nom est requis";
    if (empty($prenom)) $errors[] = "Le prénom est requis";
    if (empty($date_naissance)) $errors[] = "La date de naissance est requise";
    if (!in_array($genre, ['M', 'F'])) $errors[] = "Genre invalide";
    if ($classe_id <= 0) $errors[] = "Classe invalide";
    
    // Gestion de la photo
    $photo_name = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['photo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $errors[] = "Format de photo invalide (seulement JPG/PNG)";
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = "La photo est trop volumineuse (max 2MB)";
        } else {
            $photo_name = uniqid() . '.' . $ext;
            $upload_path = __DIR__ . '/uploads/eleves/' . $photo_name;
            
            if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                $errors[] = "Erreur lors de l'upload de la photo";
                $photo_name = null;
            }
        }
    }
    
    // Insertion en base si aucune erreur
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO eleves 
                                  (nom, prenom, date_naissance, genre, classe_id, photo, created_at) 
                                  VALUES (?, ?, ?, ?, ?, ?, NOW())");
            
            $stmt->execute([
                $nom,
                $prenom,
                $date_naissance,
                $genre,
                $classe_id,
                $photo_name
            ]);
            
            setFlash('success', 'Élève ajouté avec succès');
            header('Location: eleves.php');
            exit();
            
        } catch (PDOException $e) {
            // Supprimer la photo uploadée en cas d'erreur
            if ($photo_name && file_exists(__DIR__ . '/uploads/eleves/' . $photo_name)) {
                unlink(__DIR__ . '/uploads/eleves/' . $photo_name);
            }
            
            setFlash('error', 'Erreur lors de l\'ajout de l\'élève: ' . $e->getMessage());
        }
    } else {
        setFlash('error', implode('<br>', $errors));
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Élève | Gestion Scolaire</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1.5rem;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .form-control, .form-select {
            border-radius: 5px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-secondary {
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .alert {
            border-radius: 5px;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #555;
        }
        
        .container {
            max-width: 800px;
            margin-top: 3rem;
            margin-bottom: 3rem;
        }
        
        h2 {
            font-weight: 600;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }
            
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow-lg">
            <div class="card-header text-white">
                <h2><i class="fas fa-user-graduate me-2"></i>Ajouter un Nouvel Élève</h2>
            </div>
            
            <div class="card-body bg-white">
                <?php if ($flash = getFlash()): ?>
                    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show">
                        <?= $flash['message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" novalidate class="needs-validation">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required
                                       placeholder="Entrez le nom">
                                <label for="nom">Nom</label>
                                <div class="invalid-feedback">Veuillez entrer le nom de l'élève.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="prenom" name="prenom" 
                                       value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required
                                       placeholder="Entrez le prénom">
                                <label for="prenom">Prénom</label>
                                <div class="invalid-feedback">Veuillez entrer le prénom de l'élève.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="date_naissance" name="date_naissance" 
                                       value="<?= htmlspecialchars($_POST['date_naissance'] ?? '') ?>" required
                                       placeholder="Date de naissance">
                                <label for="date_naissance">Date de naissance</label>
                                <div class="invalid-feedback">Veuillez sélectionner une date valide.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="genre" name="genre" required>
                                    <option value="M" <?= ($_POST['genre'] ?? 'M') === 'M' ? 'selected' : '' ?>>Masculin</option>
                                    <option value="F" <?= ($_POST['genre'] ?? 'M') === 'F' ? 'selected' : '' ?>>Féminin</option>
                                </select>
                                <label for="genre">Genre</label>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-floating">
                                <select class="form-select" id="classe_id" name="classe_id" required>
                                    <option value="">Sélectionnez une classe</option>
                                    <?php foreach ($classes as $classe): ?>
                                        <option value="<?= (int)$classe['id'] ?>"
                                            <?= ($_POST['classe_id'] ?? '') == $classe['id'] ? 'selected' : '' ?>>
                                            Classe <?= htmlspecialchars($classe['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="classe_id">Classe</label>
                                <div class="invalid-feedback">Veuillez sélectionner une classe.</div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="photo" class="form-label">Photo de l'élève</label>
                                <input type="file" class="form-control" id="photo" name="photo" 
                                       accept="image/jpeg,image/png">
                                <div class="form-text">Formats acceptés: JPG, PNG (max 2MB)</div>
                            </div>
                        </div>
                        
                        <div class="col-12 d-flex justify-content-between mt-4">
                            <a href="eleves.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validation du formulaire
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>