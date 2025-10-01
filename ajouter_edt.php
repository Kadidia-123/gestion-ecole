<?php
require 'config.php';

// Récupération des listes pour les sélecteurs (à adapter selon tes tables)
$classes = $pdo->query("SELECT id, nom FROM classes")->fetchAll(PDO::FETCH_ASSOC);
$matieres = $pdo->query("SELECT id, nom FROM matieres")->fetchAll(PDO::FETCH_ASSOC);
$enseignants = $pdo->query("SELECT id, nom FROM enseignants")->fetchAll(PDO::FETCH_ASSOC);

$message = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $classe_id = $_POST['classe_id'];
    $jour = $_POST['jour'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    $matiere_id = $_POST['matiere_id'];
    $enseignant_id = $_POST['enseignant_id'];

    $stmt = $pdo->prepare("INSERT INTO emplois_temps 
        (classe_id, jour, heure_debut, heure_fin, matiere_id, enseignant_id) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$classe_id, $jour, $heure_debut, $heure_fin, $matiere_id, $enseignant_id]);

    $message = "Cours ajouté.";
}

// Insertion des enseignants par défaut (à faire une seule fois)
// $pdo->exec("INSERT INTO enseignants (nom) VALUES
// ('Mme Traore'),
// ('M. Toure'),
// ('Mme fofana'),
// ('M. konate')");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un cours à l'emploi du temps</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f7f7f7; font-family: Arial, sans-serif; }
        .container { max-width: 500px; margin: 60px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #ccc; padding: 30px; }
        h2 { text-align: center; color: #3498db; margin-bottom: 25px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; color: #34495e; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; background: #3498db; color: #fff; border: none; padding: 10px; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background: #217dbb; }
        .message { text-align: center; color: #27ae60; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-calendar-plus"></i> Ajouter un cours</h2>
    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="classe_id">Classe</label>
            <select name="classe_id" id="classe_id" required>
                <option value="">-- Sélectionnez --</option>
                <?php foreach ($classes as $classe): ?>
                    <option value="<?= $classe['id'] ?>"><?= htmlspecialchars($classe['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="jour">Jour</label>
            <select name="jour" id="jour" required>
                <option value="">-- Sélectionnez --</option>
                <option>Lundi</option>
                <option>Mardi</option>
                <option>Mercredi</option>
                <option>Jeudi</option>
                <option>Vendredi</option>
                <option>Samedi</option>
            </select>
        </div>
        <div class="form-group">
            <label for="heure_debut">Heure début</label>
            <input type="time" name="heure_debut" id="heure_debut" required>
        </div>
        <div class="form-group">
            <label for="heure_fin">Heure fin</label>
            <input type="time" name="heure_fin" id="heure_fin" required>
        </div>
        <div class="form-group">
            <label for="matiere_id">Matière</label>
            <select name="matiere_id" id="matiere_id" required>
                <option value="">-- Sélectionnez --</option>
                <?php foreach ($matieres as $matiere): ?>
                    <option value="<?= $matiere['id'] ?>"><?= htmlspecialchars($matiere['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="enseignant_id">Enseignant</label>
            <select name="enseignant_id" id="enseignant_id" required>
                <option value="">-- Sélectionnez --</option>
                <?php foreach ($enseignants as $enseignant): ?>
                    <option value="<?= $enseignant['id'] ?>"><?= htmlspecialchars($enseignant['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit"><i class="fas fa-save"></i> Ajouter</button>
    </form>
</div>
</body>
</html>