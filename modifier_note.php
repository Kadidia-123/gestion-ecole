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
$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eleve_id = $_POST['eleve_id'] ?? '';
    $matiere_id = $_POST['matiere_id'] ?? '';
    $periode = $_POST['periode'] ?? '';
    $note_value = !empty($_POST['note']) ? $_POST['note'] : null;
    $note_composition = !empty($_POST['note_composition']) ? $_POST['note_composition'] : null;
    $note_classe = !empty($_POST['note_classe']) ? $_POST['note_classe'] : null;
    $commentaire = $_POST['commentaire'] ?? '';
    $annee_scolaire = $_POST['annee_scolaire'] ?? date('Y') . '-' . (date('Y') + 1);

    // Validation
    $errors = [];
    if (empty($eleve_id)) $errors[] = "L'élève est requis";
    if (empty($matiere_id)) $errors[] = "La matière est requise";
    if (empty($periode)) $errors[] = "La période est requise";
    
    // Validation des notes (optionnelles mais doivent être entre 0 et 20 si renseignées)
    if ($note_value !== null && (!is_numeric($note_value) || $note_value < 0 || $note_value > 20)) {
        $errors[] = "La note principale doit être entre 0 et 20";
    }
    if ($note_composition !== null && (!is_numeric($note_composition) || $note_composition < 0 || $note_composition > 20)) {
        $errors[] = "La note de composition doit être entre 0 et 20";
    }
    if ($note_classe !== null && (!is_numeric($note_classe) || $note_classe < 0 || $note_classe > 20)) {
        $errors[] = "La note de classe doit être entre 0 et 20";
    }

    if (empty($errors)) {
        try {
            // Log pour débogage
            error_log("DEBUG modifier_note.php - ID note actuelle: " . $id);
            error_log("DEBUG modifier_note.php - Élève: $eleve_id, Matière: $matiere_id, Période: $periode, Année: $annee_scolaire");
            
            // Vérifier les valeurs actuelles de la note dans la DB
            $current_note_stmt = $pdo->prepare("SELECT eleve_id, matiere_id, periode, annee_scolaire FROM notes WHERE id = ?");
            $current_note_stmt->execute([$id]);
            $current_note = $current_note_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($current_note) {
                error_log("DEBUG modifier_note.php - Valeurs actuelles en DB: Élève={$current_note['eleve_id']}, Matière={$current_note['matiere_id']}, Période={$current_note['periode']}, Année={$current_note['annee_scolaire']}");
                
                // Si toutes les valeurs sont identiques, on peut modifier directement sans vérifier les doublons
                $is_same = (
                    (int)$current_note['eleve_id'] == (int)$eleve_id &&
                    (int)$current_note['matiere_id'] == (int)$matiere_id &&
                    trim($current_note['periode']) === trim($periode) &&
                    trim($current_note['annee_scolaire']) === trim($annee_scolaire)
                );
                
                if ($is_same) {
                    error_log("DEBUG modifier_note.php - Les valeurs sont identiques, modification autorisée");
                    // Pas besoin de vérifier les doublons, on modifie directement
                } else {
                    // Vérifier si une autre note existe déjà avec les mêmes critères (en excluant la note actuelle)
                    $check_sql = "SELECT id FROM notes 
                                  WHERE eleve_id = ? 
                                  AND matiere_id = ? 
                                  AND periode = ? 
                                  AND annee_scolaire = ? 
                                  AND id != ?";
                    $check_stmt = $pdo->prepare($check_sql);
                    $check_stmt->execute([$eleve_id, $matiere_id, $periode, $annee_scolaire, $id]);
                    $existing_note = $check_stmt->fetch();

                    if ($existing_note) {
                        error_log("DEBUG modifier_note.php - Doublon trouvé avec ID: " . $existing_note['id']);
                        $error = "Une note existe déjà pour cette période et cette année scolaire! (ID du doublon: " . $existing_note['id'] . ")";
                    }
                }
            }
            
            if (!isset($error)) {
                $stmt = $pdo->prepare("
                    UPDATE notes SET 
                        eleve_id = ?, 
                        matiere_id = ?, 
                        periode = ?, 
                        note = ?, 
                        note_composition = ?, 
                        note_classe = ?,
                        commentaire = ?,
                        annee_scolaire = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $eleve_id, 
                    $matiere_id, 
                    $periode, 
                    $note_value, 
                    $note_composition, 
                    $note_classe,
                    $commentaire, 
                    $annee_scolaire,
                    $id
                ]);
                
                setFlash('success', 'Note modifiée avec succès');
                // Rediriger vers notes.php si voir_note.php n'existe pas
                if (file_exists(__DIR__ . '/voir_note.php')) {
                    header('Location: voir_note.php?id=' . $id);
                } else {
                    header('Location: notes.php');
                }
                exit();
            }
        } catch (Exception $e) {
            $error = "Erreur lors de la modification : " . $e->getMessage();
        }
    } else {
        $error = implode('<br>', $errors);
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
            <?php if (isset($error)): ?>
                <div style="padding: 15px; margin: 20px; background-color: #f8d7da; color: #721c24; border-radius: 4px; border-left: 4px solid #dc3545;">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                    <?php if (isset($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                        <div style="margin-top: 10px; padding: 10px; background-color: #fff3cd; border-radius: 4px; font-size: 0.9em;">
                            <strong>Debug info:</strong><br>
                            ID note: <?= htmlspecialchars($id ?? 'N/A') ?><br>
                            Élève: <?= htmlspecialchars($_POST['eleve_id'] ?? 'N/A') ?><br>
                            Matière: <?= htmlspecialchars($_POST['matiere_id'] ?? 'N/A') ?><br>
                            Période: <?= htmlspecialchars($_POST['periode'] ?? 'N/A') ?><br>
                            Année: <?= htmlspecialchars($_POST['annee_scolaire'] ?? 'N/A') ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($flash = getFlash()): ?>
                <div style="padding: 15px; margin: 20px; background-color: <?= $flash['type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $flash['type'] === 'success' ? '#155724' : '#721c24' ?>; border-radius: 4px; border-left: 4px solid <?= $flash['type'] === 'success' ? '#28a745' : '#dc3545' ?>;">
                    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i> <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="eleve_id">Élève</label>
                    <select id="eleve_id" name="eleve_id" required>
                        <?php foreach ($eleves as $eleve): ?>
                        <option value="<?= $eleve['id'] ?>" <?= $eleve['id'] == $note['eleve_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars(trim(($eleve['prenom'] ?? '') . ' ' . ($eleve['nom'] ?? ''))) ?>
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
                    <label for="note">Note principale (/20)</label>
                    <input type="number" id="note" name="note" min="0" max="20" step="0.01" 
                           value="<?= htmlspecialchars($note['note'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="note_composition">Note de composition (/20)</label>
                    <input type="number" id="note_composition" name="note_composition" min="0" max="20" step="0.01" 
                           value="<?= htmlspecialchars($note['note_composition'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="note_classe">Note de classe (/20)</label>
                    <input type="number" id="note_classe" name="note_classe" min="0" max="20" step="0.01" 
                           value="<?= htmlspecialchars($note['note_classe'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="annee_scolaire">Année scolaire</label>
                    <input type="text" id="annee_scolaire" name="annee_scolaire" 
                           value="<?= htmlspecialchars($note['annee_scolaire'] ?? date('Y') . '-' . (date('Y') + 1)) ?>" required>
                    <small style="color: #6c757d;">Format: 2024-2025</small>
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