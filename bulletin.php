<?php
require 'config.php';

// Fonctions utilitaires
function getOrdinalSuffix($n) {
    if ($n == 1) return 'er';
    return 'ème';
}

function getRang($pdo, $eleve_id) {
    $sql = "SELECT e.id, 
                   ROUND((SUM(n.note) + SUM(IFNULL(n.note_composition, 0))) / 
                   (COUNT(n.note) + COUNT(IFNULL(n.note_composition, 0))), 2) AS moyenne
            FROM eleves e
            LEFT JOIN notes n ON e.id = n.eleve_id
            GROUP BY e.id
            ORDER BY moyenne DESC";
    $stmt = $pdo->query($sql);
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $rang = 1;
    foreach ($resultats as $row) {
        if ($row['id'] == $eleve_id) {
            return $rang . getOrdinalSuffix($rang);
        }
        $rang++;
    }
    return 'N/A';
}

function getMoyenneGenerale($pdo, $eleve_id) {
    $sql = "SELECT ROUND((SUM(n.note) + SUM(IFNULL(n.note_composition, 0))) / 
                   (COUNT(n.note) + COUNT(IFNULL(n.note_composition, 0))), 2) AS moyenne
            FROM notes n
            WHERE n.eleve_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$eleve_id]);
    $result = $stmt->fetch();
    return $result ? $result['moyenne'] : 'N/A';
}

function getMoyennesParMatiere($pdo, $eleve_id) {
    $sql = "SELECT m.id, m.nom, m.coefficient, 
                   ROUND((SUM(n.note) + SUM(IFNULL(n.note_composition, 0))) / 
                   (COUNT(n.note) + COUNT(IFNULL(n.note_composition, 0))), 2) AS moyenne
            FROM matieres m
            LEFT JOIN notes n ON m.id = n.matiere_id AND n.eleve_id = ?
            GROUP BY m.id, m.nom, m.coefficient
            ORDER BY m.nom";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$eleve_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAppreciation($moyenne) {
    if ($moyenne === 'N/A') return "Non noté";
    if ($moyenne >= 16) return "Excellent";
    if ($moyenne >= 14) return "Très bien";
    if ($moyenne >= 12) return "Bien";
    if ($moyenne >= 10) return "Assez bien";
    return "Insuffisant";
}

// Récupération des données
try {
    $ecole_info = $pdo->query("SELECT nom, adresse, ville, code_postal, logo FROM ecole LIMIT 1")->fetch();
} catch (Exception $e) {
    $ecole_info = [
        'nom' => 'École Sounké TRAORE',
        'adresse' => 'Garantiguibougou',
        'ville' => 'Bamako',
        'code_postal' => '300 Kalaban',
        'logo' => ''
    ];
}

$classe_id = $_GET['classe'] ?? null;
$classes = $pdo->query("SELECT id, nom FROM classes ORDER BY nom")->fetchAll();

$eleves = [];
if ($classe_id) {
    $stmt = $pdo->prepare("SELECT id, nom, prenom FROM eleves WHERE classe_id = ? ORDER BY nom");
    $stmt->execute([$classe_id]);
    $eleves = $stmt->fetchAll();
}

$eleve_id = $_GET['eleve_id'] ?? null;
$eleve = null;
$moyennes_par_matiere = [];

if ($eleve_id) {
    $stmt = $pdo->prepare("SELECT id, nom, prenom, classe_id FROM eleves WHERE id = ?");
    $stmt->execute([$eleve_id]);
    $eleve = $stmt->fetch();

    if ($eleve) {
        $moyennes_par_matiere = getMoyennesParMatiere($pdo, $eleve_id);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Bulletin Scolaire</title>
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

        .bulletin-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 30px;
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
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .school-info p {
            opacity: 0.9;
            font-size: 14px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #2c3e50;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Filtres */
        .filters {
            padding: 25px 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .filter-form {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        select {
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            background: white;
            font-size: 14px;
            min-width: 220px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        /* Actions */
        .actions {
            padding: 20px 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Tableau principal */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .main-table th {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            color: white;
            padding: 20px 15px;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            border: none;
        }

        .main-table td {
            padding: 18px 15px;
            text-align: center;
            border-bottom: 1px solid #ecf0f1;
            transition: background-color 0.3s ease;
        }

        .student-info-row {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }

        .student-info-row td {
            padding: 25px 15px;
            font-weight: 600;
            font-size: 16px;
            border-bottom: 2px solid #bdc3c7;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .info-label {
            color: #7f8c8d;
            font-weight: 500;
        }

        .info-value {
            color: #2c3e50;
            font-weight: 700;
        }

        .moyenne-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            color: white;
            font-weight: 700;
            min-width: 70px;
            text-align: center;
        }

        .moyenne-excellente {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
        }

        .moyenne-bonne {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }

        .moyenne-moyenne {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }

        .moyenne-faible {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        /* Lignes des matières */
        .matiere-row td {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        .matiere-row:hover td {
            background: #e3f2fd;
        }

        .coefficient {
            background: #34495e;
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Ligne de total */
        .total-row {
            background: linear-gradient(135deg, #2c3e50, #34495e);
        }

        .total-row td {
            color: white;
            font-weight: 800;
            font-size: 18px;
            border: none;
            padding: 25px 15px;
        }

        /* Appréciation */
        .appreciation-section {
            padding: 30px;
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border-left: 5px solid #f39c12;
        }

        .appreciation-title {
            color: #e67e22;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .appreciation-text {
            color: #7d6608;
            font-size: 16px;
            line-height: 1.6;
            font-style: italic;
        }

        /* Signatures */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 40px 30px;
            background: #f8f9fa;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-top: 2px solid #7f8c8d;
            margin: 50px 0 15px;
        }

        .signature-label {
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            background: #2c3e50;
            color: #ecf0f1;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
                padding: 20px;
            }

            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            select {
                min-width: auto;
            }

            .actions {
                justify-content: center;
            }

            .main-table {
                font-size: 14px;
            }

            .main-table th,
            .main-table td {
                padding: 12px 8px;
            }

            .signatures {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        /* Impression */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .bulletin-container {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
            }

            .filters, .actions {
                display: none;
            }

            .btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="bulletin-container">
        <!-- En-tête -->
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

        <!-- Filtres -->
        <div class="filters">
            <form method="get" class="filter-form">
                <select name="classe" onchange="this.form.submit()">
                    <option value="">-- Sélectionnez une classe --</option>
                    <?php foreach ($classes as $classe): ?>
                        <option value="<?= htmlspecialchars($classe['id']) ?>" <?= $classe_id == $classe['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($classe['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if ($classe_id): ?>
                <select name="eleve_id" onchange="this.form.submit()">
                    <option value="">-- Sélectionnez un élève --</option>
                    <?php foreach ($eleves as $e): ?>
                        <option value="<?= htmlspecialchars($e['id']) ?>" <?= $eleve_id == $e['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nom'] . ' ' . $e['prenom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
            </form>
        </div>

        <!-- Actions -->
        <div class="actions">
            <a href="dashboard.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>

        <?php if ($eleve): 
            $moyenneGenerale = getMoyenneGenerale($pdo, $eleve_id);
            $rang = getRang($pdo, $eleve_id);
            
            // Déterminer la classe CSS pour la moyenne
            if ($moyenneGenerale === 'N/A') {
                $moyenneClass = 'moyenne-faible';
            } elseif ($moyenneGenerale >= 16) {
                $moyenneClass = 'moyenne-excellente';
            } elseif ($moyenneGenerale >= 14) {
                $moyenneClass = 'moyenne-bonne';
            } elseif ($moyenneGenerale >= 10) {
                $moyenneClass = 'moyenne-moyenne';
            } else {
                $moyenneClass = 'moyenne-faible';
            }

            // Appréciation générale
            if ($moyenneGenerale === 'N/A') {
                $appreciation = "Données insuffisantes pour formuler une appréciation.";
            } elseif ($moyenneGenerale >= 16) {
                $appreciation = "Excellente année scolaire ! L'élève fait preuve d'un travail remarquable et d'une grande régularité. Continue sur cette voie !";
            } elseif ($moyenneGenerale >= 14) {
                $appreciation = "Très bon travail. L'élève est sérieux, appliqué et montre de réelles capacités. Félicitations !";
            } elseif ($moyenneGenerale >= 12) {
                $appreciation = "Bon travail dans l'ensemble. Quelques efforts supplémentaires dans certaines matières permettraient d'améliorer encore les résultats.";
            } elseif ($moyenneGenerale >= 10) {
                $appreciation = "Résultats satisfaisants. L'élève doit consolider ses acquis et fournir un travail plus régulier pour progresser.";
            } else {
                $appreciation = "Résultats insuffisants. Un effort important et soutenu doit être fourni pour rattraper le niveau requis.";
            }
        ?>
            <!-- Tableau principal -->
            <table class="main-table">
                <!-- En-tête du tableau -->
                <thead>
                    <tr>
                        <th colspan="6" style="font-size: 24px; padding: 30px;">
                            <i class="fas fa-scroll"></i> BULLETIN SCOLAIRE
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Informations de l'élève -->
                    <tr class="student-info-row">
                        <td colspan="6">
                            <div class="info-item">
                                <span class="info-label">Élève :</span>
                                <span class="info-value"><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Classe :</span>
                                <span class="info-value"><?= htmlspecialchars($classes[array_search($eleve['classe_id'], array_column($classes, 'id'))]['nom']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Moyenne Générale :</span>
                                <span class="moyenne-badge <?= $moyenneClass ?>">
                                    <?= htmlspecialchars($moyenneGenerale) ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Rang :</span>
                                <span class="info-value"><?= htmlspecialchars($rang) ?></span>
                            </div>
                        </td>
                    </tr>

                    <!-- En-tête des colonnes -->
                    <tr>
                        <th>Matière</th>
                        <th>Coefficient</th>
                        <th>Moyenne</th>
                        <th>Appréciation</th>
                        <th>Notes</th>
                        <th>Statut</th>
                    </tr>

                    <!-- Lignes des matières -->
                    <?php 
                    $total_pondere = 0;
                    $total_coefficient = 0;
                    
                    foreach ($moyennes_par_matiere as $matiere): 
                        $moyenne = $matiere['moyenne'] ?? 'N/A';
                        
                        if ($moyenne !== 'N/A') {
                            $total_pondere += $moyenne * $matiere['coefficient'];
                            $total_coefficient += $matiere['coefficient'];
                        }
                        
                        // Déterminer la classe CSS pour la moyenne de la matière
                        if ($moyenne === 'N/A') {
                            $matiereMoyenneClass = 'moyenne-faible';
                        } elseif ($moyenne >= 16) {
                            $matiereMoyenneClass = 'moyenne-excellente';
                        } elseif ($moyenne >= 14) {
                            $matiereMoyenneClass = 'moyenne-bonne';
                        } elseif ($moyenne >= 10) {
                            $matiereMoyenneClass = 'moyenne-moyenne';
                        } else {
                            $matiereMoyenneClass = 'moyenne-faible';
                        }
                        
                        $appreciation_matiere = getAppreciation($moyenne);
                        $statut = ($moyenne !== 'N/A' && $moyenne >= 10) ? 'Validé' : 'Non validé';
                        $statut_class = ($moyenne !== 'N/A' && $moyenne >= 10) ? 'moyenne-excellente' : 'moyenne-faible';
                    ?>
                    <tr class="matiere-row">
                        <td style="text-align: left; font-weight: 600;">
                            <i class="fas fa-book" style="color: #3498db; margin-right: 8px;"></i>
                            <?= htmlspecialchars($matiere['nom']) ?>
                        </td>
                        <td>
                            <span class="coefficient"><?= htmlspecialchars($matiere['coefficient']) ?></span>
                        </td>
                        <td>
                            <span class="moyenne-badge <?= $matiereMoyenneClass ?>">
                                <?= htmlspecialchars($moyenne) ?>
                            </span>
                        </td>
                        <td style="font-style: italic; color: #7f8c8d;">
                            <?= $appreciation_matiere ?>
                        </td>
                        <td>
                            <i class="fas fa-chart-line" style="color: #9b59b6;"></i>
                        </td>
                        <td>
                            <span class="moyenne-badge <?= $statut_class ?>" style="font-size: 12px; padding: 5px 10px;">
                                <?= $statut ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <!-- Ligne de total -->
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right;">
                            <strong>MOYENNE GÉNÉRALE :</strong>
                        </td>
                        <td>
                            <span class="moyenne-badge <?= $moyenneClass ?>" style="font-size: 20px; padding: 10px 20px;">
                                <?= htmlspecialchars($moyenneGenerale) ?>
                            </span>
                        </td>
                        <td colspan="3" style="font-size: 20px;">
                            <strong><?= $moyenneGenerale >= 10 ? 'ADMIS(E)' : 'NON ADMIS(E)' ?></strong>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Appréciation générale -->
            <div class="appreciation-section">
                <div class="appreciation-title">
                    <i class="fas fa-comment-dots"></i>
                    APPRÉCIATION GÉNÉRALE
                </div>
                <div class="appreciation-text">
                    <?= $appreciation ?>
                </div>
            </div>

            <!-- Signatures -->
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Signature des Parents</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Le Directeur</div>
                    <div style="color: #7f8c8d; font-size: 12px; margin-top: 5px;">
                        <?= htmlspecialchars($ecole_info['nom']) ?>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Message si aucun élève sélectionné -->
            <div style="text-align: center; padding: 60px 30px; color: #7f8c8d;">
                <i class="fas fa-user-graduate" style="font-size: 64px; margin-bottom: 20px; opacity: 0.5;"></i>
                <h3 style="margin-bottom: 10px;">Sélectionnez un élève</h3>
                <p>Veuillez choisir une classe et un élève pour afficher le bulletin scolaire.</p>
            </div>
        <?php endif; ?>

        <!-- Pied de page -->
        <div class="footer">
            <p>Bulletin généré le <?= date('d/m/Y à H:i') ?> - <?= htmlspecialchars($ecole_info['nom']) ?></p>
        </div>
    </div>
</body>
</html>