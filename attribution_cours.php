<?php
// =============================================
// CONFIGURATION ET CONNEXION À LA BASE DE DONNÉES
// =============================================
session_start();
require_once 'config.php';

// Variables pour les statistiques
$total_enseignants = 0;
$total_matieres = 0;
$total_attributions = 0;
$enseignants = [];
$matieres = [];
$attributions = [];

try {
    // Récupérer le nombre total d'enseignants
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM enseignants");
    $total_enseignants = $stmt->fetch()['total'];
    
    // Récupérer le nombre total de matières
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM matieres");
    $total_matieres = $stmt->fetch()['total'];
    
    // Récupérer le nombre total d'attributions
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM attribution_cours");
    $total_attributions = $stmt->fetch()['total'];
    
    // Récupérer la liste des enseignants
    $stmt = $pdo->query("SELECT id, nom, prenom FROM enseignants ORDER BY nom");
    $enseignants = $stmt->fetchAll();
    
    // Récupérer la liste des matières
    $stmt = $pdo->query("SELECT id, nom FROM matieres ORDER BY nom");
    $matieres = $stmt->fetchAll();
    
    // Récupérer les attributions avec les noms des enseignants et matières
    $stmt = $pdo->query("
        SELECT a.id, a.date_attribution,
               CONCAT(e.nom, ' ', e.prenom) as enseignant_nom,
               m.nom as matiere_nom
        FROM attribution_cours a
        JOIN enseignants e ON a.enseignant_id = e.id
        JOIN matieres m ON a.matiere_id = m.id
        ORDER BY a.date_attribution DESC
    ");
    $attributions = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error_message = "Erreur de base de données : " . $e->getMessage();
}

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    $enseignant_id = $_POST['enseignant_id'];
                    $matiere_id = $_POST['matiere_id'];
                    
                    // Vérifier si l'attribution existe déjà
                    $stmt = $pdo->prepare("SELECT id FROM attribution_cours WHERE enseignant_id = ? AND matiere_id = ?");
                    $stmt->execute([$enseignant_id, $matiere_id]);
                    
                    if ($stmt->fetch()) {
                        $error_message = "Cette attribution existe déjà.";
                    } else {
                        // Ajouter l'attribution
                        $stmt = $pdo->prepare("INSERT INTO attribution_cours (enseignant_id, matiere_id, date_attribution) VALUES (?, ?, NOW())");
                        $stmt->execute([$enseignant_id, $matiere_id]);
                        
                        // Rediriger pour éviter la resoumission
                        header('Location: attribution_cours.php?success=1');
                        exit();
                    }
                } catch (PDOException $e) {
                    $error_message = "Erreur lors de l'ajout : " . $e->getMessage();
                }
                break;
                
            case 'delete':
                try {
                    $id = $_POST['id'];
                    $stmt = $pdo->prepare("DELETE FROM attribution_cours WHERE id = ?");
                    $stmt->execute([$id]);
                    
                    header('Location: attribution_cours.php?deleted=1');
                    exit();
                } catch (PDOException $e) {
                    $error_message = "Erreur lors de la suppression : " . $e->getMessage();
                }
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Attributions de Cours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --primary-dark: #2e59d9;
            --secondary-color: #6f42c1;
            --accent-color: #36b9cc;
            --light-bg: #f8f9fc;
            --card-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --dark-color: #5a5c69;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            background: linear-gradient(135deg, var(--light-bg) 0%, #e5e9f2 100%);
            min-height: 100vh;
            padding: 20px;
            font-family: 'Nunito', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #444;
        }
        
        .app-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
        }
        
        .page-header h1 {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 10px;
            position: relative;
        }
        
        .page-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem rgba(58, 59, 69, 0.25);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 15px 20px;
            font-weight: 700;
            border: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-header i {
            margin-right: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(to bottom right, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: linear-gradient(to bottom right, var(--primary-dark), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
        }
        
        .btn-warning {
            background: linear-gradient(to bottom right, var(--warning-color), #f4b619);
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(to bottom right, var(--danger-color), #e02d1b);
            color: white;
        }
        
        .btn-warning, .btn-danger {
            border-radius: 6px;
            padding: 8px 15px;
            border: none;
            transition: all 0.3s;
        }
        
        .btn-warning:hover, .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .table th {
            border-top: none;
            background: linear-gradient(135deg, #eaecf4, #d8dcf0);
            color: var(--primary-color);
            font-weight: 700;
            padding: 15px;
        }
        
        .table td {
            padding: 15px;
            vertical-align: middle;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.1);
            padding: 15px 20px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #d1d3e2;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.3rem rgba(78, 115, 223, 0.2);
        }
        
        .action-buttons .btn {
            border-radius: 6px;
            margin-right: 5px;
        }
        
        .badge-date {
            background-color: #e8eaf0;
            color: #6e707e;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6e707e;
        }
        
        .empty-state i {
            font-size: 5rem;
            margin-bottom: 15px;
            color: #dddfeb;
        }
        
        @media (max-width: 768px) {
            .card {
                margin-bottom: 15px;
            }
            
            .action-buttons {
                display: flex;
                flex-direction: column;
            }
            
            .action-buttons .btn {
                margin-bottom: 5px;
                margin-right: 0;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .page-header p {
                font-size: 1rem;
            }
        }
        
        .stats-box {
            text-align: center;
            padding: 20px;
            border-radius: 12px;
            background: white;
            box-shadow: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        
        .stats-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 0.9rem;
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .teacher-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            margin-right: 10px;
        }
        
        .subject-badge {
            background: linear-gradient(135deg, var(--accent-color), #2bb9cc);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 0.5rem 1.5rem rgba(78, 115, 223, 0.4);
            z-index: 100;
            transition: all 0.3s;
        }
        
        .floating-btn:hover {
            transform: translateY(-5px) rotate(90deg);
            box-shadow: 0 0.7rem 2rem rgba(78, 115, 223, 0.5);
            color: white;
        }
        
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 350px;
        }
        
        .progress {
            height: 10px;
            border-radius: 10px;
            background-color: #eaecf4;
        }
        
        .progress-bar {
            border-radius: 10px;
        }
        
        .stats-card {
            background: linear-gradient(135deg, var(--success-color), #18b57c);
            color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--card-shadow);
        }
        
        .stats-card h5 {
            font-size: 1.1rem;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .stats-card .number {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 5px;
        }
        
        .stats-card .label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s;
            z-index: 10;
            text-decoration: none;
        }
        
        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(-5px);
            color: white;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="page-header text-center">
            <!-- CORRECTION DU BOUTON DE RETOUR -->
            <a href="javascript:history.back()" class="back-button">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="mb-3"><i class="fas fa-chalkboard-teacher me-2"></i> Gestion des attributions de cours</h1>
            <p class="lead">Attribuez des matières aux enseignants en toute simplicité</p>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stats-box">
                    <div class="stats-number"><?= $total_enseignants ?></div>
                    <div class="stats-label">Enseignants</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-box">
                    <div class="stats-number"><?= $total_matieres ?></div>
                    <div class="stats-label">Matières</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-box">
                    <div class="stats-number"><?= $total_attributions ?></div>
                    <div class="stats-label">Attributions</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-box">
                    <div class="stats-number"><?= $total_enseignants > 0 ? round(($total_attributions / ($total_enseignants * $total_matieres)) * 100) : 0 ?>%</div>
                    <div class="stats-label">Cours couverts</div>
                </div>
            </div>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-3 fa-2x"></i>
                <div>
                    <h5 class="alert-heading mb-1">Erreur</h5>
                    <p class="mb-0"><?= htmlspecialchars($error_message) ?></p>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fa-2x"></i>
                <div>
                    <h5 class="alert-heading mb-1">Succès</h5>
                    <p class="mb-0">L'attribution a été ajoutée avec succès.</p>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fa-2x"></i>
                <div>
                    <h5 class="alert-heading mb-1">Information</h5>
                    <p class="mb-0">L'attribution a été supprimée avec succès.</p>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="alert alert-info d-flex align-items-center">
            <i class="fas fa-lightbulb me-3 fa-2x"></i>
            <div>
                <h5 class="alert-heading mb-1">Conseil</h5>
                <p class="mb-0">Sélectionnez un enseignant et une matière, puis cliquez sur "Ajouter l'attribution" pour enregistrer.</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <i class="fas fa-plus-circle me-2"></i>
                            Nouvelle attribution
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="attributionForm">
                            <input type="hidden" name="action" value="add">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Enseignant</label>
                                <select name="enseignant_id" class="form-select" required>
                                    <option value="">Sélectionnez un enseignant</option>
                                    <?php foreach ($enseignants as $enseignant): ?>
                                        <option value="<?= $enseignant['id'] ?>"><?= htmlspecialchars($enseignant['nom'] . ' ' . $enseignant['prenom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Matière</label>
                                <select name="matiere_id" class="form-select" required>
                                    <option value="">Sélectionnez une matière</option>
                                    <?php foreach ($matieres as $matiere): ?>
                                        <option value="<?= $matiere['id'] ?>"><?= htmlspecialchars($matiere['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-2"></i> 
                                    Ajouter l'attribution
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-chart-pie me-2"></i> Répartition par matière
                    </div>
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between">
                            <span>Mathématiques</span>
                            <span>4 enseignants</span>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="mb-3 d-flex justify-content-between">
                            <span>Physique</span>
                            <span>3 enseignants</span>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 22.5%" aria-valuenow="22.5" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="mb-3 d-flex justify-content-between">
                            <span>Français</span>
                            <span>3 enseignants</span>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 22.5%" aria-valuenow="22.5" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="mb-3 d-flex justify-content-between">
                            <span>Informatique</span>
                            <span>2 enseignants</span>
                        </div>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <i class="fas fa-list-check me-2"></i>
                            Liste des attributions
                        </div>
                        <span class="badge bg-light text-dark"><?= count($attributions) ?></span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Enseignant</th>
                                        <th>Matière</th>
                                        <th>Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($attributions)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="fas fa-inbox"></i>
                                                    <p>Aucune attribution trouvée</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($attributions as $attribution): ?>
                                            <tr>
                                                <td class="fw-semibold d-flex align-items-center">
                                                    <div class="teacher-avatar"><?= strtoupper(substr($attribution['enseignant_nom'], 0, 2)) ?></div>
                                                    <?= htmlspecialchars($attribution['enseignant_nom']) ?>
                                                </td>
                                                <td><span class="subject-badge"><?= htmlspecialchars($attribution['matiere_nom']) ?></span></td>
                                                <td><span class="badge badge-date"><?= date('d/m/Y', strtotime($attribution['date_attribution'])) ?></span></td>
                                                <td class="text-end action-buttons">
                                                    <button class="btn btn-sm btn-warning" title="Modifier" onclick="editAttribution(<?= $attribution['id'] ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Supprimer" onclick="deleteAttribution(<?= $attribution['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-chart-line me-2"></i> Statistiques des attributions
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <h5 class="text-primary">5</h5>
                                <small class="text-muted">Enseignants actifs</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-primary">7</h5>
                                <small class="text-muted">Matières enseignées</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-primary">12h</h5>
                                <small class="text-muted">Volume hebdomadaire</small>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Charge de travail</span>
                                <span class="text-primary">75%</span>
                            </div>
                            <div class="progress mb-4">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <a href="#" class="floating-btn">
            <i class="fas fa-plus"></i>
        </a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Activer les tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        
        // Fonction pour supprimer une attribution
        function deleteAttribution(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette attribution ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Fonction pour modifier une attribution (à implémenter)
        function editAttribution(id) {
            alert('Fonction de modification à implémenter pour l\'ID: ' + id);
        }
    </script>
</body>
</html>