<?php
require_once 'config.php';

// Ajouter une matière
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $nom = $_POST['nom'] ?? '';
    $coefficient = $_POST['coefficient'] ?? '';
    $niveau = $_POST['niveau'] ?? '';

    if ($nom && $coefficient && $niveau) {
        $stmt = $pdo->prepare("INSERT INTO matieres (nom, coefficient, niveau) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $coefficient, $niveau]);
        header("Location: matieres.php");
        exit();
    }
}

// Modifier une matière
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $coefficient = $_POST['coefficient'] ?? '';
    $niveau = $_POST['niveau'] ?? '';

    if ($id && $nom && $coefficient && $niveau) {
        $stmt = $pdo->prepare("UPDATE matieres SET nom = ?, coefficient = ?, niveau = ? WHERE id = ?");
        $stmt->execute([$nom, $coefficient, $niveau, $id]);
        header("Location: matieres.php");
        exit();
    }
}

// Supprimer une matière
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM matieres WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: matieres.php");
    exit();
}

// Récupérer les matières
$matieres = $pdo->query("SELECT * FROM matieres")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Matières</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #eef2ff;
            --primary-dark: #3a56d4;
            --success: #27ae60;
            --success-light: #e6f7e6;
            --warning: #f8961e;
            --warning-light: #fff4e6;
            --danger: #f72585;
            --danger-light: #ffe6ef;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 8px;
            --border-radius-lg: 12px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            background: white;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-title h1 {
            font-weight: 600;
            margin: 0;
            font-size: 1.5rem;
        }

        .card-body {
            padding: 2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            text-decoration: none;
            font-size: 1rem;
        }

        .btn i {
            font-size: 0.9em;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(67, 97, 238, 0.3);
        }

        .btn-success {
            background-color: var(--success);
            color: white;
            box-shadow: 0 4px 6px rgba(39, 174, 96, 0.2);
        }

        .btn-success:hover {
            background-color: #219150;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(39, 174, 96, 0.3);
        }

        .btn-warning {
            background-color: var(--warning);
            color: white;
            box-shadow: 0 4px 6px rgba(248, 150, 30, 0.2);
        }

        .btn-warning:hover {
            background-color: #e67e22;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(248, 150, 30, 0.3);
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
            box-shadow: 0 4px 6px rgba(247, 37, 133, 0.2);
        }

        .btn-danger:hover {
            background-color: #e91e63;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(247, 37, 133, 0.3);
        }

        .btn-secondary {
            background-color: var(--gray);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            padding: 0;
            transition: var(--transition);
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(-3px);
        }

        .form-container {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            box-shadow: var(--box-shadow);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.75rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 500;
            color: var(--dark);
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            transition: var(--transition);
            font-size: 1rem;
            background-color: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }

        .table th {
            background-color: var(--primary-light);
            color: var(--primary-dark);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            position: sticky;
            top: 0;
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid var(--light-gray);
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover td {
            background-color: rgba(67, 97, 238, 0.05);
        }

        .actions {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            width: 100%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray);
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }
            
            .actions {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .btn-sm {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="header-title">
                    <a href="dashboard.php" class="btn btn-back" title="Retour au tableau de bord">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1><i class="fas fa-book"></i> Gestion des Matières</h1>
                </div>
                <button class="btn btn-success" onclick="document.getElementById('addModal').style.display='flex'">
                    <i class="fas fa-plus"></i> Ajouter
                </button>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                
                                <th>Nom</th>
                                <th>Coefficient</th>
                                <th>Niveau</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matieres as $matiere): ?>
                            <tr>
                                
                                <td><?= htmlspecialchars($matiere['nom']) ?></td>
                                <td><?= htmlspecialchars($matiere['coefficient']) ?></td>
                                <td><?= htmlspecialchars($matiere['niveau']) ?></td>
                                <td>
                                    <div class="actions">
                                        <button class="btn btn-primary btn-sm" onclick="openViewModal(<?= $matiere['id'] ?>, '<?= htmlspecialchars($matiere['nom']) ?>', <?= $matiere['coefficient'] ?>, '<?= htmlspecialchars($matiere['niveau']) ?>')">
                                            <i class="fas fa-eye"></i> Voir
                                        </button>
                                        <button class="btn btn-warning btn-sm" onclick="openEditModal(<?= $matiere['id'] ?>, '<?= htmlspecialchars($matiere['nom']) ?>', <?= $matiere['coefficient'] ?>, '<?= htmlspecialchars($matiere['niveau']) ?>')">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                        <a href="matieres.php?delete=<?= $matiere['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette matière ?')">
                                            <i class="fas fa-trash-alt"></i> Supprimer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajout -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-plus"></i> Ajouter une matière</h3>
                <button class="close-btn" onclick="document.getElementById('addModal').style.display='none'">&times;</button>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label for="add_nom" class="form-label">Nom</label>
                    <input type="text" id="add_nom" name="nom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="add_coefficient" class="form-label">Coefficient</label>
                    <input type="number" id="add_coefficient" name="coefficient" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="add_niveau" class="form-label">Niveau</label>
                    <input type="text" id="add_niveau" name="niveau" class="form-control" required>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('addModal').style.display='none'" style="flex: 1;">
                        Annuler
                    </button>
                    <button type="submit" name="add" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Voir -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-eye"></i> Détails de la matière</h3>
                <button class="close-btn" onclick="document.getElementById('viewModal').style.display='none'">&times;</button>
            </div>
            <div class="form-group">
                <label class="form-label">Nom</label>
                <div class="form-control" id="view_nom" style="background-color: var(--light);"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Coefficient</label>
                <div class="form-control" id="view_coefficient" style="background-color: var(--light);"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Niveau</label>
                <div class="form-control" id="view_niveau" style="background-color: var(--light);"></div>
            </div>
            <div style="margin-top: 2rem;">
                <button type="button" class="btn btn-primary" onclick="document.getElementById('viewModal').style.display='none'" style="width: 100%;">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Modifier -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-edit"></i> Modifier la matière</h3>
                <button class="close-btn" onclick="document.getElementById('editModal').style.display='none'">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="edit_nom" class="form-label">Nom</label>
                    <input type="text" id="edit_nom" name="nom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_coefficient" class="form-label">Coefficient</label>
                    <input type="number" id="edit_coefficient" name="coefficient" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_niveau" class="form-label">Niveau</label>
                    <input type="text" id="edit_niveau" name="niveau" class="form-control" required>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('editModal').style.display='none'" style="flex: 1;">
                        Annuler
                    </button>
                    <button type="submit" name="update" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fonction pour ouvrir le modal de visualisation
        function openViewModal(id, nom, coefficient, niveau) {
            document.getElementById('view_id').textContent = id;
            document.getElementById('view_nom').textContent = nom;
            document.getElementById('view_coefficient').textContent = coefficient;
            document.getElementById('view_niveau').textContent = niveau;
            document.getElementById('viewModal').style.display = 'flex';
        }

        // Fonction pour ouvrir le modal d'édition
        function openEditModal(id, nom, coefficient, niveau) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nom').value = nom;
            document.getElementById('edit_coefficient').value = coefficient;
            document.getElementById('edit_niveau').value = niveau;
            document.getElementById('editModal').style.display = 'flex';
        }

        // Fermer les modals quand on clique en dehors
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>