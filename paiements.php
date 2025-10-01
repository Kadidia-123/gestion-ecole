<?php
// Simuler la connexion à la base de données et récupération des élèves
$eleves = [
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
    ["id" => 19, "nom" => "RAMATA BAH ", "classe" => "5ème", "classe_id" => 5],
    ["id" => 20, "nom" => "ZOL DJIGUE", "classe" => "5ème", "classe_id" => 5],
    ["id" => 21, "nom" => "Mohamed ", "classe" => "6ème", "classe_id" => 6],
    ["id" => 22, "nom" => "Fatima Zahra", "classe" => "6ème", "classe_id" => 6],
    ["id" => 23, "nom" => "Jean COULIBALY", "classe" => "6ème", "classe_id" => 6],
    ["id" => 24, "nom" => "MARIAM TRAORE", "classe" => "6ème", "classe_id" => 6],
    ["id" => 25, "nom" => "Paul POUJOUGOU", "classe" => "7ème", "classe_id" => 7],
    ["id" => 26, "nom" => "Sophie KAMATE", "classe" => "7ème", "classe_id" => 7],
    ["id" => 27, "nom" => "Ahmed DIABATE", "classe" => "7ème", "classe_id" => 7],
    ["id" => 28, "nom" => "FANTA KONATE", "classe" => "7ème", "classe_id" => 7],
    ["id" => 29, "nom" => "Antoine TALL", "classe" => "8ème", "classe_id" => 8],
    ["id" => 30, "nom" => "INA cissoko", "classe" => "8ème", "classe_id" => 8],
    ["id" => 31, "nom" => "Karim cisse", "classe" => "8ème", "classe_id" => 8],
    ["id" => 32, "nom" => "Leila traore", "classe" => "8ème", "classe_id" => 8],
    ["id" => 33, "nom" => "Youssouf Ahmed", "classe" => "9ème", "classe_id" => 9],
    ["id" => 34, "nom" => "Clara Morgane", "classe" => "9ème", "classe_id" => 9],
    ["id" => 35, "nom" => "David guisse", "classe" => "9ème", "classe_id" => 9],
    ["id" => 36, "nom" => "Zeinab toure", "classe" => "9ème", "classe_id" => 9]
];

// Grouper les élèves par classe
$elevesParClasse = [];
foreach ($eleves as $eleve) {
    $classe = $eleve['classe'];
    if (!isset($elevesParClasse[$classe])) {
        $elevesParClasse[$classe] = [];
    }
    $elevesParClasse[$classe][] = $eleve;
}

// Classes disponibles
$classes = ["1ème", "2ème", "3ème", "4ème", "5ème", "6ème", "7ème", "8ème", "9ème"];

// Données de paiements (simulées) - CORRIGÉ avec IDs
$paiements = [
    ["id" => 1, "classe" => "1ème", "eleve" => "OUMOU TOURE", "montant" => "120,00 FCFA", "date_paiement" => "15/09/2025", "mois_couvert" => "Septembre 2025", "paye_par" => "Parent", "statut" => "payé"],
    ["id" => 2, "classe" => "2ème", "eleve" => "MATOU KONE", "montant" => "120,00 FCFA", "date_paiement" => "14/09/2025", "mois_couvert" => "Septembre 2025", "paye_par" => "Tuteur", "statut" => "payé"],
    ["id" => 3, "classe" => "3ème", "eleve" => "NABI FOFANA", "montant" => "120,00 FCFA", "date_paiement" => "13/09/2025", "mois_couvert" => "Septembre 2025", "paye_par" => "Parent", "statut" => "en attente"],
    ["id" => 4, "classe" => "4ème", "eleve" => "Sophie SISSOKO", "montant" => "120,00 FCFA", "date_paiement" => "12/09/2025", "mois_couvert" => "Septembre 2025", "paye_par" => "Autre", "statut" => "annulé"],
    ["id" => 5, "classe" => "5ème", "eleve" => "KADI KONE", "montant" => "120,00 FCFA", "date_paiement" => "11/09/2025", "mois_couvert" => "Septembre 2025", "paye_par" => "Parent", "statut" => "payé"]
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion des Paiements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #a5b4fc;
            --secondary: #10b981;
            --secondary-dark: #059669;
            --warning: #f59e0b;
            --warning-dark: #d97706;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --info: #06b6d4;
            --info-dark: #0891b2;
            --dark: #1f2937;
            --darker: #111827;
            --light: #f8fafc;
            --lighter: #f1f5f9;
            --gray: #6b7280;
            --gray-light: #9ca3af;
            --border: #e5e7eb;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius: 12px;
            --radius-lg: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Modern */
        header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1.5rem 0;
            position: relative;
            overflow: hidden;
        }
        
        header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.05)"><polygon points="0,0 1000,50 1000,100 0,100"/></svg>');
            background-size: cover;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.75rem;
            font-weight: 700;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }
        
        .logo i {
            font-size: 1.5rem;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Main Layout */
        .main-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
            padding: 30px 0;
        }
        
        /* Main Content */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        /* Card Modern */
        .card {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        
        .card-header {
            padding: 25px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-title i {
            color: var(--primary);
        }
        
        /* Table Modern */
        .table-container {
            overflow-x: auto;
            padding: 0 25px 25px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        
        thead {
            background: var(--lighter);
        }
        
        th {
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            color: var(--gray);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border);
        }
        
        td {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
            transition: var(--transition);
        }
        
        tbody tr {
            transition: var(--transition);
        }
        
        tbody tr:hover {
            background: var(--lighter);
            transform: scale(1.01);
        }
        
        tbody tr:hover td {
            border-color: var(--primary-light);
        }
        
        /* Status Badges Modern */
        .status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .status::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .status-paid {
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
        }
        
        .status-paid::before {
            background: var(--secondary);
        }
        
        .status-pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }
        
        .status-pending::before {
            background: var(--warning);
        }
        
        .status-cancelled {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        
        .status-cancelled::before {
            background: var(--danger);
        }
        
        /* Buttons Modern */
        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn i {
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, var(--gray) 0%, var(--gray-light) 100%);
            color: white;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(107, 114, 128, 0.3);
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.8rem;
        }
        
        /* Action Buttons */
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            color: white;
            font-size: 0.9rem;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
        
        .edit-btn {
            background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);
        }
        
        .delete-btn {
            background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
        }
        
        /* Modal Modern */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .modal-content {
            background: white;
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 500px;
            box-shadow: var(--shadow-lg);
            transform: scale(0.9);
            opacity: 0;
            animation: modalAppear 0.3s ease forwards;
        }
        
        @keyframes modalAppear {
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .modal-header {
            padding: 25px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }
        
        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            transition: var(--transition);
        }
        
        .close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        
        .modal-body {
            padding: 25px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        /* Form Modern */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 1rem;
            transition: var(--transition);
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 40px;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 30px 0;
            color: var(--gray);
            font-size: 0.9rem;
            border-top: 1px solid var(--border);
            margin-top: 50px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }
            
            .card-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .modal-content {
                max-width: 95%;
            }
            
            .buttons-container {
                flex-direction: column;
                gap: 10px;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease forwards;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .buttons-container {
            display: flex;
            gap: 15px;
            justify-content: space-between;
            margin-top: 25px;
        }
        
        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .buttons-container {
                flex-direction: column;
            }
            
            .buttons-container .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span>École Primaire & Collège - Gestion des Paiements</span>
                </div>
                <div class="user-menu">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="main-layout">
            <main class="main-content">
                <div class="card fade-in">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-list"></i>
                            Liste des Paiements
                        </h2>
                        <div style="display: flex; gap: 15px;">
                            
                            <button class="btn btn-primary" id="addPaymentBtn">
                                <i class="fas fa-plus"></i>
                                Nouveau Paiement
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Classe</th>
                                    <th>Élève</th>
                                    <th>Montant</th>
                                    <th>Date Paiement</th>
                                    <th>Mois Couvert</th>
                                    <th>Payé par</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="paiementsTableBody">
                                <?php foreach ($paiements as $paiement): ?>
                                <tr data-id="<?= $paiement['id'] ?>" class="fade-in">
                                    <td>
                                        <strong><?= $paiement['classe'] ?></strong>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8rem;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <?= $paiement['eleve'] ?>
                                        </div>
                                    </td>
                                    <td>
                                        <strong style="color: var(--primary);"><?= $paiement['montant'] ?></strong>
                                    </td>
                                    <td><?= $paiement['date_paiement'] ?></td>
                                    <td><?= $paiement['mois_couvert'] ?></td>
                                    <td><?= $paiement['paye_par'] ?></td>
                                    <td>
                                        <span class="status status-<?= 
                                            $paiement['statut'] == 'payé' ? 'paid' : 
                                            ($paiement['statut'] == 'en attente' ? 'pending' : 'cancelled')
                                        ?>">
                                            <?= ucfirst($paiement['statut']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="action-btn edit-btn" onclick="editPaiement(<?= $paiement['id'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete-btn" onclick="deletePaiement(<?= $paiement['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal pour ajouter/modifier un paiement -->
    <div class="modal" id="paymentModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modalTitle">Ajouter un Paiement</div>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" id="paiementId" value="">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="class">Classe *</label>
                            <select class="form-control form-select" id="class" required onchange="updateStudents()">
                                <option value="">Sélectionner une classe</option>
                                <?php foreach ($classes as $classe): ?>
                                    <option value="<?= $classe ?>"><?= $classe ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="student">Élève *</label>
                            <select class="form-control form-select" id="student" required>
                                <option value="">Sélectionner d'abord une classe</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="amount">Montant (FCFA) *</label>
                            <input type="number" class="form-control" id="amount" placeholder="Ex: 120.00" step="0.01" min="0" required value="120.00">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="paymentDate">Date de Paiement *</label>
                            <input type="date" class="form-control" id="paymentDate" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="month">Mois Couvert *</label>
                        <select class="form-control form-select" id="month" required>
                            <option value="">Sélectionner un mois</option>
                            <option value="Janvier 2025">Janvier 2025</option>
                            <option value="Février 2025">Février 2025</option>
                            <option value="Mars 2025">Mars 2025</option>
                            <option value="Avril 2025">Avril 2025</option>
                            <option value="Mai 2025">Mai 2025</option>
                            <option value="Juin 2025">Juin 2025</option>
                            <option value="Juillet 2025">Juillet 2025</option>
                            <option value="Août 2025">Août 2025</option>
                            <option value="Septembre 2025" selected>Septembre 2025</option>
                            <option value="Octobre 2025">Octobre 2025</option>
                            <option value="Novembre 2025">Novembre 2025</option>
                            <option value="Décembre 2025">Décembre 2025</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="payer">Payé par *</label>
                            <select class="form-control form-select" id="payer" required>
                                <option value="">Qui effectue le paiement?</option>
                                <option value="Parent" selected>Parent</option>
                                <option value="Tuteur">Tuteur</option>
                                <option value="Élève">Élève</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="status">Statut *</label>
                            <select class="form-control form-select" id="status" required>
                                <option value="payé" selected>Payé</option>
                                <option value="en attente">En attente</option>
                                <option value="annulé">Annulé</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="buttons-container">
                        <button type="button" class="btn btn-secondary" onclick="fermerModal()">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer le Paiement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            &copy; 2025 École Primaire & Collège - Système de Gestion des Paiements
        </div>
    </footer>

    <script>
        // Données des élèves par classe
        const studentsByClass = <?= json_encode($elevesParClasse) ?>;
        
        // Données des paiements (simulées)
        let paiementsData = <?= json_encode($paiements) ?>;
        
        // Fonction pour formater la date correctement
        function formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
        
        // FONCTION CORRIGÉE pour retourner au tableau de bord
        function retourTableauDeBord() {
            if (confirm('Voulez-vous retourner au tableau de bord ?')) {
                // Afficher un message de chargement
                showNotification('Chargement du tableau de bord...', 'info');
                
                // Simuler un délai de chargement
                setTimeout(() => {
                    // Créer un contenu de tableau de bord basique
                    document.body.innerHTML = `
                        <header>
                            <div class="container">
                                <div class="header-content">
                                    <div class="logo">
                                        <div class="logo-icon">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <span>École Primaire & Collège - Tableau de Bord</span>
                                    </div>
                                    <div class="user-menu">
                                        <div class="user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </header>
                        
                        <div class="container">
                            <div style="padding: 30px 0;">
                                <div class="card fade-in">
                                    <div class="card-header">
                                        <h2 class="card-title">
                                            <i class="fas fa-tachometer-alt"></i>
                                            Tableau de Bord
                                        </h2>
                                        <button class="btn btn-primary" onclick="window.location.reload()">
                                            <i class="fas fa-list"></i>
                                            Retour aux Paiements
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div style="text-align: center; padding: 50px 20px;">
                                            <div style="font-size: 4rem; color: var(--primary); margin-bottom: 20px;">
                                                <i class="fas fa-tachometer-alt"></i>
                                            </div>
                                            <h2 style="margin-bottom: 20px; color: var(--dark);">Tableau de Bord</h2>
                                            <p style="color: var(--gray); margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
                                                Bienvenue dans le tableau de bord de gestion de l'école. Ici vous pouvez consulter les statistiques, 
                                                les rapports et gérer l'ensemble du système.
                                            </p>
                                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 40px;">
                                                <div class="card" style="text-align: center; padding: 25px;">
                                                    <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 10px;">
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                    <h3 style="margin-bottom: 10px;">36</h3>
                                                    <p style="color: var(--gray);">Élèves inscrits</p>
                                                </div>
                                                <div class="card" style="text-align: center; padding: 25px;">
                                                    <div style="font-size: 2.5rem; color: var(--secondary); margin-bottom: 10px;">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </div>
                                                    <h3 style="margin-bottom: 10px;">5</h3>
                                                    <p style="color: var(--gray);">Paiements ce mois</p>
                                                </div>
                                                <div class="card" style="text-align: center; padding: 25px;">
                                                    <div style="font-size: 2.5rem; color: var(--info); margin-bottom: 10px;">
                                                        <i class="fas fa-chart-line"></i>
                                                    </div>
                                                    <h3 style="margin-bottom: 10px;">9</h3>
                                                    <p style="color: var(--gray);">Classes actives</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <footer class="footer">
                            <div class="container">
                                &copy; 2025 École Primaire & Collège - Tableau de Bord
                            </div>
                        </footer>
                    `;
                    
                    showNotification('Tableau de bord chargé avec succès', 'success');
                }, 800);
            }
        }
        
        // Fonction pour fermer le modal
        function fermerModal() {
            const modal = document.getElementById('paymentModal');
            modal.style.display = 'none';
        }
        
        // Mise à jour de la liste des élèves en fonction de la classe sélectionnée
        function updateStudents() {
            const classSelect = document.getElementById('class');
            const studentSelect = document.getElementById('student');
            const selectedClass = classSelect.value;
            
            // Vider la liste actuelle des élèves
            studentSelect.innerHTML = '';
            
            if (selectedClass && studentsByClass[selectedClass]) {
                // Ajouter une option par défaut
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Sélectionner un élève';
                studentSelect.appendChild(defaultOption);
                
                // Ajouter les élèves de la classe sélectionnée
                studentsByClass[selectedClass].forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = student.nom;
                    studentSelect.appendChild(option);
                });
            } else {
                // Si aucune classe n'est sélectionnée
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Sélectionner d\'abord une classe';
                studentSelect.appendChild(defaultOption);
            }
        }
        
        // Gestion de la modal
        const modal = document.getElementById('paymentModal');
        const addPaymentBtn = document.getElementById('addPaymentBtn');
        const closeBtn = document.querySelector('.close-btn');
        const modalTitle = document.getElementById('modalTitle');
        const paiementIdField = document.getElementById('paiementId');
        
        addPaymentBtn.addEventListener('click', () => {
            modalTitle.textContent = 'Ajouter un Paiement';
            paiementIdField.value = '';
            document.getElementById('paymentForm').reset();
            
            // Définir les valeurs par défaut
            document.getElementById('amount').value = '120.00';
            document.getElementById('paymentDate').valueAsDate = new Date();
            document.getElementById('month').value = 'Septembre 2025';
            document.getElementById('payer').value = 'Parent';
            document.getElementById('status').value = 'payé';
            
            modal.style.display = 'flex';
        });
        
        closeBtn.addEventListener('click', fermerModal);
        
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                fermerModal();
            }
        });
        
        // Édition d'un paiement
        function editPaiement(id) {
            const paiement = paiementsData.find(p => p.id === id);
            if (!paiement) return;
            
            modalTitle.textContent = 'Modifier le Paiement';
            paiementIdField.value = paiement.id;
            
            // Remplir le formulaire avec les données du paiement
            document.getElementById('class').value = paiement.classe;
            updateStudents();
            
            // Petit délai pour permettre la mise à jour de la liste des élèves
            setTimeout(() => {
                const studentSelect = document.getElementById('student');
                for (let i = 0; i < studentSelect.options.length; i++) {
                    if (studentSelect.options[i].text === paiement.eleve) {
                        studentSelect.selectedIndex = i;
                        break;
                    }
                }
            }, 100);
            
            // Extraire le montant numérique
            const montantNumerique = paiement.montant.replace(' FCFA', '').replace(',', '.');
            document.getElementById('amount').value = parseFloat(montantNumerique);
            
            // Conversion de la date pour l'input date
            const dateParts = paiement.date_paiement.split('/');
            const formattedDate = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
            document.getElementById('paymentDate').value = formattedDate;
            
            document.getElementById('month').value = paiement.mois_couvert;
            document.getElementById('payer').value = paiement.paye_par;
            document.getElementById('status').value = paiement.statut;
            
            modal.style.display = 'flex';
        }
        
        // Suppression d'un paiement
        function deletePaiement(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')) {
                // Supprimer de la base de données simulée
                paiementsData = paiementsData.filter(p => p.id !== id);
                
                // Mettre à jour l'affichage
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) {
                    row.style.animation = 'fadeOut 0.3s ease';
                    setTimeout(() => {
                        row.remove();
                    }, 300);
                }
                
                // Afficher une notification
                showNotification('Paiement supprimé avec succès!', 'success');
            }
        }
        
        // Fonction pour afficher les notifications
        function showNotification(message, type = 'info') {
            // Vérifier si une notification existe déjà
            const existingToast = document.querySelector('.notification-toast');
            if (existingToast) {
                document.body.removeChild(existingToast);
            }
            
            // Créer une notification toast
            const toast = document.createElement('div');
            toast.className = 'notification-toast';
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#6366f1'};
                color: white;
                border-radius: 10px;
                box-shadow: var(--shadow);
                z-index: 10000;
                animation: slideInRight 0.3s ease;
                font-weight: 500;
                max-width: 300px;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }
        
        // Gestion de la soumission du formulaire
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Formulaire soumis');
            
            // Validation de base
            const classValue = document.getElementById('class').value;
            const studentValue = document.getElementById('student').value;
            const amountValue = document.getElementById('amount').value;
            
            if (!classValue || !studentValue || !amountValue) {
                showNotification('Veuillez remplir tous les champs obligatoires (*)', 'error');
                return;
            }
            
            // Récupération des valeurs du formulaire
            const id = paiementIdField.value;
            const selectedClass = document.getElementById('class').value;
            const studentName = document.getElementById('student').options[document.getElementById('student').selectedIndex].text;
            const amount = parseFloat(document.getElementById('amount').value);
            const paymentDate = document.getElementById('paymentDate').value;
            const month = document.getElementById('month').value;
            const payer = document.getElementById('payer').value;
            const status = document.getElementById('status').value;
            
            // Validation du montant
            if (isNaN(amount) || amount <= 0) {
                showNotification('Veuillez entrer un montant valide', 'error');
                return;
            }
            
            // Générer un nouvel ID
            let newId;
            if (id) {
                newId = parseInt(id);
            } else {
                newId = paiementsData.length > 0 ? Math.max(...paiementsData.map(p => p.id)) + 1 : 1;
            }
            
            // Préparer les données du paiement
            const paiementData = {
                id: newId,
                classe: selectedClass,
                eleve: studentName,
                montant: amount.toFixed(2).replace('.', ',') + ' FCFA',
                date_paiement: formatDate(new Date(paymentDate)),
                mois_couvert: month,
                paye_par: payer,
                statut: status
            };
            
            console.log('Données du paiement:', paiementData);
            
            if (id) {
                // MODIFICATION - Mettre à jour un paiement existant
                const index = paiementsData.findIndex(p => p.id === parseInt(id));
                if (index !== -1) {
                    paiementsData[index] = paiementData;
                    
                    // Mettre à jour la ligne dans le tableau
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        row.cells[0].innerHTML = `<strong>${paiementData.classe}</strong>`;
                        row.cells[1].innerHTML = `
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                                ${paiementData.eleve}
                            </div>
                        `;
                        row.cells[2].innerHTML = `<strong style="color: var(--primary);">${paiementData.montant}</strong>`;
                        row.cells[3].textContent = paiementData.date_paiement;
                        row.cells[4].textContent = paiementData.mois_couvert;
                        row.cells[5].textContent = paiementData.paye_par;
                        
                        // Mettre à jour le statut
                        const statusClass = paiementData.statut === 'payé' ? 'paid' : 
                                          paiementData.statut === 'en attente' ? 'pending' : 'cancelled';
                        row.cells[6].innerHTML = `<span class="status status-${statusClass}">${paiementData.statut.charAt(0).toUpperCase() + paiementData.statut.slice(1)}</span>`;
                    }
                    showNotification('Paiement modifié avec succès!', 'success');
                }
            } else {
                // AJOUT - Ajouter un nouveau paiement
                paiementsData.push(paiementData);
                
                // Ajouter la nouvelle ligne au tableau
                const tableBody = document.getElementById('paiementsTableBody');
                const newRow = document.createElement('tr');
                newRow.setAttribute('data-id', paiementData.id);
                newRow.className = 'fade-in';
                
                const statusClass = paiementData.statut === 'payé' ? 'paid' : 
                                  paiementData.statut === 'en attente' ? 'pending' : 'cancelled';
                
                newRow.innerHTML = `
                    <td><strong>${paiementData.classe}</strong></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8rem;">
                                <i class="fas fa-user"></i>
                            </div>
                            ${paiementData.eleve}
                        </div>
                    </td>
                    <td><strong style="color: var(--primary);">${paiementData.montant}</strong></td>
                    <td>${paiementData.date_paiement}</td>
                    <td>${paiementData.mois_couvert}</td>
                    <td>${paiementData.paye_par}</td>
                    <td><span class="status status-${statusClass}">${paiementData.statut.charAt(0).toUpperCase() + paiementData.statut.slice(1)}</span></td>
                    <td>
                        <div class="actions">
                            <button class="action-btn edit-btn" onclick="editPaiement(${paiementData.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" onclick="deletePaiement(${paiementData.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                
                tableBody.appendChild(newRow);
                showNotification('Paiement enregistré avec succès!', 'success');
            }
            
            // Fermer la modal
            fermerModal();
        });
        
        // Définir la date du jour comme valeur par défaut
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('paymentDate').valueAsDate = new Date();
        });
        
        // Ajouter les animations CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
            
            @keyframes fadeOut {
                from {
                    opacity: 1;
                    transform: scale(1);
                }
                to {
                    opacity: 0;
                    transform: scale(0.95);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>