<?php
session_start();

// Classe EmploisTemps
class EmploisTemps {
    private $simulation_data = [];
    
    public function __construct() {
        $this->initSimulationData();
    }
    
    private function initSimulationData() {
        // Données d'exemple pour la simulation
        $this->simulation_data = [
            [
                'id' => 1,
                'classe_id' => 1,
                'classe_nom' => '1ère Année',
                'jour' => 'Lundi',
                'heure_debut' => '07:45:00',
                'heure_fin' => '09:45:00',
                'matiere_id' => 1,
                'matiere_nom' => 'Mathématiques',
                'enseignant_id' => 1,
                'enseignant_nom' => 'Coulibaly',
                'enseignant_prenom' => 'Oumar'
            ],
            [
                'id' => 2,
                'classe_id' => 1,
                'classe_nom' => '1ère Année',
                'jour' => 'Lundi',
                'heure_debut' => '10:00:00',
                'heure_fin' => '12:00:00',
                'matiere_id' => 2,
                'matiere_nom' => 'Français',
                'enseignant_id' => 2,
                'enseignant_nom' => 'Kone',
                'enseignant_prenom' => 'Marietou'
            ],
            [
                'id' => 3,
                'classe_id' => 1,
                'classe_nom' => '1ère Année',
                'jour' => 'Lundi',
                'heure_debut' => '15:00:00',
                'heure_fin' => '17:00:00',
                'matiere_id' => 3,
                'matiere_nom' => 'Histoire-Géographie',
                'enseignant_id' => 3,
                'enseignant_nom' => 'Traore',
                'enseignant_prenom' => 'Moussa'
            ]
        ];
    }
    
    public function create($classe_id, $jour, $heure_debut, $heure_fin, $matiere_id, $enseignant_id) {
        $new_id = count($this->simulation_data) + 1;
        
        $classes = $this->getClasses();
        $matieres = $this->getMatieres();
        $enseignants = $this->getEnseignants();
        
        $new_cours = [
            'id' => $new_id,
            'classe_id' => $classe_id,
            'classe_nom' => $classes[$classe_id] ?? 'Classe Inconnue',
            'jour' => $jour,
            'heure_debut' => $heure_debut,
            'heure_fin' => $heure_fin,
            'matiere_id' => $matiere_id,
            'matiere_nom' => $matieres[$matiere_id] ?? 'Matière Inconnue',
            'enseignant_id' => $enseignant_id,
            'enseignant_nom' => $enseignants[$enseignant_id]['nom'] ?? 'Inconnu',
            'enseignant_prenom' => $enseignants[$enseignant_id]['prenom'] ?? 'Inconnu'
        ];
        
        $this->simulation_data[] = $new_cours;
        return true;
    }
    
    public function readAll() {
        return $this->simulation_data;
    }
    
    public function readOne($id) {
        foreach ($this->simulation_data as $cours) {
            if ($cours['id'] == $id) {
                return $cours;
            }
        }
        return null;
    }
    
    public function update($id, $classe_id, $jour, $heure_debut, $heure_fin, $matiere_id, $enseignant_id) {
        foreach ($this->simulation_data as &$cours) {
            if ($cours['id'] == $id) {
                $classes = $this->getClasses();
                $matieres = $this->getMatieres();
                $enseignants = $this->getEnseignants();
                
                $cours['classe_id'] = $classe_id;
                $cours['classe_nom'] = $classes[$classe_id] ?? 'Classe Inconnue';
                $cours['jour'] = $jour;
                $cours['heure_debut'] = $heure_debut;
                $cours['heure_fin'] = $heure_fin;
                $cours['matiere_id'] = $matiere_id;
                $cours['matiere_nom'] = $matieres[$matiere_id] ?? 'Matière Inconnue';
                $cours['enseignant_id'] = $enseignant_id;
                $cours['enseignant_nom'] = $enseignants[$enseignant_id]['nom'] ?? 'Inconnu';
                $cours['enseignant_prenom'] = $enseignants[$enseignant_id]['prenom'] ?? 'Inconnu';
                
                return true;
            }
        }
        return false;
    }
    
    public function delete($id) {
        foreach ($this->simulation_data as $key => $cours) {
            if ($cours['id'] == $id) {
                unset($this->simulation_data[$key]);
                $this->simulation_data = array_values($this->simulation_data);
                return true;
            }
        }
        return false;
    }
    
    public function checkConflict($classe_id, $jour, $heure_debut, $heure_fin, $exclude_id = null) {
        foreach ($this->simulation_data as $cours) {
            if ($cours['id'] != $exclude_id && 
                $cours['classe_id'] == $classe_id && 
                $cours['jour'] == $jour &&
                $cours['heure_debut'] == $heure_debut) {
                return true;
            }
        }
        return false;
    }
    
    public function readByClasse($classe_id) {
        $result = [];
        foreach ($this->simulation_data as $cours) {
            if ($cours['classe_id'] == $classe_id) {
                $result[] = $cours;
            }
        }
        return $result;
    }
    
    // Vérifier si c'est une plage horaire de récréation
    public function isRecreation($heure_debut, $heure_fin) {
        return ($heure_debut == '09:45:00' && $heure_fin == '10:00:00');
    }
    
    // Vérifier si c'est une plage horaire de pause déjeuner
    public function isPauseDejeuner($heure_debut, $heure_fin) {
        return ($heure_debut >= '12:00:00' && $heure_fin <= '14:00:00');
    }
    
    // Vérifier si un cours chevauche la pause déjeuner
    public function chevauchePauseDejeuner($heure_debut, $heure_fin) {
        return ($heure_debut < '14:00:00' && $heure_fin > '12:00:00');
    }
    
    // Vérifier si les horaires sont dans les créneaux autorisés
    public function isHoraireValide($heure_debut, $heure_fin) {
        $creneaux_autorises = [
            ['debut' => '07:45:00', 'fin' => '09:45:00'],
            ['debut' => '10:00:00', 'fin' => '12:00:00'],
            ['debut' => '15:00:00', 'fin' => '17:00:00']
        ];
        
        foreach ($creneaux_autorises as $creneau) {
            if ($heure_debut === $creneau['debut'] && $heure_fin === $creneau['fin']) {
                return true;
            }
        }
        return false;
    }
    
    // Obtenir les créneaux disponibles pour une classe et un jour
    public function getCreneauxDisponibles($classe_id, $jour) {
        $creneaux_autorises = [
            '07:45:00' => '07:45 - 09:45',
            '10:00:00' => '10:00 - 12:00', 
            '15:00:00' => '15:00 - 17:00'
        ];
        
        $creneaux_occupes = [];
        foreach ($this->simulation_data as $cours) {
            if ($cours['classe_id'] == $classe_id && $cours['jour'] == $jour) {
                $creneaux_occupes[] = $cours['heure_debut'];
            }
        }
        
        $creneaux_disponibles = [];
        foreach ($creneaux_autorises as $heure => $label) {
            if (!in_array($heure, $creneaux_occupes)) {
                $creneaux_disponibles[$heure] = $label;
            }
        }
        
        return $creneaux_disponibles;
    }
    
    // Méthodes pour obtenir les listes
    public function getClasses() {
        return [
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
    }
    
    public function getMatieres() {
        return [
            1 => 'Mathématiques',
            2 => 'Français',
            3 => 'Histoire-Géographie',
            4 => 'Sciences',
            5 => 'Anglais',
            6 => 'Éducation Physique',
            7 => 'Arts Plastiques',
            8 => 'Musique',
            9 => 'Technologie',
            10 => 'Philosophie'
        ];
    }
    
    public function getEnseignants() {
        return [
            1 => ['nom' => 'Coulibaly', 'prenom' => 'Oumar'],
            2 => ['nom' => 'Kone', 'prenom' => 'Marietou'],
            3 => ['nom' => 'Traore', 'prenom' => 'Moussa'],
            4 => ['nom' => 'Maige', 'prenom' => 'Sophie'],
            5 => ['nom' => 'Toure', 'prenom' => 'Leila Kane'],
            6 => ['nom' => 'Bouare', 'prenom' => 'Anna'],
            7 => ['nom' => 'Konate', 'prenom' => 'Modibo'],
            8 => ['nom' => 'Doumbia', 'prenom' => 'Kadidia'],
            9 => ['nom' => 'Haidara', 'prenom' => 'Oumou'],
            10 => ['nom' => 'Guindo', 'prenom' => 'Issa']
        ];
    }
    
    public function getJours() {
        return ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    }
    
    public function getHoraires() {
        return [
            '07:45:00' => '07:45',
            '09:45:00' => '09:45',
            '10:00:00' => '10:00',
            '12:00:00' => '12:00',
            '15:00:00' => '15:00',
            '17:00:00' => '17:00'
        ];
    }
    
    // Obtenir les informations de récréation
    public function getRecreationInfo() {
        return [
            'heure_debut' => '09:45:00',
            'heure_fin' => '10:00:00',
            'label' => 'Récréation',
            'couleur' => '#ff6b6b'
        ];
    }
    
    // Obtenir les informations de pause déjeuner
    public function getPauseDejeunerInfo() {
        return [
            'heure_debut' => '12:00:00',
            'heure_fin' => '14:00:00',
            'label' => 'Pause Déjeuner',
            'couleur' => '#fd7e14'
        ];
    }
    
    // Obtenir les créneaux horaires autorisés
    public function getCreneauxAutorises() {
        return [
            [
                'debut' => '07:45:00',
                'fin' => '09:45:00',
                'label' => 'Matinée - 1ère période',
                'duree' => '2h00'
            ],
            [
                'debut' => '10:00:00',
                'fin' => '12:00:00',
                'label' => 'Matinée - 2ème période',
                'duree' => '2h00'
            ],
            [
                'debut' => '15:00:00',
                'fin' => '17:00:00',
                'label' => 'Après-midi',
                'duree' => '2h00'
            ]
        ];
    }
    
    // Organiser les cours par période pour l'affichage tableau
    public function organiserCoursParPeriode($cours) {
        $periodes = [
            'matin_1' => ['debut' => '07:45:00', 'fin' => '09:45:00', 'label' => '7H45 - 9H45'],
            'recreation' => ['debut' => '09:45:00', 'fin' => '10:00:00', 'label' => 'Récréation'],
            'matin_2' => ['debut' => '10:00:00', 'fin' => '12:00:00', 'label' => '10H - 12H'],
            'dejeuner' => ['debut' => '12:00:00', 'fin' => '14:00:00', 'label' => 'Pause Déjeuner'],
            'apres_midi' => ['debut' => '15:00:00', 'fin' => '17:00:00', 'label' => '15H - 17H']
        ];
        
        $cours_organises = [];
        foreach ($this->getJours() as $jour) {
            $cours_organises[$jour] = [];
            foreach ($periodes as $periode_key => $periode) {
                $cours_organises[$jour][$periode_key] = [
                    'label' => $periode['label'],
                    'cours' => []
                ];
                
                // Pour les périodes de cours, chercher les cours correspondants
                if (in_array($periode_key, ['matin_1', 'matin_2', 'apres_midi'])) {
                    foreach ($cours as $c) {
                        if ($c['jour'] == $jour && $c['heure_debut'] == $periode['debut']) {
                            $cours_organises[$jour][$periode_key]['cours'][] = $c;
                        }
                    }
                }
            }
        }
        
        return $cours_organises;
    }
}

// Initialisation
$emploisTemps = new EmploisTemps();

// Sauvegarder les données en session pour persistance
if (!isset($_SESSION['emplois_temps_data'])) {
    $_SESSION['emplois_temps_data'] = $emploisTemps->readAll();
}

// Charger les données depuis la session
$emploisTemps_data = &$_SESSION['emplois_temps_data'];

// Traitement des actions
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? $_GET['id'] : null;
$classe_id = isset($_GET['classe_id']) ? $_GET['classe_id'] : null;

// Traitement du formulaire d'ajout/modification
if ($_POST && isset($_POST['classe_id'])) {
    // Récupérer les données du formulaire
    $classe_id = $_POST['classe_id'];
    $jour = $_POST['jour'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    $matiere_id = $_POST['matiere_id'];
    $enseignant_id = $_POST['enseignant_id'];
    
    // Validation basique
    if (empty($classe_id) || empty($jour) || empty($heure_debut) || empty($heure_fin) || empty($matiere_id) || empty($enseignant_id)) {
        $_SESSION['message'] = "Tous les champs sont obligatoires!";
        $_SESSION['message_type'] = "danger";
        header("Location: emplois_temps.php");
        exit();
    }
    
    // Vérifier que les horaires sont dans les créneaux autorisés
    if (!$emploisTemps->isHoraireValide($heure_debut, $heure_fin)) {
        $_SESSION['message'] = "Les horaires doivent correspondre aux créneaux autorisés (7H45-9H45, 10H-12H, 15H-17H)!";
        $_SESSION['message_type'] = "warning";
        header("Location: emplois_temps.php");
        exit();
    }
    
    // Vérifier les conflits - UN SEUL cours par créneau pour chaque classe
    $conflict = $emploisTemps->checkConflict($classe_id, $jour, $heure_debut, $heure_fin);
    
    if ($conflict) {
        $_SESSION['message'] = "Conflit d'horaire détecté! Cette classe a déjà un cours programmé à ce créneau horaire.";
        $_SESSION['message_type'] = "warning";
        header("Location: emplois_temps.php");
        exit();
    }
    
    // Créer un nouveau cours
    $new_id = count($emploisTemps_data) + 1;
    $classes = $emploisTemps->getClasses();
    $matieres = $emploisTemps->getMatieres();
    $enseignants = $emploisTemps->getEnseignants();
    
    $new_cours = [
        'id' => $new_id,
        'classe_id' => $classe_id,
        'classe_nom' => $classes[$classe_id],
        'jour' => $jour,
        'heure_debut' => $heure_debut,
        'heure_fin' => $heure_fin,
        'matiere_id' => $matiere_id,
        'matiere_nom' => $matieres[$matiere_id],
        'enseignant_id' => $enseignant_id,
        'enseignant_nom' => $enseignants[$enseignant_id]['nom'],
        'enseignant_prenom' => $enseignants[$enseignant_id]['prenom']
    ];
    
    $emploisTemps_data[] = $new_cours;
    $_SESSION['message'] = "Cours ajouté avec succès!";
    $_SESSION['message_type'] = "success";
    
    header("Location: emplois_temps.php");
    exit();
}

// Traitement de la suppression
if ($action == 'delete' && $id) {
    foreach ($emploisTemps_data as $key => $cours) {
        if ($cours['id'] == $id) {
            unset($emploisTemps_data[$key]);
            $emploisTemps_data = array_values($emploisTemps_data);
            $_SESSION['message'] = "Cours supprimé avec succès!";
            $_SESSION['message_type'] = "success";
            break;
        }
    }
    header("Location: emplois_temps.php");
    exit();
}

// Récupérer les données pour les selects
$classes = $emploisTemps->getClasses();
$matieres = $emploisTemps->getMatieres();
$enseignants_data = $emploisTemps->getEnseignants();
$jours = $emploisTemps->getJours();
$horaires = $emploisTemps->getHoraires();
$recreation_info = $emploisTemps->getRecreationInfo();
$pause_dejeuner_info = $emploisTemps->getPauseDejeunerInfo();
$creneaux_autorises = $emploisTemps->getCreneauxAutorises();

// Préparer les enseignants pour l'affichage dans les selects
$enseignants = [];
foreach ($enseignants_data as $key => $enseignant) {
    $enseignants[$key] = $enseignant['prenom'] . ' ' . $enseignant['nom'];
}

// Utiliser les données de la session
$cours = $emploisTemps_data;
$total = count($cours);

// Organiser les cours pour l'affichage tableau
$cours_organises = $emploisTemps->organiserCoursParPeriode($cours);

// Récupérer les créneaux disponibles pour le formulaire
$creneaux_disponibles = [];
if (isset($_POST['classe_id']) && isset($_POST['jour'])) {
    $creneaux_disponibles = $emploisTemps->getCreneauxDisponibles($_POST['classe_id'], $_POST['jour']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Emplois du Temps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .table-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
        }
        .emploi-table {
            width: 100%;
            border-collapse: collapse;
        }
        .emploi-table th {
            background: #e9ecef;
            padding: 15px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #dee2e6;
        }
        .emploi-table td {
            padding: 12px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }
        .periode-header {
            background: #f8f9fa;
            font-weight: 600;
            text-align: center;
            width: 120px;
        }
        .cours-item {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 5px;
            border-left: 4px solid rgba(255,255,255,0.3);
        }
        .cours-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .recreation-cell {
            background: #fff5f5;
            color: #e53e3e;
            text-align: center;
            font-weight: 600;
        }
        .pause-cell {
            background: #fff4e6;
            color: #dd6b20;
            text-align: center;
            font-weight: 600;
        }
        .cours-time {
            font-weight: 600;
            font-size: 0.8rem;
            margin-bottom: 3px;
        }
        .cours-matiere {
            font-weight: 500;
            margin-bottom: 2px;
            font-size: 0.9rem;
        }
        .cours-enseignant {
            font-size: 0.75rem;
            opacity: 0.9;
        }
        .cours-classe {
            font-size: 0.7rem;
            background: rgba(255,255,255,0.2);
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
        }
        .action-buttons {
            display: flex;
            gap: 3px;
            margin-top: 5px;
        }
        .action-buttons .btn {
            font-size: 0.6rem;
            padding: 2px 6px;
        }
        .stats-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #007bff;
        }
        .stats-label {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
        }
        .classe-selector {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .creneau-info {
            background: #e7f3ff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .creneau-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }
        .creneau-heures {
            background: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
            min-width: 120px;
            text-align: center;
        }
        .empty-cell {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
        }
        .table-responsive {
            border-radius: 0 0 10px 10px;
        }
        .creneau-disponible {
            background: #d4edda;
            color: #155724;
            padding: 8px 12px;
            border-radius: 4px;
            margin: 5px 0;
            border-left: 4px solid #28a745;
        }
        .creneau-occupe {
            background: #f8d7da;
            color: #721c24;
            padding: 8px 12px;
            border-radius: 4px;
            margin: 5px 0;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="emplois_temps.php">
                <i class="fas fa-calendar-alt me-2"></i>Gestion Emplois du Temps
            </a>
            <div class="navbar-nav ms-auto">
                <a href="dashboard.php" class="btn btn-light btn-sm">
                    <i class="fas fa-tachometer-alt me-1"></i>Retour au tableau de bord
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <?php if ($action == 'add' || $action == 'edit'): ?>
            <!-- Formulaire d'ajout/modification -->
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-header <?php echo $action == 'add' ? 'bg-success' : 'bg-warning'; ?> text-white">
                            <h4 class="mb-0">
                                <i class="fas <?php echo $action == 'add' ? 'fa-plus' : 'fa-edit'; ?> me-2"></i>
                                <?php echo $action == 'add' ? 'Ajouter un Cours' : 'Modifier le Cours'; ?>
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php
                            $cours_data = null;
                            if ($action == 'edit' && $id) {
                                foreach ($emploisTemps_data as $cours) {
                                    if ($cours['id'] == $id) {
                                        $cours_data = $cours;
                                        break;
                                    }
                                }
                            }
                            
                            // Récupérer les créneaux disponibles si une classe et un jour sont sélectionnés
                            $creneaux_disponibles_form = [];
                            if (isset($_POST['classe_id']) && isset($_POST['jour'])) {
                                $creneaux_disponibles_form = $emploisTemps->getCreneauxDisponibles($_POST['classe_id'], $_POST['jour']);
                            } elseif ($cours_data) {
                                $creneaux_disponibles_form = $emploisTemps->getCreneauxDisponibles($cours_data['classe_id'], $cours_data['jour']);
                            }
                            ?>
                            
                            <form method="POST" action="emplois_temps.php" id="coursForm">
                                <?php if ($action == 'edit' && $cours_data): ?>
                                    <input type="hidden" name="id" value="<?php echo $cours_data['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="creneau-info">
                                    <h6 class="mb-3"><i class="fas fa-clock me-2"></i>Créneaux horaires autorisés :</h6>
                                    <?php foreach ($creneaux_autorises as $creneau): ?>
                                        <div class="creneau-item">
                                            <span class="creneau-heures">
                                                <?php echo substr($creneau['debut'], 0, 5) . ' - ' . substr($creneau['fin'], 0, 5); ?>
                                            </span>
                                            <span class="text-muted"><?php echo $creneau['label']; ?> (<?php echo $creneau['duree']; ?>)</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if (!empty($creneaux_disponibles_form)): ?>
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Créneaux disponibles :</h6>
                                    <?php 
                                    $creneaux_autorises_list = [
                                        '07:45:00' => '07:45 - 09:45',
                                        '10:00:00' => '10:00 - 12:00',
                                        '15:00:00' => '15:00 - 17:00'
                                    ];
                                    
                                    foreach ($creneaux_autorises_list as $heure => $label): 
                                        $disponible = in_array($heure, array_keys($creneaux_disponibles_form));
                                    ?>
                                        <div class="<?php echo $disponible ? 'creneau-disponible' : 'creneau-occupe'; ?>">
                                            <i class="fas <?php echo $disponible ? 'fa-check' : 'fa-times'; ?> me-2"></i>
                                            <?php echo $label; ?>
                                            <?php if ($disponible): ?>
                                                <span class="badge bg-success">Disponible</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Occupé</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Classe</label>
                                        <select name="classe_id" class="form-select" required id="classeSelect">
                                            <option value="">Choisir une classe</option>
                                            <?php foreach ($classes as $key => $value): ?>
                                                <option value="<?php echo $key; ?>" 
                                                    <?php echo ($cours_data && $cours_data['classe_id'] == $key) ? 'selected' : ''; ?>
                                                    <?php echo (isset($_POST['classe_id']) && $_POST['classe_id'] == $key) ? 'selected' : ''; ?>>
                                                    <?php echo $value; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jour</label>
                                        <select name="jour" class="form-select" required id="jourSelect">
                                            <option value="">Choisir un jour</option>
                                            <?php foreach ($jours as $jour): ?>
                                                <option value="<?php echo $jour; ?>" 
                                                    <?php echo ($cours_data && $cours_data['jour'] == $jour) ? 'selected' : ''; ?>
                                                    <?php echo (isset($_POST['jour']) && $_POST['jour'] == $jour) ? 'selected' : ''; ?>>
                                                    <?php echo $jour; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Heure de début</label>
                                        <select name="heure_debut" class="form-select" required id="heureDebutSelect">
                                            <option value="">Choisir l'heure de début</option>
                                            <?php 
                                            $creneaux_list = [
                                                '07:45:00' => '07:45',
                                                '10:00:00' => '10:00', 
                                                '15:00:00' => '15:00'
                                            ];
                                            foreach ($creneaux_list as $heure => $label): 
                                                $disponible = true;
                                                if (isset($_POST['classe_id']) && isset($_POST['jour'])) {
                                                    $disponible = in_array($heure, array_keys($creneaux_disponibles_form));
                                                }
                                            ?>
                                                <option value="<?php echo $heure; ?>" 
                                                    <?php echo ($cours_data && $cours_data['heure_debut'] == $heure) ? 'selected' : ''; ?>
                                                    <?php echo (!$disponible && !$cours_data) ? 'disabled' : ''; ?>>
                                                    <?php echo $label; ?> 
                                                    <?php if (!$disponible && !$cours_data): ?>
                                                        (Occupé)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i> Seuls les créneaux disponibles sont sélectionnables
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Heure de fin</label>
                                        <select name="heure_fin" class="form-select" required id="heureFinSelect">
                                            <option value="">Choisir l'heure de fin</option>
                                            <option value="09:45:00" <?php echo ($cours_data && $cours_data['heure_fin'] == '09:45:00') ? 'selected' : ''; ?>>09:45</option>
                                            <option value="12:00:00" <?php echo ($cours_data && $cours_data['heure_fin'] == '12:00:00') ? 'selected' : ''; ?>>12:00</option>
                                            <option value="17:00:00" <?php echo ($cours_data && $cours_data['heure_fin'] == '17:00:00') ? 'selected' : ''; ?>>17:00</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Matière</label>
                                        <select name="matiere_id" class="form-select" required>
                                            <option value="">Choisir une matière</option>
                                            <?php foreach ($matieres as $key => $value): ?>
                                                <option value="<?php echo $key; ?>" 
                                                    <?php echo ($cours_data && $cours_data['matiere_id'] == $key) ? 'selected' : ''; ?>>
                                                    <?php echo $value; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Enseignant</label>
                                        <select name="enseignant_id" class="form-select" required>
                                            <option value="">Choisir un enseignant</option>
                                            <?php foreach ($enseignants as $key => $value): ?>
                                                <option value="<?php echo $key; ?>" 
                                                    <?php echo ($cours_data && $cours_data['enseignant_id'] == $key) ? 'selected' : ''; ?>>
                                                    <?php echo $value; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn <?php echo $action == 'add' ? 'btn-success' : 'btn-warning'; ?>">
                                        <i class="fas <?php echo $action == 'add' ? 'fa-save' : 'fa-edit'; ?> me-1"></i>
                                        <?php echo $action == 'add' ? 'Ajouter le Cours' : 'Modifier le Cours'; ?>
                                    </button>
                                    <a href="emplois_temps.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>Retour
                                    </a>
                                    <button type="button" class="btn btn-info" onclick="actualiserDisponibilite()">
                                        <i class="fas fa-sync-alt me-1"></i>Actualiser les disponibilités
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Vue Tableau Organisé -->
            
            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="stats-number"><?php echo $total; ?></div>
                        <div class="stats-label">Cours Programmes</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="stats-number"><?php echo count($classes); ?></div>
                        <div class="stats-label">Classes</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="stats-number"><?php echo count($matieres); ?></div>
                        <div class="stats-label">Matières</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="stats-number"><?php echo count($enseignants); ?></div>
                        <div class="stats-label">Enseignants</div>
                    </div>
                </div>
            </div>

            <!-- Sélecteur de classe -->
            <div class="classe-selector">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar me-2"></i>
                            Emploi du Temps
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="classeSelector" onchange="changeClasse(this.value)">
                            <option value="">Toutes les classes</option>
                            <?php foreach ($classes as $key => $value): ?>
                                <option value="<?php echo $key; ?>" <?php echo $classe_id == $key ? 'selected' : ''; ?>>
                                    <?php echo $value; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 text-end">
                        <a href="emplois_temps.php?action=add" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Nouveau Cours
                        </a>
                    </div>
                </div>
            </div>

            <!-- Information des créneaux -->
            <div class="creneau-info mb-4">
                <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Organisation de la journée :</h6>
                <div class="row">
                    <?php foreach ($creneaux_autorises as $creneau): ?>
                        <div class="col-md-4">
                            <div class="creneau-item">
                                <span class="creneau-heures">
                                    <?php echo substr($creneau['debut'], 0, 5) . ' - ' . substr($creneau['fin'], 0, 5); ?>
                                </span>
                                <span class="text-muted"><?php echo $creneau['label']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tableau Emploi du Temps -->
            <div class="table-container">
                <div class="table-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-0">
                                <i class="fas fa-school me-2"></i>
                                <?php 
                                if ($classe_id && isset($classes[$classe_id])) {
                                    echo $classes[$classe_id];
                                } else {
                                    echo 'Toutes les Classes';
                                }
                                ?>
                            </h3>
                            <p class="mb-0 mt-1">Semaine du <?php echo date('d/m/Y'); ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group">
                                <button class="btn btn-light" onclick="previousWeek()">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="btn btn-light" onclick="nextWeek()">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="emploi-table">
                        <thead>
                            <tr>
                                <th class="periode-header">Périodes</th>
                                <?php foreach ($jours as $jour): ?>
                                    <th><?php echo $jour; ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $periodes = [
                                'matin_1' => '7H45 - 9H45',
                                'recreation' => 'Récréation',
                                'matin_2' => '10H - 12H', 
                                'dejeuner' => 'Pause Déjeuner',
                                'apres_midi' => '15H - 17H'
                            ];
                            
                            foreach ($periodes as $periode_key => $periode_label): ?>
                                <tr>
                                    <td class="periode-header">
                                        <?php echo $periode_label; ?>
                                    </td>
                                    <?php foreach ($jours as $jour): ?>
                                        <td class="<?php 
                                            if ($periode_key == 'recreation') echo 'recreation-cell';
                                            elseif ($periode_key == 'dejeuner') echo 'pause-cell';
                                            else echo 'cours-cell';
                                        ?>">
                                            <?php if (in_array($periode_key, ['recreation', 'dejeuner'])): ?>
                                                <div class="text-center py-2">
                                                    <i class="fas <?php echo $periode_key == 'recreation' ? 'fa-coffee' : 'fa-utensils'; ?> me-2"></i>
                                                    <?php echo $periode_label; ?>
                                                </div>
                                            <?php else: ?>
                                                <?php if (!empty($cours_organises[$jour][$periode_key]['cours'])): ?>
                                                    <?php foreach ($cours_organises[$jour][$periode_key]['cours'] as $cours): 
                                                        // Filtrer par classe si une classe est sélectionnée
                                                        if ($classe_id && $cours['classe_id'] != $classe_id) continue;
                                                        $couleur_matiere = getCouleurMatiere($cours['matiere_id']);
                                                    ?>
                                                        <div class="cours-item" style="background: <?php echo $couleur_matiere; ?>;">
                                                            <div class="cours-time">
                                                                <?php echo substr($cours['heure_debut'], 0, 5) . ' - ' . substr($cours['heure_fin'], 0, 5); ?>
                                                            </div>
                                                            <div class="cours-matiere">
                                                                <?php echo $cours['matiere_nom']; ?>
                                                            </div>
                                                            <?php if (!$classe_id): ?>
                                                                <div class="cours-classe">
                                                                    <?php echo $cours['classe_nom']; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="cours-enseignant">
                                                                <?php echo $cours['enseignant_prenom'] . ' ' . $cours['enseignant_nom']; ?>
                                                            </div>
                                                            <div class="action-buttons">
                                                                <a href="emplois_temps.php?action=edit&id=<?php echo $cours['id']; ?>" 
                                                                   class="btn btn-warning btn-sm">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="emplois_temps.php?action=delete&id=<?php echo $cours['id']; ?>" 
                                                                   class="btn btn-danger btn-sm" 
                                                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="empty-cell">
                                                        <i class="fas fa-clock"></i><br>
                                                        Aucun cours
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-light text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Gestion Emplois du Temps</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function changeClasse(classeId) {
            if (classeId) {
                window.location.href = 'emplois_temps.php?classe_id=' + classeId;
            } else {
                window.location.href = 'emplois_temps.php';
            }
        }

        function previousWeek() {
            alert('Fonctionnalité de navigation par semaine à implémenter');
        }

        function nextWeek() {
            alert('Fonctionnalité de navigation par semaine à implémenter');
        }

        function actualiserDisponibilite() {
            const classeSelect = document.getElementById('classeSelect');
            const jourSelect = document.getElementById('jourSelect');
            
            if (classeSelect.value && jourSelect.value) {
                document.getElementById('coursForm').submit();
            } else {
                alert('Veuillez d\'abord sélectionner une classe et un jour');
            }
        }

        // Mettre à jour les disponibilités quand la classe ou le jour change
        document.getElementById('classeSelect')?.addEventListener('change', function() {
            const jourSelect = document.getElementById('jourSelect');
            if (this.value && jourSelect.value) {
                setTimeout(() => {
                    document.getElementById('coursForm').submit();
                }, 500);
            }
        });

        document.getElementById('jourSelect')?.addEventListener('change', function() {
            const classeSelect = document.getElementById('classeSelect');
            if (this.value && classeSelect.value) {
                setTimeout(() => {
                    document.getElementById('coursForm').submit();
                }, 500);
            }
        });

        // Synchroniser heure de début et heure de fin
        document.getElementById('heureDebutSelect')?.addEventListener('change', function() {
            const heureFinSelect = document.getElementById('heureFinSelect');
            const correspondances = {
                '07:45:00': '09:45:00',
                '10:00:00': '12:00:00',
                '15:00:00': '17:00:00'
            };
            
            if (this.value in correspondances) {
                heureFinSelect.value = correspondances[this.value];
            }
        });
    </script>
</body>
</html>

<?php
// Fonction pour générer des couleurs différentes pour chaque matière
function getCouleurMatiere($matiere_id) {
    $couleurs = [
        '#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8',
        '#6f42c1', '#e83e8c', '#fd7e14', '#20c997', '#6610f2',
        '#0dcaf0', '#198754', '#ff6b6b', '#4ecdc4', '#45b7d1'
    ];
    return $couleurs[$matiere_id % count($couleurs)];
}
?>