<?php
require 'config.php';

// Récupérer la liste des élèves et des matières
$eleves = $pdo->query("SELECT id, nom, prenom FROM eleves")->fetchAll(PDO::FETCH_ASSOC);
$matieres = $pdo->query("SELECT id, nom, coefficient, niveau FROM matieres")->fetchAll(PDO::FETCH_ASSOC);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eleve_id = $_POST['eleve_id'];
    $matiere_id = $_POST['matiere_id'];
    $periode = $_POST['periode'];
    $note = $_POST['note'];

    // Vérification que l'élève et la matière existent
    $checkEleve = $pdo->prepare("SELECT COUNT(*) FROM eleves WHERE id = ?");
    $checkEleve->execute([$eleve_id]);
    $checkMatiere = $pdo->prepare("SELECT COUNT(*) FROM matieres WHERE id = ?");
    $checkMatiere->execute([$matiere_id]);

    if ($checkEleve->fetchColumn() == 0) {
        $message = "Élève invalide.";
    } elseif ($checkMatiere->fetchColumn() == 0) {
        $message = "Matière invalide.";
    } else {
        $sql = "INSERT INTO notes (eleve_id, matiere_id, periode, note)
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$eleve_id, $matiere_id, $periode, $note]);
        $message = "Note enregistrée.";
    }
}

// Insertion des matières par défaut si la table est vide
$checkMatieres = $pdo->query("SELECT COUNT(*) FROM matieres")->fetchColumn();
if ($checkMatieres == 0) {
    $pdo->exec("INSERT INTO matieres (nom, coefficient, niveau) VALUES
                ('Math', 1, 'Tout'),
                ('Physique-Chimie', 1, 'Tout'),
                ('Bio', 1, 'Tout'),
                ('Rédaction', 1, 'Tout')");
    $matieres = $pdo->query("SELECT id, nom, coefficient, niveau FROM matieres")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une note</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f7f7f7; font-family: Arial, sans-serif; }
        .container { max-width: 400px; margin: 60px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #ccc; padding: 30px; }
        h2 { text-align: center; color: #3498db; margin-bottom: 25px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; color: #34495e; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; background: #3498db; color: #fff; border: none; padding: 10px; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background: #217dbb; }
        .message { text-align: center; color: #27ae60; margin-bottom: 15px; }
        .error { text-align: center; color: #e74c3c; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-plus"></i> Ajouter une note</h2>
    <?php if ($message): ?>
        <div class="<?= strpos($message, 'invalide') !== false ? 'error' : 'message' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="eleve_id">Élève</label>
            <select name="eleve_id" id="eleve_id" required>
                <option value="">-- Sélectionnez --</option>
                <?php foreach ($eleves as $eleve): ?>
                    <option value="<?= $eleve['id'] ?>">
                        <?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="matiere_id">Matière</label>
            <select name="matiere_id" id="matiere_id" required>
                <option value="">-- Sélectionnez --</option>
                <?php foreach ($matieres as $matiere): ?>
                    <option value="<?= $matiere['id'] ?>">
                        <?= htmlspecialchars($matiere['nom']) ?> (Coef: <?= $matiere['coefficient'] ?>, Niveau: <?= htmlspecialchars($matiere['niveau']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="periode">Période</label>
            <input name="periode" id="periode" placeholder="Trimestre 1, etc." required>
        </div>
        <div class="form-group">
            <label for="note">Note</label>
            <input type="number" step="0.01" name="note" id="note" required>
        </div>
        <button type="submit"><i class="fas fa-save"></i> Ajouter</button>
    </form>
</div>
</body>
</html>
