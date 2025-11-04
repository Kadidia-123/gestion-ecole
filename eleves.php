<?php
require_once './config.php';
require_once './functions.php';

/**
 * Gestion des élèves - Page principale
 */

// Initialisation des variables
$search = $_GET['search'] ?? '';
$eleves = [];
$total = 0;
$totalPages = 1;
$error = null;
$flash = getFlash();

// Configuration de la pagination
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

try {
    // Construction de la requête de recherche
    $whereClause = '';
    $params = [];

    if (!empty($search)) {
        $whereClause = "WHERE nom LIKE :search OR prenom LIKE :search OR matricule LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    // Comptage du total des élèves
    $countSql = "SELECT COUNT(*) FROM eleves $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();
    $totalPages = ceil($total / $limit);

    // Récupération des élèves avec pagination
    if ($total > 0) {
        $sql = "SELECT 
                    eleves.*, 
                    classes.nom AS classe_nom,
                    classes.niveau AS classe_niveau
                FROM eleves 
                LEFT JOIN classes ON eleves.classe_id = classes.id
                $whereClause
                ORDER BY eleves.id DESC 
                LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        
        // Liaison des paramètres de recherche
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        
        // Liaison des paramètres de pagination
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $eleves = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error = "Erreur de base de données : " . $e->getMessage();
} catch (Exception $e) {
    $error = "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Élèves | SchoolAdmin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== VARIABLES CSS ===== */
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --border-radius: 12px;
            --box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        /* ===== STYLES GÉNÉRAUX ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f5f7ff;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ===== COMPOSANTS CARTE ===== */
        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px 25px;
        }

        .card-body {
            padding: 25px;
        }

        .card-footer {
            padding: 20px 25px;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        /* ===== EN-TÊTE ===== */
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-title h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        /* ===== BOUTONS ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            border: none;
            font-size: 0.95rem;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-warning {
            background-color: var(--warning);
            color: white;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-secondary {
            background-color: var(--gray);
            color: white;
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* ===== RECHERCHE ===== */
        .search-section {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-form {
            flex: 1;
            display: flex;
            gap: 10px;
        }

        .search-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        /* ===== TABLEAU ===== */
        .table-container {
            overflow-x: auto;
            margin: 20px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }

        .table th {
            background-color: #f8f9fa;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 2px solid #dee2e6;
        }

        .table td {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .table tr:hover td {
            background-color: #f8f9fa;
        }

        /* ===== PHOTOS ===== */
        .student-photo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #e9ecef;
        }

        .photo-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
        }

        /* ===== ACTIONS ===== */
        .actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: var(--transition);
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        /* ===== PAGINATION ===== */
        .pagination {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 25px;
        }

        .page-link {
            padding: 8px 12px;
            border-radius: 6px;
            background-color: #f8f9fa;
            color: var(--dark);
            text-decoration: none;
            transition: var(--transition);
        }

        .page-link:hover {
            background-color: #e9ecef;
        }

        .page-link.active {
            background-color: var(--primary);
            color: white;
        }

        /* ===== ALERTES ===== */
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        /* ===== BADGES ===== */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-info {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        /* ===== STATISTIQUES ===== */
        .stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* ===== UTILITAIRES ===== */
        .text-muted {
            color: #6c757d;
        }

        .small {
            font-size: 0.85rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background-color: var(--gray);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
        }

        .back-link:hover {
            background-color: #5a6268;
            transform: translateX(-5px);
        }

        /* ===== MODE SOMBRE ===== */
        .dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }

        .dark-mode .card {
            background-color: #1e1e1e;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .dark-mode .table th {
            background-color: #2d2d2d;
            color: #e0e0e0;
            border-bottom-color: #444;
        }

        .dark-mode .table td {
            border-bottom-color: #444;
        }

        .dark-mode .table tr:hover td {
            background-color: #2d2d2d;
        }

        .dark-mode .photo-placeholder {
            background-color: #333;
            color: #666;
        }

        .dark-mode .page-link {
            background-color: #333;
            color: #e0e0e0;
        }

        .dark-mode .page-link:hover {
            background-color: #444;
        }

        .dark-mode .text-muted {
            color: #aaa;
        }

        .dark-mode .card-footer {
            background-color: #2d2d2d;
            border-top-color: #444;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .search-section {
                flex-direction: column;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .actions {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .stats {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Carte principale -->
        <div class="card">
            <!-- En-tête de la carte -->
            <div class="card-header">
                <div class="header-content">
                    <div class="header-title">
                        <i class="fas fa-users"></i>
                        <h2>Gestion des Élèves</h2>
                    </div>
                    <div class="header-actions">
                        <button id="themeToggle" class="btn btn-sm btn-glass">
                            <i class="fas fa-moon"></i> Thème
                        </button>
                    </div>
                </div>
            </div>

            <!-- Corps de la carte -->
            <div class="card-body">
                <!-- Messages d'alerte -->
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] ?>">
                        <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'danger' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                        <?= htmlspecialchars($flash['message']) ?>
                    </div>
                <?php endif; ?>
                
                <!-- Section de recherche -->
                <div class="search-section">
                    <form method="GET" class="search-form">
                        <input type="text" 
                               name="search" 
                               class="search-input" 
                               placeholder="Rechercher par nom, prénom ou matricule..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </form>
                    
                    <a href="ajouter_eleve.php" class="btn btn-success">
                        <i class="fas fa-plus"></i> Nouvel élève
                    </a>
                </div>
                
                <!-- Tableau des élèves -->
                <?php if (!empty($eleves)): ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Matricule</th>
                                    <th>Nom Complet</th>
                                    <th>Email</th>
                                    <th>Genre</th>
                                    <th>Date Naiss.</th>
                                    <th>Lieu Naiss.</th>
                                    <th>Classe</th>
                                    <th>Statut</th>
                                    <th>Téléphone</th>
                                    <th>Nationalité</th>
                                    <th>Nom Père</th>
                                    <th>Nom Mère</th>
                                    <th>Tel Parent</th>
                                    <th>Adresse</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($eleves as $eleve): ?>
                                <tr>
                                    <td>
                                        <?php 
                                        $photoUrl = !empty($eleve['photo']) ? getStudentPhotoUrl($eleve['photo']) : null;
                                        if ($photoUrl): ?>
                                            <img src="<?= htmlspecialchars($photoUrl) ?>" 
                                                 class="student-photo" 
                                                 alt="Photo de <?= htmlspecialchars($eleve['prenom'] ?? '') ?>"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="photo-placeholder" style="display: none;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php else: ?>
                                            <div class="photo-placeholder">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= !empty($eleve['matricule']) ? htmlspecialchars($eleve['matricule']) : '<span class="text-muted">N/A</span>' ?></strong>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars(trim(($eleve['prenom'] ?? '') . ' ' . ($eleve['nom'] ?? ''))) ?></strong>
                                    </td>
                                    <td>
                                        <?= !empty($eleve['email']) ? htmlspecialchars($eleve['email']) : '<span class="text-muted">N/A</span>' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($eleve['genre'])): ?>
                                            <span class="badge <?= $eleve['genre'] == 'M' ? 'badge-info' : 'badge-warning' ?>">
                                                <?= $eleve['genre'] == 'M' ? 'Masculin' : 'Féminin' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Non renseigné</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= !empty($eleve['date_naissance']) ? date('d/m/Y', strtotime($eleve['date_naissance'])) : '<span class="text-muted">N/A</span>' ?>
                                    </td>
                                    <td>
                                        <?= !empty($eleve['lieu_naissance']) ? htmlspecialchars($eleve['lieu_naissance']) : '<span class="text-muted">N/A</span>' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($eleve['classe_nom'])): ?>
                                            <span class="badge badge-success">
                                                <?= htmlspecialchars($eleve['classe_nom']) ?>
                                            </span>
                                            <div class="text-muted small">
                                                <?= !empty($eleve['classe_niveau']) ? htmlspecialchars($eleve['classe_niveau']) : '' ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Non affecté</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $eleve['statut'] == 'actif' ? 'badge-success' : 'badge-danger' ?>">
                                            <?= $eleve['statut'] == 'actif' ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= !empty($eleve['telephone']) ? htmlspecialchars($eleve['telephone']) : '<span class="text-muted">N/A</span>' ?>
                                    </td>
                                    <td>
                                        <?= !empty($eleve['nationalite']) ? htmlspecialchars($eleve['nationalite']) : '<span class="text-muted">N/A</span>' ?>
                                    </td>
                                    <td>
                                        <?= !empty($eleve['nom_pere']) ? htmlspecialchars($eleve['nom_pere']) : '<span class="text-muted">N/A</span>' ?>
                                    </td>
                                    <td>
                                        <?= !empty($eleve['nom_mere']) ? htmlspecialchars($eleve['nom_mere']) : '<span class="text-muted">N/A</span>' ?>
                                    </td>
                                    <td>
                                        <?= !empty($eleve['tel_parent']) ? htmlspecialchars($eleve['tel_parent']) : '<span class="text-muted">N/A</span>' ?>
                                    </td>
                                    <td>
                                        <?= !empty($eleve['adresse']) ? htmlspecialchars($eleve['adresse']) : '<span class="text-muted">N/A</span>' ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="voir_eleve.php?id=<?= $eleve['id'] ?>" 
                                               class="action-btn btn-primary" 
                                               title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="modifier_eleve.php?id=<?= $eleve['id'] ?>" 
                                               class="action-btn btn-warning" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="supprimer_eleve.php?id=<?= $eleve['id'] ?>" 
                                               class="action-btn btn-danger" 
                                               title="Supprimer" 
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élève ? Cette action est irréversible.')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" 
                                   class="page-link" 
                                   title="Première page">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                                   class="page-link" 
                                   title="Page précédente">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                                   class="page-link <?= $i == $page ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                                   class="page-link" 
                                   title="Page suivante">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>" 
                                   class="page-link" 
                                   title="Dernière page">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Aucun élève trouvé. <?= !empty($search) ? 'Essayez avec un autre terme de recherche.' : '' ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pied de carte -->
            <div class="card-footer">
                <div class="stats">
                    <div>
                        Affichage de <strong><?= count($eleves) ?></strong> sur <strong><?= $total ?></strong> élèves
                    </div>
                    
                </div>
            </div>
        </div>

        <!-- Lien de retour -->
        <a href="dashboard.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>

    <script>
        // Gestion du thème sombre
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;
        
        // Vérifier le thème stocké
        if (localStorage.getItem('darkMode') === 'true') {
            body.classList.add('dark-mode');
            themeToggle.innerHTML = '<i class="fas fa-sun"></i> Thème clair';
        }
        
        // Basculer le thème
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const isDark = body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark);
            themeToggle.innerHTML = isDark 
                ? '<i class="fas fa-sun"></i> Thème clair' 
                : '<i class="fas fa-moon"></i> Thème sombre';
        });

        // Animation de chargement
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.3s ease';
            
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });
    </script>
</body>
</html>