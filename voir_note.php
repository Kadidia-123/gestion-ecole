<?php
require_once 'config.php';
require_once 'functions.php';

// Vérifier l'ID de la note
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: notes.php');
    exit();
}

$id = $_GET['id'];

// Récupérer la note avec les détails
$stmt = $pdo->prepare("
    SELECT n.id, n.note, n.note_composition, n.periode, n.commentaire,
           e.nom AS eleve_nom, e.prenom AS eleve_prenom,
           m.nom AS matiere_nom
    FROM notes n
    JOIN eleves e ON n.eleve_id = e.id
    JOIN matieres m ON n.matiere_id = m.id
    WHERE n.id = ?
");
$stmt->execute([$id]);
$note = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$note) {
    header('Location: notes.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la note</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        
        .card-body {
            margin-top: 20px;
        }
        
        .info-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .info-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
        }
        
        .notes-container {
            display: flex;
            gap: 30px;
            margin-top: 15px;
        }
        
        .note-section {
            flex: 1;
            padding: 15px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        
        .note-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #495057;
        }
        
        .note-value {
            font-size: 24px;
            font-weight: 700;
        }
        
        .main-note {
            color: #4361ee;
        }
        
        .comp-note {
            color: #3a0ca3;
        }
        
        .not-set {
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-book-open"></i> Détails de la note</h1>
            <a href="notes.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="info-item">
                    <div class="info-label">Élève</div>
                    <div class="info-value"><?= htmlspecialchars($note['eleve_prenom'] . ' ' . $note['eleve_nom']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Matière</div>
                    <div class="info-value"><?= htmlspecialchars($note['matiere_nom']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Période</div>
                    <div class="info-value"><?= htmlspecialchars($note['periode']) ?></div>
                </div>
                
                <div class="notes-container">
                    <div class="note-section">
                        <div class="note-title">Note Principale</div>
                        <div class="note-value main-note">
                            <?= htmlspecialchars($note['note']) ?>/20
                        </div>
                    </div>
                    
                    <div class="note-section">
                        <div class="note-title">Note de Composition</div>
                        <div class="note-value comp-note">
                            <?php 
                            if (array_key_exists('note_composition', $note) && $note['note_composition'] !== null) {
                                echo htmlspecialchars($note['note_composition']) . '/20';
                            } else {
                                echo '<span class="not-set">Non notée</span>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($note['commentaire'])): ?>
                <div class="info-item">
                    <div class="info-label">Commentaire</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($note['commentaire'])) ?></div>
                </div>
                <?php endif; ?>
                
                <div style="margin-top: 30px; display: flex; gap: 10px;">
                    <a href="modifier_note.php?id=<?= $note['id'] ?>" class="btn" style="background-color: #ffc107;">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="supprimer_note.php?id=<?= $note['id'] ?>" class="btn" style="background-color: #dc3545;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette note ?')">
                        <i class="fas fa-trash"></i> Supprimer
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>