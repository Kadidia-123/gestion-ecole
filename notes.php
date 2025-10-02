<?php
require 'config.php';
session_start();

// Vérifier et créer la colonne annee_scolaire si elle n'existe pas
try {
    $check_column = $pdo->query("SHOW COLUMNS FROM notes LIKE 'annee_scolaire'")->fetch();
    if (!$check_column) {
        $pdo->exec("ALTER TABLE notes ADD COLUMN annee_scolaire VARCHAR(20) NOT NULL DEFAULT '2023-2024'");
        // Mettre à jour les enregistrements existants
        $current_year = date('Y');
        $default_annee = ($current_year - 1) . '-' . $current_year;
        $pdo->exec("UPDATE notes SET annee_scolaire = '$default_annee'");
    }
} catch (Exception $e) {
    // Continuer même si il y a une erreur
}

// Fonction pour obtenir la classe CSS d'une note
function getNoteClass($note) {
    if ($note === null || $note === '') return 'note-empty';
    if ($note >= 16) return 'note-excellent';
    if ($note >= 14) return 'note-good';
    if ($note >= 10) return 'note-average';
    return 'note-poor';
}

// Fonction pour obtenir le libellé de l'année
function getAnneeLabel($annee) {
    $annees = [
        1 => '1ère Année',
        2 => '2ème Année', 
        3 => '3ème Année',
        4 => '4ème Année',
        5 => '5ème Année',
        6 => '6ème Année',
        7 => '7ème Année',
        8 => '8ème Année',
        9 => '9ème Année'
    ];
    return $annees[$annee] ?? 'Année ' . $annee;
}

// AJOUTER une note
if ($_POST['action'] ?? '' == 'add_note') {
    $eleve_id = $_POST['eleve_id'] ?? '';
    $matiere_id = $_POST['matiere_id'] ?? '';
    $periode = $_POST['periode'] ?? '';
    $note = $_POST['note'] ?? null;
    $note_composition = $_POST['note_composition'] ?? null;
    $note_classe = $_POST['note_classe'] ?? null;
    $commentaire = $_POST['commentaire'] ?? '';
    $annee_scolaire = $_POST['annee_scolaire'] ?? date('Y');

    try {
        // Vérifier si une note existe déjà pour cette période
        $check_sql = "SELECT id FROM notes WHERE eleve_id = ? AND matiere_id = ? AND periode = ? AND annee_scolaire = ?";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$eleve_id, $matiere_id, $periode, $annee_scolaire]);
        $existing_note = $check_stmt->fetch();

        if ($existing_note) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Une note existe déjà pour cette période et cette année scolaire!'];
        } else {
            $sql = "INSERT INTO notes (eleve_id, matiere_id, periode, note, note_composition, note_classe, commentaire, annee_scolaire) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$eleve_id, $matiere_id, $periode, $note, $note_composition, $note_classe, $commentaire, $annee_scolaire]);
            
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Note ajoutée avec succès!'];
        }
    } catch (Exception $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Erreur lors de l\'ajout de la note: ' . $e->getMessage()];
    }
    
    header("Location: notes.php?classe=" . ($_GET['classe'] ?? '') . "&eleve_id=" . $eleve_id . "&matiere_id=" . $matiere_id . "&annee=" . $annee_scolaire);
    exit;
}

// MODIFIER une note
if ($_POST['action'] ?? '' == 'edit_note') {
    $note_id = $_POST['note_id'] ?? '';
    $eleve_id = $_POST['eleve_id'] ?? '';
    $matiere_id = $_POST['matiere_id'] ?? '';
    $note = $_POST['note'] ?? null;
    $note_composition = $_POST['note_composition'] ?? null;
    $note_classe = $_POST['note_classe'] ?? null;
    $commentaire = $_POST['commentaire'] ?? '';
    $annee_scolaire = $_POST['annee_scolaire'] ?? '';

    try {
        $sql = "UPDATE notes SET note = ?, note_composition = ?, note_classe = ?, commentaire = ?, annee_scolaire = ?
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$note, $note_composition, $note_classe, $commentaire, $annee_scolaire, $note_id]);
        
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Note modifiée avec succès!'];
    } catch (Exception $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Erreur lors de la modification: ' . $e->getMessage()];
    }
    
    header("Location: notes.php?classe=" . ($_GET['classe'] ?? '') . "&eleve_id=" . $eleve_id . "&matiere_id=" . $matiere_id . "&annee=" . $annee_scolaire);
    exit;
}

// SUPPRIMER une note
if ($_GET['action'] ?? '' == 'delete_note') {
    $note_id = $_GET['note_id'] ?? '';
    
    try {
        // Récupérer les informations de la note avant suppression
        $sql_info = "SELECT eleve_id, matiere_id, annee_scolaire FROM notes WHERE id = ?";
        $stmt_info = $pdo->prepare($sql_info);
        $stmt_info->execute([$note_id]);
        $note_info = $stmt_info->fetch();
        
        if ($note_info) {
            $sql = "DELETE FROM notes WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$note_id]);
            
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Note supprimée avec succès!'];
            
            header("Location: notes.php?classe=" . ($_GET['classe'] ?? '') . "&eleve_id=" . $note_info['eleve_id'] . "&matiere_id=" . $note_info['matiere_id'] . "&annee=" . $note_info['annee_scolaire']);
        } else {
            header("Location: notes.php");
        }
        exit;
    } catch (Exception $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Erreur lors de la suppression: ' . $e->getMessage()];
        header("Location: notes.php");
        exit;
    }
}

// Récupération des données
$classes = $pdo->query("SELECT id, nom, annee FROM classes ORDER BY annee, nom")->fetchAll();
$matieres = $pdo->query("SELECT id, nom FROM matieres ORDER BY nom")->fetchAll();
$periodes = ['1er Trimestre', '2ème Trimestre', '3ème Trimestre', 'Examen Final'];

// Générer les années scolaires (5 dernières années)
$current_year = date('Y');
$annees_scolaires = [];
for ($i = 0; $i < 5; $i++) {
    $year = $current_year - $i;
    $annees_scolaires[] = $year . '-' . ($year + 1);
}

$classe_id = $_GET['classe'] ?? '';
$eleve_id = $_GET['eleve_id'] ?? '';
$matiere_id = $_GET['matiere_id'] ?? '';
$annee_scolaire = $_GET['annee'] ?? $annees_scolaires[0];
$edit_note_id = $_GET['edit_note_id'] ?? '';

$eleves = [];
$notes = [];
$note_a_editer = null;
$classe_info = null;

if ($classe_id) {
    // Récupérer les informations de la classe
    $stmt = $pdo->prepare("SELECT id, nom, annee FROM classes WHERE id = ?");
    $stmt->execute([$classe_id]);
    $classe_info = $stmt->fetch();

    // Récupérer les élèves de la classe
    $stmt = $pdo->prepare("SELECT id, nom, prenom FROM eleves WHERE classe_id = ? ORDER BY nom, prenom");
    $stmt->execute([$classe_id]);
    $eleves = $stmt->fetchAll();
}

if ($eleve_id && $matiere_id && $annee_scolaire) {
    // Récupérer toutes les notes pour l'année scolaire
    $stmt = $pdo->prepare("SELECT n.*, m.nom as matiere_nom, e.nom as eleve_nom, e.prenom as eleve_prenom
                          FROM notes n
                          JOIN matieres m ON n.matiere_id = m.id
                          JOIN eleves e ON n.eleve_id = e.id
                          WHERE n.eleve_id = ? AND n.matiere_id = ? AND n.annee_scolaire = ?
                          ORDER BY 
                            CASE n.periode 
                                WHEN '1er Trimestre' THEN 1
                                WHEN '2ème Trimestre' THEN 2
                                WHEN '3ème Trimestre' THEN 3
                                WHEN 'Examen Final' THEN 4
                                ELSE 5
                            END");
    $stmt->execute([$eleve_id, $matiere_id, $annee_scolaire]);
    $notes = $stmt->fetchAll();

    // Récupérer la note à éditer si demandé
    if ($edit_note_id) {
        $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
        $stmt->execute([$edit_note_id]);
        $note_a_editer = $stmt->fetch();
    }
}

// Récupérer les informations de l'école
try {
    $ecole_info = $pdo->query("SELECT nom, adresse, ville, code_postal, logo FROM ecole LIMIT 1")->fetch();
} catch (Exception $e) {
    $ecole_info = [
        'nom' => 'École Primaire & Secondaire',
        'adresse' => 'Garantiguibougou',
        'ville' => 'Bamako',
        'code_postal' => '300 Kalaban',
        'logo' => ''
    ];
}

// Afficher les messages de session
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion des Notes - 1ère à 9ème Année</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #e74c3c, #f39c12, #2ecc71, #3498db);
        }

        .school-info h1 {
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .school-info p {
            opacity: 0.9;
            font-size: 14px;
        }

        .logo {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #2c3e50;
        }

        .nav {
            background: #34495e;
            padding: 15px 30px;
            display: flex;
            gap: 20px;
            border-bottom: 1px solid #4a6278;
        }

        .nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav a:hover, .nav a.active {
            background: #3498db;
        }

        .filters {
            padding: 25px 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        select, input, textarea {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: inherit;
        }

        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-sm {
            padding: 8px 15px;
            font-size: 12px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 5px solid;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left-color: #27ae60;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #e74c3c;
        }

        .form-container {
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 20px 30px;
            border-left: 5px solid #3498db;
        }

        .form-title {
            color: #2c3e50;
            font-size: 22px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .note-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .form-full {
            grid-column: 1 / -1;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .table-container {
            padding: 0 30px 30px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-title {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 700;
        }

        .notes-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .notes-table th {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            color: white;
            padding: 18px 15px;
            text-align: center;
            font-weight: 600;
            border: none;
        }

        .notes-table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ecf0f1;
            transition: background 0.3s;
        }

        .notes-table tr:hover td {
            background: #f8f9fa;
        }

        .notes-table tr:nth-child(even) {
            background: #f8fafc;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .note-value {
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 20px;
            color: white;
            display: inline-block;
            min-width: 60px;
        }

        .note-excellent { background: #27ae60; }
        .note-good { background: #3498db; }
        .note-average { background: #f39c12; }
        .note-poor { background: #e74c3c; }
        .note-empty { background: #95a5a6; }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px 30px;
            background: #f8f9fa;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin: 10px 0;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
        }

        .annee-badge {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .instructions {
            text-align: center;
            padding: 60px 30px;
            color: #7f8c8d;
        }

        .instructions i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .classe-info {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .classe-titre {
            font-size: 18px;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
                padding: 20px;
            }

            .nav {
                flex-direction: column;
                gap: 10px;
            }

            .filter-form {
                grid-template-columns: 1fr;
            }

            .notes-table {
                font-size: 14px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .note-form {
                grid-template-columns: 1fr;
            }

            .table-header {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .classe-info {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="school-info">
                <h1><?= htmlspecialchars($ecole_info['nom']) ?></h1>
                <p><?= htmlspecialchars($ecole_info['adresse']) ?> - <?= htmlspecialchars($ecole_info['code_postal'] . ' ' . $ecole_info['ville']) ?></p>
            </div>
            <div class="logo">
                <?php if (!empty($ecole_info['logo'])): ?>
                    <img src="<?= htmlspecialchars($ecole_info['logo']) ?>" alt="Logo" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                <?php else: ?>
                    <i class="fas fa-graduation-cap"></i>
                <?php endif; ?>
            </div>
        </div>

        <div class="nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Tableau de Bord</a>
            <a href="notes.php" class="active"><i class="fas fa-edit"></i> Gestion des Notes</a>
            <a href="bulletin.php"><i class="fas fa-scroll"></i> Bulletins</a>
            <a href="eleves.php"><i class="fas fa-users"></i> Élèves</a>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?= $message['type'] == 'error' ? 'error' : 'success' ?>">
                <i class="fas fa-<?= $message['type'] == 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <?= htmlspecialchars($message['text']) ?>
            </div>
        <?php endif; ?>

        <!-- Filtres -->
        <div class="filters">
            <form method="get" class="filter-form">
                <div class="form-group">
                    <label for="annee"><i class="fas fa-calendar-alt"></i> Année Scolaire</label>
                    <select name="annee" id="annee" onchange="this.form.submit()">
                        <?php foreach ($annees_scolaires as $annee): ?>
                            <option value="<?= htmlspecialchars($annee) ?>" <?= $annee_scolaire == $annee ? 'selected' : '' ?>>
                                <?= htmlspecialchars($annee) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="classe"><i class="fas fa-chalkboard"></i> Classe</label>
                    <select name="classe" id="classe" onchange="this.form.submit()">
                        <option value="">-- Sélectionnez une classe --</option>
                        <?php foreach ($classes as $classe): ?>
                            <option value="<?= htmlspecialchars($classe['id']) ?>" <?= $classe_id == $classe['id'] ? 'selected' : '' ?>>
                                <?= getAnneeLabel($classe['annee']) ?> - <?= htmlspecialchars($classe['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($classe_id): ?>
                <div class="form-group">
                    <label for="eleve_id"><i class="fas fa-user-graduate"></i> Élève</label>
                    <select name="eleve_id" id="eleve_id" onchange="this.form.submit()">
                        <option value="">-- Sélectionnez un élève --</option>
                        <?php foreach ($eleves as $e): ?>
                            <option value="<?= htmlspecialchars($e['id']) ?>" <?= $eleve_id == $e['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if ($eleve_id): ?>
                <div class="form-group">
                    <label for="matiere_id"><i class="fas fa-book"></i> Matière</label>
                    <select name="matiere_id" id="matiere_id" onchange="this.form.submit()">
                        <option value="">-- Sélectionnez une matière --</option>
                        <?php foreach ($matieres as $m): ?>
                            <option value="<?= htmlspecialchars($m['id']) ?>" <?= $matiere_id == $m['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if ($eleve_id && $matiere_id): ?>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync"></i> Actualiser
                    </button>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($classe_id && $classe_info): ?>
            <div class="classe-info">
                <div class="classe-titre">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <?= getAnneeLabel($classe_info['annee']) ?> - <?= htmlspecialchars($classe_info['nom']) ?>
                </div>
                <div class="annee-badge">
                    <i class="fas fa-calendar"></i>
                    Année Scolaire : <?= htmlspecialchars($annee_scolaire) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($eleve_id && $matiere_id && $annee_scolaire): 
            $stmt = $pdo->prepare("SELECT nom, prenom FROM eleves WHERE id = ?");
            $stmt->execute([$eleve_id]);
            $eleve_info = $stmt->fetch();

            $stmt = $pdo->prepare("SELECT nom FROM matieres WHERE id = ?");
            $stmt->execute([$matiere_id]);
            $matiere_info = $stmt->fetch();

            // Calculer les statistiques
            $moyenne_sql = "SELECT 
                ROUND(AVG(note), 2) as moyenne_principale,
                ROUND(AVG(note_composition), 2) as moyenne_composition,
                ROUND(AVG(note_classe), 2) as moyenne_classe,
                COUNT(*) as total_notes
            FROM notes WHERE eleve_id = ? AND matiere_id = ? AND annee_scolaire = ?";
            $moyenne_stmt = $pdo->prepare($moyenne_sql);
            $moyenne_stmt->execute([$eleve_id, $matiere_id, $annee_scolaire]);
            $stats = $moyenne_stmt->fetch();
        ?>
            <!-- Statistiques -->
            <div class="stats">
                <div class="stat-card">
                    <i class="fas fa-sticky-note" style="color: #3498db; font-size: 24px;"></i>
                    <div class="stat-value"><?= $stats['total_notes'] ?? 0 ?></div>
                    <div class="stat-label">Notes enregistrées</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-chart-line" style="color: #27ae60; font-size: 24px;"></i>
                    <div class="stat-value"><?= $stats['moyenne_principale'] ?? 'N/A' ?></div>
                    <div class="stat-label">Moyenne principale</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-file-alt" style="color: #f39c12; font-size: 24px;"></i>
                    <div class="stat-value"><?= $stats['moyenne_composition'] ?? 'N/A' ?></div>
                    <div class="stat-label">Moyenne composition</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-users" style="color: #9b59b6; font-size: 24px;"></i>
                    <div class="stat-value"><?= $stats['moyenne_classe'] ?? 'N/A' ?></div>
                    <div class="stat-label">Moyenne classe</div>
                </div>
            </div>

            <!-- Formulaire d'ajout/modification -->
            <div class="form-container">
                <h2 class="form-title">
                    <i class="fas fa-<?= $note_a_editer ? 'edit' : 'plus-circle' ?>"></i>
                    <?= $note_a_editer ? 'Modifier la Note' : 'Ajouter une Nouvelle Note' ?>
                </h2>
                
                <form method="post" class="note-form">
                    <input type="hidden" name="action" value="<?= $note_a_editer ? 'edit_note' : 'add_note' ?>">
                    <input type="hidden" name="eleve_id" value="<?= htmlspecialchars($eleve_id) ?>">
                    <input type="hidden" name="matiere_id" value="<?= htmlspecialchars($matiere_id) ?>">
                    <input type="hidden" name="annee_scolaire" value="<?= htmlspecialchars($annee_scolaire) ?>">
                    <?php if ($note_a_editer): ?>
                        <input type="hidden" name="note_id" value="<?= htmlspecialchars($note_a_editer['id']) ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="periode">Période *</label>
                        <select name="periode" id="periode" required <?= $note_a_editer ? 'disabled' : '' ?>>
                            <option value="">-- Sélectionnez une période --</option>
                            <?php foreach ($periodes as $periode): ?>
                                <option value="<?= htmlspecialchars($periode) ?>" 
                                    <?= ($note_a_editer && $note_a_editer['periode'] == $periode) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($periode) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($note_a_editer): ?>
                            <input type="hidden" name="periode" value="<?= htmlspecialchars($note_a_editer['periode']) ?>">
                            <small style="color: #7f8c8d; margin-top: 5px;">La période ne peut pas être modifiée</small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="note">Note principale (sur 20)</label>
                        <input type="number" name="note" id="note" step="0.01" min="0" max="20" 
                               placeholder="Ex: 15.5"
                               value="<?= $note_a_editer ? htmlspecialchars($note_a_editer['note']) : '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="note_composition">Note de composition (sur 20)</label>
                        <input type="number" name="note_composition" id="note_composition" step="0.01" min="0" max="20" 
                               placeholder="Ex: 16.0"
                               value="<?= $note_a_editer ? htmlspecialchars($note_a_editer['note_composition']) : '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="note_classe">Note de classe (sur 20)</label>
                        <input type="number" name="note_classe" id="note_classe" step="0.01" min="0" max="20" 
                               placeholder="Ex: 14.5"
                               value="<?= $note_a_editer ? htmlspecialchars($note_a_editer['note_classe']) : '' ?>">
                    </div>

                    <div class="form-group form-full">
                        <label for="commentaire">Commentaire</label>
                        <textarea name="commentaire" id="commentaire" placeholder="Commentaire sur la note..."><?= $note_a_editer ? htmlspecialchars($note_a_editer['commentaire']) : '' ?></textarea>
                    </div>

                    <div class="form-group form-full">
                        <button type="submit" class="btn btn-<?= $note_a_editer ? 'warning' : 'success' ?>">
                            <i class="fas fa-<?= $note_a_editer ? 'edit' : 'save' ?>"></i> 
                            <?= $note_a_editer ? 'Modifier la Note' : 'Enregistrer la Note' ?>
                        </button>
                        
                        <?php if ($note_a_editer): ?>
                            <a href="notes.php?classe=<?= $classe_id ?>&eleve_id=<?= $eleve_id ?>&matiere_id=<?= $matiere_id ?>&annee=<?= $annee_scolaire ?>" 
                               class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Tableau des notes existantes -->
            <div class="table-container">
                <div class="table-header">
                    <h2 class="table-title">
                        <i class="fas fa-list"></i>
                        Notes de <?= htmlspecialchars($eleve_info['prenom'] . ' ' . $eleve_info['nom']) ?> 
                        en <?= htmlspecialchars($matiere_info['nom']) ?>
                    </h2>
                    
                    <div>
                        <span class="stat-label">Total: <?= count($notes) ?> note(s)</span>
                    </div>
                </div>

                <?php if (count($notes) > 0): ?>
                    <table class="notes-table">
                        <thead>
                            <tr>
                                <th>Période</th>
                                <th>Note principale</th>
                                <th>Composition</th>
                                <th>Note de classe</th>
                                <th>Moyenne</th>
                                <th>Commentaire</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notes as $note): 
                                $notes_array = array_filter([$note['note'], $note['note_composition'], $note['note_classe']], function($n) {
                                    return $n !== null && $n !== '';
                                });
                                
                                $moyenne_note = !empty($notes_array) ? round(array_sum($notes_array) / count($notes_array), 2) : 'N/A';
                                $note_class = $moyenne_note !== 'N/A' ? getNoteClass($moyenne_note) : 'note-empty';
                            ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($note['periode']) ?></strong></td>
                                    <td>
                                        <span class="note-value <?= getNoteClass($note['note']) ?>">
                                            <?= $note['note'] !== null ? htmlspecialchars($note['note']) : '-' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="note-value <?= getNoteClass($note['note_composition']) ?>">
                                            <?= $note['note_composition'] !== null ? htmlspecialchars($note['note_composition']) : '-' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="note-value <?= getNoteClass($note['note_classe']) ?>">
                                            <?= $note['note_classe'] !== null ? htmlspecialchars($note['note_classe']) : '-' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="note-value <?= $note_class ?>">
                                            <?= $moyenne_note ?>
                                        </span>
                                    </td>
                                    <td style="text-align: left; max-width: 200px;"><?= htmlspecialchars($note['commentaire']) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="notes.php?classe=<?= $classe_id ?>&eleve_id=<?= $eleve_id ?>&matiere_id=<?= $matiere_id ?>&annee=<?= $annee_scolaire ?>&edit_note_id=<?= $note['id'] ?>" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                            <a href="notes.php?action=delete_note&note_id=<?= $note['id'] ?>&classe=<?= $classe_id ?>&eleve_id=<?= $eleve_id ?>&matiere_id=<?= $matiere_id ?>&annee=<?= $annee_scolaire ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette note ? Cette action est irréversible.')">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="instructions">
                        <i class="fas fa-sticky-note"></i>
                        <h3 style="margin-bottom: 10px;">Aucune note enregistrée</h3>
                        <p>Utilisez le formulaire ci-dessus pour ajouter la première note pour l'année scolaire <?= htmlspecialchars($annee_scolaire) ?>.</p>
                    </div>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- Instructions -->
            <div class="instructions">
                <i class="fas fa-edit"></i>
                <h3 style="margin-bottom: 10px;">Gestion des Notes - 1ère à 9ème Année</h3>
                <p>Veuillez sélectionner une année scolaire, une classe, un élève et une matière pour gérer les notes.</p>
                <div style="margin-top: 20px; color: #34495e; font-weight: 600;">
                    <p>📝 <strong>Comment utiliser :</strong></p>
                    <p>1. Sélectionnez l'année scolaire</p>
                    <p>2. Choisissez une classe (1ère à 9ème année)</p>
                    <p>3. Sélectionnez un élève</p>
                    <p>4. Choisissez une matière</p>
                    <p>5. Ajoutez, modifiez ou supprimez des notes</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const noteInputs = document.querySelectorAll('input[type="number"]');
            noteInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.value !== '') {
                        const note = parseFloat(this.value);
                        if (note < 0) this.value = 0;
                        if (note > 20) this.value = 20;
                    }
                });
            });

            const deleteButtons = document.querySelectorAll('.btn-danger');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Êtes-vous sûr de vouloir supprimer cette note ? Cette action est irréversible.')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>