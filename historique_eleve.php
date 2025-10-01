<?php
session_start();
require_once './config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ./index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: eleves.php');
    exit();
}

$eleveId = $_GET['id'];

// Récupérer les infos de l'élève
$sql = "SELECT e.*, c.nom as classe_nom FROM eleves e 
        LEFT JOIN classes c ON e.classe_id = c.id
        WHERE e.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$eleveId]);
$eleve = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$eleve) {
    $_SESSION['error'] = "Élève non trouvé";
    header('Location: eleves.php');
    exit();
}

// Récupérer l'historique
$sql = "SELECT h.*, u.nom as utilisateur_nom 
        FROM historique_eleves h
        LEFT JOIN utilisateurs u ON h.utilisateur_id = u.id
        WHERE h.eleve_id = ?
        ORDER BY h.date_modification DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$eleveId]);
$historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des modifications</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Historique des modifications</h1>
            <nav>
                <ul>
                    <li><a href="eleves.php">Retour à la liste</a></li>
                </ul>
            </nav>
        </header>
        
        <main>
            <div class="student-info">
                <h2><?= htmlspecialchars($eleve['prenom'] . ' ' . htmlspecialchars($eleve['nom'])) ?></h2>
                <p>Matricule: <?= htmlspecialchars($eleve['matricule']) ?></p>
                <p>Classe: <?= htmlspecialchars($eleve['classe_nom'] ?? 'Non affecté') ?></p>
            </div>
            
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Champ modifié</th>
                        <th>Ancienne valeur</th>
                        <th>Nouvelle valeur</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $entry): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($entry['date_modification'])) ?></td>
                        <td><?= htmlspecialchars($entry['utilisateur_nom']) ?></td>
                        <td><?= htmlspecialchars($entry['champ_modifie']) ?></td>
                        <td><?= htmlspecialchars($entry['ancienne_valeur']) ?></td>
                        <td><?= htmlspecialchars($entry['nouvelle_valeur']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($historique)): ?>
                    <tr>
                        <td colspan="5">Aucune modification enregistrée</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>