<?php
session_start();

// Initialiser les données si elles n'existent pas
if (!isset($_SESSION['absences_data'])) {
    $_SESSION['absences_data'] = [
        'classes' => [
            ["id" => 1, "nom" => "1ème", "niveau" => "1ème année"],
            ["id" => 2, "nom" => "2ème", "niveau" => "2ème année"],
            ["id" => 3, "nom" => "3ème", "niveau" => "3ème année"],
            ["id" => 4, "nom" => "4ème", "niveau" => "4ème année"],
            ["id" => 5, "nom" => "5ème", "niveau" => "5ème année"],
            ["id" => 6, "nom" => "6ème", "niveau" => "6ème année"],
            ["id" => 7, "nom" => "7ème", "niveau" => "7ème année"],
            ["id" => 8, "nom" => "8ème", "niveau" => "8ème année"],
            ["id" => 9, "nom" => "9ème", "niveau" => "9ème année"]
        ],
        'eleves' => [
            ["id" => 1, "nom" => "OUMOU TOURE", "classe" => "1ème", "classe_id" => 1],
            ["id" => 2, "nom" => "Emma SIDIBE", "classe" => "1ème", "classe_id" => 1],
            ["id" => 3, "nom" => "HABY BAH", "classe" => "1ème", "classe_id" => 1],
            ["id" => 4, "nom" => "FATOUMA GUINDO", "classe" => "1ème", "classe_id" => 1],
            ["id" => 5, "nom" => "MATOU KONE", "classe" => "2ème", "classe_id" => 2],
            ["id" => 6, "nom" => "GOGO OUOLO", "classe" => "2ème", "classe_id" => 2],
            ["id" => 7, "nom" => "OUMAR COULIBALY", "classe" => "2ème", "classe_id" => 2],
            ["id" => 8, "nom" => "NENE OUANE", "classe" => "2ème", "classe_id" => 2],
            ["id" => 9, "nom" => "Thomas SAGARA", "classe" => "3ème", "classe_id" => 3],
            ["id" => 10, "nom" => "KAMISSA DICKO", "classe" => "3ème", "classe_id" => 3],
            ["id" => 11, "nom" => "NABI FOFANA", "classe" => "3ème", "classe_id" => 3],
            ["id" => 12, "nom" => "DJENE DAOU", "classe" => "3ème", "classe_id" => 3],
            ["id" => 13, "nom" => "Sophie SISSOKO", "classe" => "4ème", "classe_id" => 4],
            ["id" => 14, "nom" => "KALIFA COULIBALY", "classe" => "4ème", "classe_id" => 4],
            ["id" => 15, "nom" => "BIJOU HAIDARA", "classe" => "4ème", "classe_id" => 4],
            ["id" => 16, "nom" => "THINI GUINDO", "classe" => "4ème", "classe_id" => 4],
            ["id" => 17, "nom" => "TOKORA FANE", "classe" => "5ème", "classe_id" => 5],
            ["id" => 18, "nom" => "KADI KONE", "classe" => "5ème", "classe_id" => 5],
            ["id" => 19, "nom" => "RAMATA BAH", "classe" => "5ème", "classe_id" => 5],
            ["id" => 20, "nom" => "ZOL DJIGUE", "classe" => "5ème", "classe_id" => 5],
            ["id" => 21, "nom" => "Mohamed", "classe" => "6ème", "classe_id" => 6],
            ["id" => 22, "nom" => "Fatima Zahra", "classe" => "6ème", "classe_id" => 6],
            ["id" => 23, "nom" => "Jean COULIBALY", "classe" => "6ème", "classe_id" => 6],
            ["id" => 24, "nom" => "MARIAM TRAORE", "classe" => "6ème", "classe_id" => 6],
            ["id" => 25, "nom" => "Paul POUJOUGOU", "classe" => "7ème", "classe_id" => 7],
            ["id" => 26, "nom" => "Sophie KAMATE", "classe" => "7ème", "classe_id" => 7],
            ["id" => 27, "nom" => "Ahmed DIABATE", "classe" => "7ème", "classe_id" => 7],
            ["id" => 28, "nom" => "FANTA KONATE", "classe" => "7ème", "classe_id" => 7],
            ["id" => 29, "nom" => "Antoine TALL", "classe" => "8ème", "classe_id" => 8],
            ["id" => 30, "nom" => "INA CISSOKO", "classe" => "8ème", "classe_id" => 8],
            ["id" => 31, "nom" => "Karim CISSE", "classe" => "8ème", "classe_id" => 8],
            ["id" => 32, "nom" => "Leila TRAORE", "classe" => "8ème", "classe_id" => 8],
            ["id" => 33, "nom" => "Youssouf AHMED", "classe" => "9ème", "classe_id" => 9],
            ["id" => 34, "nom" => "Clara MORGANE", "classe" => "9ème", "classe_id" => 9],
            ["id" => 35, "nom" => "David GUISSE", "classe" => "9ème", "classe_id" => 9],
            ["id" => 36, "nom" => "Zeinab TOURE", "classe" => "9ème", "classe_id" => 9]
        ],
        'matieres' => [
            ['id' => 1, 'nom' => 'Mathématiques'],
            ['id' => 2, 'nom' => 'Français'],
            ['id' => 3, 'nom' => 'Physique-Chimie'],
            ['id' => 4, 'nom' => 'Histoire-Géographie'],
            ['id' => 5, 'nom' => 'Anglais'],
            ['id' => 6, 'nom' => 'SVT'],
            ['id' => 7, 'nom' => 'EPS'],
            ['id' => 8, 'nom' => 'Technologie'],
            ['id' => 9, 'nom' => 'Arts Plastiques'],
            ['id' => 10, 'nom' => 'Musique']
        ],
        'absences' => [
            [
                'id' => 1,
                'eleve_id' => 1,
                'date_absence' => '2024-01-15',
                'matiere_id' => 1,
                'justifiee' => 1,
                'motif' => 'Maladie avec certificat médical',
                'date_creation' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'eleve_id' => 16,
                'date_absence' => '2024-01-16',
                'matiere_id' => 2,
                'justifiee' => 0,
                'motif' => 'Absence non justifiée',
                'date_creation' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'eleve_id' => 25,
                'date_absence' => '2024-01-17',
                'matiere_id' => 3,
                'justifiee' => 1,
                'motif' => 'Rendez-vous médical',
                'date_creation' => date('Y-m-d H:i:s')
            ]
        ]
    ];
}

$message = '';
$data = $_SESSION['absences_data'];

// Vérifier que toutes les clés existent
if (!isset($data['classes'])) $data['classes'] = [];
if (!isset($data['eleves'])) $data['eleves'] = [];
if (!isset($data['matieres'])) $data['matieres'] = [];
if (!isset($data['absences'])) $data['absences'] = [];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter_absence'])) {
        $new_id = count($data['absences']) + 1;
        $new_absence = [
            'id' => $new_id,
            'eleve_id' => (int)$_POST['eleve_id'],
            'date_absence' => $_POST['date_absence'],
            'matiere_id' => (int)$_POST['matiere_id'],
            'justifiee' => isset($_POST['justifiee']) ? 1 : 0,
            'motif' => $_POST['motif'],
            'date_creation' => date('Y-m-d H:i:s')
        ];
        
        $data['absences'][] = $new_absence;
        $_SESSION['absences_data'] = $data;
        $message = "Absence enregistrée avec succès!";
    }
    
    if (isset($_POST['modifier_absence'])) {
        $absence_id = (int)$_POST['absence_id'];
        $justifiee = isset($_POST['justifiee']) ? 1 : 0;
        $motif = $_POST['motif'];
        
        foreach ($data['absences'] as &$absence) {
            if ($absence['id'] == $absence_id) {
                $absence['justifiee'] = $justifiee;
                $absence['motif'] = $motif;
                break;
            }
        }
        $_SESSION['absences_data'] = $data;
        $message = "Absence modifiée avec succès!";
    }
    
    if (isset($_POST['supprimer_absence'])) {
        $absence_id = (int)$_POST['absence_id'];
        $data['absences'] = array_filter($data['absences'], function($absence) use ($absence_id) {
            return $absence['id'] != $absence_id;
        });
        $_SESSION['absences_data'] = $data;
        $message = "Absence supprimée avec succès!";
    }

    // Filtre par classe
    if (isset($_POST['filter_class'])) {
        $filter_class_id = (int)$_POST['classe_id'];
        $_SESSION['current_filter'] = $filter_class_id;
    }

    // Réinitialiser le filtre
    if (isset($_POST['reset_filter'])) {
        unset($_SESSION['current_filter']);
    }

    // Réinitialiser toutes les données
    if (isset($_POST['reset_data'])) {
        unset($_SESSION['absences_data']);
        unset($_SESSION['current_filter']);
        echo "<script>window.location.reload();</script>";
        exit;
    }
}

// Préparer les données pour l'affichage
$classes = $data['classes'];
$eleves = $data['eleves'];
$matieres = $data['matieres'];
$absences = $data['absences'];

// Appliquer le filtre si défini
$current_filter = $_SESSION['current_filter'] ?? null;
if ($current_filter) {
    $absences = array_filter($absences, function($absence) use ($eleves, $current_filter) {
        $eleve = getEleveById($eleves, $absence['eleve_id']);
        return $eleve && $eleve['classe_id'] == $current_filter;
    });
}

// Fonction pour obtenir un élève par ID
function getEleveById($eleves, $eleve_id) {
    foreach ($eleves as $eleve) {
        if ($eleve['id'] == $eleve_id) {
            return $eleve;
        }
    }
    return null;
}

// Fonction pour obtenir le nom d'un élève
function getEleveName($eleves, $eleve_id) {
    $eleve = getEleveById($eleves, $eleve_id);
    return $eleve ? $eleve['nom'] : 'Inconnu';
}

// Fonction pour obtenir le nom d'une matière
function getMatiereName($matieres, $matiere_id) {
    foreach ($matieres as $matiere) {
        if ($matiere['id'] == $matiere_id) {
            return $matiere['nom'];
        }
    }
    return 'Inconnue';
}

// Fonction pour obtenir le nom d'une classe
function getClasseName($classes, $classe_id) {
    foreach ($classes as $classe) {
        if ($classe['id'] == $classe_id) {
            return $classe['nom'];
        }
    }
    return 'Inconnue';
}

// Fonction pour obtenir les élèves d'une classe
function getElevesByClasse($eleves, $classe_id) {
    return array_filter($eleves, function($eleve) use ($classe_id) {
        return $eleve['classe_id'] == $classe_id;
    });
}

// Fonction pour obtenir la classe d'un élève - CORRIGÉE
function getEleveClasse($eleves, $eleve_id) {
    $eleve = getEleveById($eleves, $eleve_id);
    // Vérifier si la clé 'classe' existe, sinon utiliser 'classe_id' pour trouver le nom
    if ($eleve) {
        if (isset($eleve['classe'])) {
            return $eleve['classe'];
        } elseif (isset($eleve['classe_id'])) {
            // Si pas de clé 'classe', on utilise classe_id pour trouver le nom
            global $classes;
            foreach ($classes as $classe) {
                if ($classe['id'] == $eleve['classe_id']) {
                    return $classe['nom'];
                }
            }
        }
    }
    return 'Inconnue';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Absences</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-top: 20px;
        }
        .header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        .content {
            padding: 30px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        .stats-card {
            text-align: center;
            padding: 20px;
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-number {
            font-size: 2.5em;
            font-weight: bold;
        }
        .table th {
            background: #34495e;
            color: white;
            border: none;
        }
        .btn-action {
            margin: 2px;
        }
        .info-badge {
            background: #17a2b8;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .classe-badge {
            font-size: 0.8em;
            margin-left: 5px;
        }
        .header-actions {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-calendar-times me-3"></i>Gestion des Absences</h1>
            <p class="lead">Système de gestion des absences scolaires</p>
            
            <!-- Bouton Retour au tableau de bord -->
            <div class="header-actions">
                <a href="dashboard.php" class="btn btn-light btn-sm">
                    <i class="fas fa-tachometer-alt me-1"></i>Retour au tableau de bord
                </a>
            </div>
        </div>
        
        <div class="content">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo strpos($message, 'Erreur') !== false ? 'danger' : 'success'; ?> alert-dismissible fade show">
                    <i class="fas <?php echo strpos($message, 'Erreur') !== false ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?> me-2"></i>
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Filtre par classe -->
            <div class="filter-section">
                <form method="POST" class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Liste des classes :</strong></label>
                        <div class="input-group">
                            <select name="classe_id" class="form-select">
                                <option value="">Toutes les classes</option>
                                <?php foreach ($classes as $classe): ?>
                                    <option value="<?php echo $classe['id']; ?>" 
                                        <?php echo $current_filter == $classe['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($classe['nom'] . ' - ' . $classe['niveau']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="filter_class" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>Filtrer
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?php if ($current_filter): ?>
                            <div class="d-flex align-items-center">
                                <span class="me-3">
                                    Filtre actif : <strong><?php echo getClasseName($classes, $current_filter); ?></strong>
                                </span>
                                <form method="POST" class="d-inline">
                                    <button type="submit" name="reset_filter" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-times me-1"></i>Supprimer le filtre
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="stats-number text-primary"><?php echo count($absences); ?></div>
                        <div class="stats-label text-muted">Total des absences</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="stats-number text-success">
                            <?php 
                                $justified = array_filter($absences, function($a) { return $a['justifiee']; });
                                echo count($justified); 
                            ?>
                        </div>
                        <div class="stats-label text-muted">Absences justifiées</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="stats-number text-danger">
                            <?php 
                                $unjustified = array_filter($absences, function($a) { return !$a['justifiee']; });
                                echo count($unjustified); 
                            ?>
                        </div>
                        <div class="stats-label text-muted">Absences non justifiées</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="stats-number text-info"><?php echo count($eleves); ?></div>
                        <div class="stats-label text-muted">Élèves enregistrés</div>
                    </div>
                </div>
            </div>
            
            <!-- Formulaire d'ajout -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Ajouter une absence</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Élève:</label>
                                <select name="eleve_id" class="form-select" required>
                                    <option value="">Sélectionner un élève</option>
                                    <?php foreach ($classes as $classe): ?>
                                        <optgroup label="<?php echo htmlspecialchars($classe['nom'] . ' - ' . $classe['niveau']); ?>">
                                            <?php 
                                            $eleves_classe = getElevesByClasse($eleves, $classe['id']);
                                            foreach ($eleves_classe as $eleve): ?>
                                                <option value="<?php echo $eleve['id']; ?>">
                                                    <?php echo htmlspecialchars($eleve['nom']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Date:</label>
                                <input type="date" name="date_absence" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Matière:</label>
                                <select name="matiere_id" class="form-select" required>
                                    <option value="">Sélectionner une matière</option>
                                    <?php foreach ($matieres as $matiere): ?>
                                        <option value="<?php echo $matiere['id']; ?>">
                                            <?php echo htmlspecialchars($matiere['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Statut:</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="justifiee" class="form-check-input" id="justifiee" value="1">
                                    <label class="form-check-label" for="justifiee">Justifiée</label>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" name="ajouter_absence" class="btn btn-success w-100">
                                    <i class="fas fa-save me-2"></i>Ajouter
                                </button>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="form-label">Motif (optionnel):</label>
                                <textarea name="motif" class="form-control" rows="2" placeholder="Raison de l'absence..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Liste des absences -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-list me-2"></i>Liste des absences 
                        <?php if ($current_filter): ?>
                            <span class="badge bg-warning ms-2">Filtré: <?php echo getClasseName($classes, $current_filter); ?></span>
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (count($absences) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Élève</th>
                                    <th>Classe</th>
                                    <th>Date</th>
                                    <th>Matière</th>
                                    <th>Statut</th>
                                    <th>Motif</th>
                                    <th>Date création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($absences as $absence): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars(getEleveName($eleves, $absence['eleve_id'])); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary classe-badge">
                                            <?php echo htmlspecialchars(getEleveClasse($eleves, $absence['eleve_id'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($absence['date_absence'])); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars(getMatiereName($matieres, $absence['matiere_id'])); ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $absence['justifiee'] ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php if ($absence['justifiee']): ?>
                                                <i class="fas fa-check me-1"></i>Justifiée
                                            <?php else: ?>
                                                <i class="fas fa-times me-1"></i>Non justifiée
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $absence['motif'] ? htmlspecialchars($absence['motif']) : '<span class="text-muted">Aucun motif</span>'; ?>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y H:i', strtotime($absence['date_creation'])); ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="absence_id" value="<?php echo $absence['id']; ?>">
                                                <input type="hidden" name="justifiee" value="<?php echo $absence['justifiee'] ? '0' : '1'; ?>">
                                                <input type="hidden" name="motif" value="<?php echo htmlspecialchars($absence['motif'] ?: ''); ?>">
                                                <button type="submit" name="modifier_absence" class="btn btn-warning btn-action" 
                                                        title="<?php echo $absence['justifiee'] ? 'Marquer non justifiée' : 'Marquer justifiée'; ?>">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                            </form>
                                            <form method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette absence ?');">
                                                <input type="hidden" name="absence_id" value="<?php echo $absence['id']; ?>">
                                                <button type="submit" name="supprimer_absence" class="btn btn-danger btn-action" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune absence enregistrée</h5>
                        <p class="text-muted">
                            <?php if ($current_filter): ?>
                                Aucune absence pour la classe "<?php echo getClasseName($classes, $current_filter); ?>"
                            <?php else: ?>
                                Utilisez le formulaire ci-dessus pour ajouter une absence.
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bouton pour réinitialiser les données -->
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>