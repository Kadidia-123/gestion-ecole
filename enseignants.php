<?php
require 'config.php';

// Récupérer tous les enseignants
$enseignants = $pdo->query("SELECT * FROM enseignants")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $ville = $_POST['ville'];
    $statut = $_POST['statut'];
    $profession = $_POST['profession'];
    $dernier_diplome = $_POST['dernier_diplome'];
    $telephone = $_POST['telephone'];
    
    $stmt = $pdo->prepare("INSERT INTO enseignants (nom, prenom, email, ville, statut, profession, dernier_diplome, telephone) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $email, $ville, $statut, $profession, $dernier_diplome, $telephone]);
    header("Location: enseignants.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM enseignants WHERE id = ?")->execute([$id]);
    header("Location: enseignants.php");
    exit();
}

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $ens = $pdo->prepare("SELECT * FROM enseignants WHERE id = ?");
    $ens->execute([$id]);
    $ens = $ens->fetch();
    ?>
    <form method="POST" class="edit-form">
        <div class="form-grid">
            <div class="form-group">
                <label>Nom</label>
                <input name="nom" value="<?= htmlspecialchars($ens['nom']) ?>" required>
            </div>
            <div class="form-group">
                <label>Prénom</label>
                <input name="prenom" value="<?= htmlspecialchars($ens['prenom']) ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input name="email" type="email" value="<?= htmlspecialchars($ens['email']) ?>" required>
            </div>
            <div class="form-group">
                <label>Ville</label>
                <input name="ville" value="<?= htmlspecialchars($ens['ville']) ?>">
            </div>
            <div class="form-group">
                <label>Statut</label>
                <select name="statut">
                    <option value="Actif" <?= $ens['statut'] == 'Actif' ? 'selected' : '' ?>>Actif</option>
                    <option value="Inactif" <?= $ens['statut'] == 'Inactif' ? 'selected' : '' ?>>Inactif</option>
                    <option value="Vacataire" <?= $ens['statut'] == 'Vacataire' ? 'selected' : '' ?>>Vacataire</option>
                </select>
            </div>
            <div class="form-group">
                <label>Profession</label>
                <input name="profession" value="<?= htmlspecialchars($ens['profession']) ?>">
            </div>
            <div class="form-group">
                <label>Dernier diplôme</label>
                <input name="dernier_diplome" value="<?= htmlspecialchars($ens['dernier_diplome']) ?>">
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input name="telephone" type="tel" value="<?= htmlspecialchars($ens['telephone']) ?>">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="update" value="<?= $id ?>" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <a href="enseignants.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
    <?php
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['update'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $ville = $_POST['ville'];
    $statut = $_POST['statut'];
    $profession = $_POST['profession'];
    $dernier_diplome = $_POST['dernier_diplome'];
    $telephone = $_POST['telephone'];
    
    $stmt = $pdo->prepare("UPDATE enseignants SET nom=?, prenom=?, email=?, ville=?, statut=?, profession=?, dernier_diplome=?, telephone=? WHERE id=?");
    $stmt->execute([$nom, $prenom, $email, $ville, $statut, $profession, $dernier_diplome, $telephone, $id]);
    header("Location: enseignants.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Enseignants | SchoolAdmin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
            --light-gray: #e9ecef;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f5f7ff;
            color: var(--dark);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            border: none;
            font-size: 0.95rem;
        }

        .btn i {
            font-size: 0.9em;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--gray);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .search-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .table-container {
            overflow-x: auto;
            margin: 20px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .table th {
            background-color: var(--light);
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 2px solid var(--light-gray);
        }

        .table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--light-gray);
            vertical-align: middle;
        }

        .table tr:hover td {
            background-color: rgba(67, 97, 238, 0.05);
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .action-link {
            color: var(--primary);
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }

        .action-link:hover {
            color: var(--primary-dark);
            text-decoration: none;
        }

        .action-link.danger {
            color: var(--danger);
        }

        .action-link.danger:hover {
            color: #d91a5f;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .edit-form {
            background: white;
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--gray);
            font-size: 0.9rem;
            padding: 15px 20px;
            border-top: 1px solid var(--light-gray);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--light-gray);
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .search-container {
                flex-direction: column;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>
                    <i class="fas fa-chalkboard-teacher"></i>
                    Gestion des Enseignants
                </h2>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Tableau de bord
                </a>
            </div>
            
            <div class="card-body">
                <?php if (isset($_GET['edit'])): ?>
                    <!-- Le formulaire d'édition est affiché ici via PHP -->
                <?php else: ?>
                    <form method="POST" class="edit-form">
                        <h3 style="margin-bottom: 20px; color: var(--primary);">
                            <i class="fas fa-plus-circle"></i> Ajouter un nouvel enseignant
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Nom <span class="required">*</span></label>
                                <input name="nom" placeholder="Nom" required>
                            </div>
                            <div class="form-group">
                                <label>Prénom <span class="required">*</span></label>
                                <input name="prenom" placeholder="Prénom" required>
                            </div>
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input name="email" type="email" placeholder="Email" required>
                            </div>
                            <div class="form-group">
                                <label>Ville</label>
                                <input name="ville" placeholder="Ville">
                            </div>
                            <div class="form-group">
                                <label>Statut</label>
                                <select name="statut">
                                    <option value="Actif">Actif</option>
                                    <option value="Inactif">Inactif</option>
                                    <option value="Vacataire">Vacataire</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Profession</label>
                                <input name="profession" placeholder="Profession">
                            </div>
                            <div class="form-group">
                                <label>Dernier diplôme</label>
                                <input name="dernier_diplome" placeholder="Dernier diplôme">
                            </div>
                            <div class="form-group">
                                <label>Téléphone</label>
                                <input name="telephone" type="tel" placeholder="Téléphone">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="add" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
                
                <div class="search-container">
                    <form method="GET" style="flex: 1; display: flex; gap: 10px;">
                        <input type="text" name="search" class="search-input" placeholder="Rechercher un enseignant..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </form>
                </div>
                
                <?php if (!empty($enseignants)): ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enseignants as $ens): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($ens['nom']) ?></strong></td>
                                    <td><?= htmlspecialchars($ens['prenom']) ?></td>
                                    <td><?= htmlspecialchars($ens['email']) ?></td>
                                    <td><?= htmlspecialchars($ens['telephone']) ?></td>
                                    <td>
                                        <span class="badge <?= $ens['statut'] == 'Actif' ? 'badge-success' : ($ens['statut'] == 'Vacataire' ? 'badge-warning' : 'badge-danger') ?>">
                                            <?= htmlspecialchars($ens['statut']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="enseignants.php?edit=<?= $ens['id'] ?>" class="action-link" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="enseignants.php?delete=<?= $ens['id'] ?>" class="action-link danger" title="Supprimer"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet enseignant ?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-user-graduate"></i>
                        <h3>Aucun enseignant trouvé</h3>
                        <p>Commencez par ajouter un nouvel enseignant</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="stats">
                <div>
                    Total: <strong><?= count($enseignants) ?></strong> enseignant(s)
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>