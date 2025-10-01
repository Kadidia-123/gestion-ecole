<?php
require_once 'models/Inscription.php';
require_once 'models/Eleve.php';
require_once 'models/Classe.php';

class InscriptionsController {
    private $inscriptionModel;
    private $eleveModel;
    private $classeModel;

    public function __construct() {
        $this->inscriptionModel = new Inscription();
        $this->eleveModel = new Eleve();  /* Supposé existant */
        $this->classeModel = new Classe();  /* Supposé existant */
    }

    public function index() {
        $annee_scolaire = $_GET['annee'] ?? $this->inscriptionModel->getCurrentYear();
        $classes = $this->classeModel->getAll();
        
        $inscriptionsParClasse = [];
        foreach ($classes as $classe) {
            $inscriptionsParClasse[$classe['id']] = [
                'classe' => $classe,
                'eleves' => $this->inscriptionModel->getByClasse($classe['id'], $annee_scolaire)
            ];
        }
        
        require 'views/inscriptions/index.php';
    }

    public function create() {
        $eleve_id = $_GET['eleve_id'] ?? null;
        $eleve = $eleve_id ? $this->eleveModel->getById($eleve_id) : null;
        $classes = $this->classeModel->getAll();
        $annees = $this->inscriptionModel->getYearsList();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'eleve_id' => $_POST['eleve_id'],
                'classe_id' => $_POST['classe_id'],
                'annee_scolaire' => $_POST['annee_scolaire'],
                'frais_scolaires' => $_POST['frais_scolaires'],
                'remarques' => $_POST['remarques']
            ];
            
            if ($this->inscriptionModel->create($data['eleve_id'], $data['classe_id'], 
                $data['annee_scolaire'], $data['frais_scolaires'], $data['remarques'])) {
                $_SESSION['success'] = "Inscription créée avec succès";
                redirect('eleves/view.php?id=' . $data['eleve_id']);
            } else {
                $_SESSION['error'] = "Erreur lors de la création de l'inscription";
            }
        }
        
        require 'views/inscriptions/create.php';
    }

    public function edit($id) {
        $inscription = $this->inscriptionModel->getById($id);
        $classes = $this->classeModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'classe_id' => $_POST['classe_id'],
                'statut' => $_POST['statut'],
                'frais_scolaires' => $_POST['frais_scolaires'],
                'remarques' => $_POST['remarques'],
                'id' => $id
            ];
            
            if ($this->inscriptionModel->update($id, $data)) {
                $_SESSION['success'] = "Inscription mise à jour avec succès";
                redirect('eleves/view.php?id=' . $inscription['eleve_id']);
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour";
            }
        }
        
        require 'views/inscriptions/edit.php';
    }

    public function delete($id) {
        $inscription = $this->inscriptionModel->getById($id);
        
        if ($this->inscriptionModel->delete($id)) {
            $_SESSION['success'] = "Inscription supprimée avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression";
        }
        
        redirect('eleves/view.php?id=' . $inscription['eleve_id']);
    }
}
?>