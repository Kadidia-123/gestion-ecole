<?php
require 'config.php';

// Vérifier si l'ID de la note à modifier est présent
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: bulletin.php");
    exit();
}

$note_id = $_GET['id'];

// Récupérer les données de la note à modifier
$stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
$stmt->execute([$note_id]);
$note = $stmt->fetch();

if (!$note) {
    header("Location: bulletin.php");
    exit();
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE notes 
                              SET eleve_id = ?, matiere_id = ?, periode = ?, 
                                  note = ?, note_composition = ?, 
                                  note_classe = ?, commentaire = ?
                              WHERE id = ?");
        
        $stmt->execute([
            $_POST['eleve_id'],
            $_POST['matiere_id'],
            $_POST['periode'],
            $_POST['note'],
            $_POST['note_composition'],
            $_POST['note_classe'],
            $_POST['commentaire'],
            $note_id
        ]);
        
        $message = "Bulletin modifié avec succès!";
        // Rafraîchir les données après modification
        $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
        $stmt->execute([$note_id]);
        $note = $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Erreur lors de la modification : " . $e->getMessage();
    }
}

// Récupérer les listes pour les menus déroulants
$eleves = $pdo->query("SELECT id, nom, prenom FROM eleves ORDER BY nom")->fetchAll();
$matieres = $pdo->query("SELECT id, nom FROM matieres ORDER BY nom")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Bulletin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        h2 { color: #2c3e50; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #3498db; color: white; }
        .btn-danger { background: #e74c3c; color: white; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .form-actions { margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-edit"></i> Modifier Bulletin</h2>
    
    <?php if (isset($message)): ?>
        <div class="message success"><?= $message ?></div>
    <?php elseif (isset($error)): ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="eleve_id">Élève</label>
            <select name="eleve_id" id="eleve_id" required>
                <option value="">Sélectionner un élève</option>
                <?php foreach ($eleves as $eleve): ?>
                    <option value="<?= $eleve['id'] ?>" <?= $note['eleve_id'] == $eleve['id'] ? 'selected' : '' ?>>
                        <?= $eleve['nom'] ?> <?= $eleve['prenom'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="matiere_id">Matière</label>
            <select name="matiere_id" id="matiere_id" required>
                <option value="">Sélectionner une matière</option>
                <?php foreach ($matieres as $matiere): ?>
                    <option value="<?= $matiere['id'] ?>" <?= $note['matiere_id'] == $matiere['id'] ? 'selected' : '' ?>>
                        <?= $matiere['nom'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="periode">Période</label>
            <input type="text" name="periode" id="periode" required value="<?= htmlspecialchars($note['periode']) ?>">
        </div>

        <div class="form-group">
            <label for="note">Note principale</label>
            <input type="number" step="0.01" min="0" max="20" name="note" id="note" required 
                   value="<?= $note['note'] ?>">
        </div>

        <div class="form-group">
            <label for="note_composition">Note de composition</label>
            <input type="number" step="0.01" min="0" max="20" name="note_composition" id="note_composition"
                   value="<?= $note['note_composition'] ?>">
        </div>

        <div class="form-group">
            <label for="note_classe">Note de classe</label>
            <input type="number" step="0.01" min="0" max="20" name="note_classe" id="note_classe"
                   value="<?= $note['note_classe'] ?>">
        </div>

        <div class="form-group">
            <label for="commentaire">Commentaire</label>
            <textarea name="commentaire" id="commentaire"><?= htmlspecialchars($note['commentaire']) ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
            <a href="gestion_bulletin.php" class="btn btn-danger">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
</body>
</html>