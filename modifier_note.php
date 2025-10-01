<?php

require_once 'config.php';
require_once 'functions.php';

// Vérifier l'ID de la note
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: notes.php');
    exit();
}

$id = $_GET['id'];

// Récupérer la note à modifier
$stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
$stmt->execute([$id]);
$note = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$note) {
    header('Location: notes.php');
    exit();
}

// Récupérer la liste des élèves et matières
$eleves = $pdo->query("SELECT id, nom, prenom FROM eleves ORDER BY nom")->fetchAll();
$matieres = $pdo->query("SELECT id, nom FROM matieres ORDER BY nom")->fetchAll();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eleve_id = $_POST['eleve_id'];
    $matiere_id = $_POST['matiere_id'];
    $periode = $_POST['periode'];
    $note_value = $_POST['note'];
     $note_composition = $_POST['note_composition'] ?? null;
    $commentaire = $_POST['commentaire'] ?? null;

    // Validation
    if ($eleve_id && $matiere_id && $periode && is_numeric($note_value) && $note_value >= 0 && $note_value <= 20) {
        $stmt = $pdo->prepare("
            UPDATE notes SET 
                eleve_id = ?, 
                matiere_id = ?, 
                periode = ?, 
                note = ?, 
                note_composition = ?, 
                commentaire = ?
            WHERE id = ?
        ");
        $stmt->execute([$eleve_id, $matiere_id, $periode, $note_value,$note_composition, $commentaire, $id]);
        
        header('Location: voir_note.php?id=' . $id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une note</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 
        * NOTE DE COMPOSITION :
        * Ce fichier permet de modifier une note existante avec :
        * - Un formulaire pré-rempli avec les données actuelles
        * - La possibilité de changer l'élève, la matière, la période, la note et le commentaire
        * - Une validation côté serveur pour s'assurer que la note est entre 0 et 20
        * - Un style cohérent avec le reste de l'application
        */
        
        /* Styles similaires à voir_note.php */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            color: white;
        }
        
        .btn i {
            margin-right: 5px;
        }
        
        .btn-back {
            background-color: #6c757d;
        }
        
        .btn-back:hover {
            background-color: #5a6268;
        }
        
        .card {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #495057;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 16px;
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn-primary {
            background-color: #4361ee;
        }
        
        .btn-primary:hover {
            background-color: #3a56d4;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-edit"></i> Modifier une note</h1>
            <a href="notes.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
        
        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label for="eleve_id">Élève</label>
                    <select id="eleve_id" name="eleve_id" required>
                        <?php foreach ($eleves as $eleve): ?>
                        <option value="<?= $eleve['id'] ?>" <?= $eleve['id'] == $note['eleve_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($eleve['prenom'] . ' ' . htmlspecialchars($eleve['nom'])) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="matiere_id">Matière</label>
                    <select id="matiere_id" name="matiere_id" required>
                        <?php foreach ($matieres as $matiere): ?>
                        <option value="<?= $matiere['id'] ?>" <?= $matiere['id'] == $note['matiere_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($matiere['nom']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="periode">Période</label>
                    <input type="text" id="periode" name="periode" value="<?= htmlspecialchars($note['periode']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="note">Note (/20)</label>
                    <input type="number" id="note" name="note" min="0" max="20" step="0.01" 
                           value="<?= htmlspecialchars($note['note']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="note">Note De Compo (/20)</label>
                    <input type="number" id="note" name="note" min="0" max="20" step="0.01" 
                           value="<?= htmlspecialchars($note['note_composition']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="commentaire">Commentaire (optionnel)</label>
                    <textarea id="commentaire" name="commentaire"><?= htmlspecialchars($note['commentaire'] ?? '') ?></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="notes.php" class="btn btn-back">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>