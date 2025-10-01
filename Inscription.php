<?php
require_once 'config.php';

class Inscription {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function create($eleve_id, $classe_id, $annee_scolaire, $frais_scolaires = 0, $remarques = null) {
        $sql = "INSERT INTO inscriptions (eleve_id, classe_id, annee_scolaire, frais_scolaires, remarques) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$eleve_id, $classe_id, $annee_scolaire, $frais_scolaires, $remarques]);
    }

    public function getById($id) {
        $sql = "SELECT i.*, e.nom as eleve_nom, e.prenom as eleve_prenom, c.nom as classe_nom
                FROM inscriptions i
                JOIN eleves e ON i.eleve_id = e.id
                JOIN classes c ON i.classe_id = c.id
                WHERE i.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByEleve($eleve_id) {
        $sql = "SELECT i.*, c.nom as classe_nom 
                FROM inscriptions i
                JOIN classes c ON i.classe_id = c.id
                WHERE i.eleve_id = ?
                ORDER BY i.annee_scolaire DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$eleve_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByClasse($classe_id, $annee_scolaire = null) {
        $sql = "SELECT i.*, e.nom as eleve_nom, e.prenom as eleve_prenom, e.matricule
                FROM inscriptions i
                JOIN eleves e ON i.eleve_id = e.id
                WHERE i.classe_id = ?" . 
                ($annee_scolaire ? " AND i.annee_scolaire = ?" : "") . "
                ORDER BY e.nom, e.prenom";
        $stmt = $this->pdo->prepare($sql);
        $params = [$classe_id];
        if ($annee_scolaire) $params[] = $annee_scolaire;
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $sql = "UPDATE inscriptions SET 
                classe_id = ?, statut = ?, frais_scolaires = ?, remarques = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['classe_id'],
            $data['statut'],
            $data['frais_scolaires'],
            $data['remarques'],
            $id
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM inscriptions WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getCurrentYear() {
        $current_year = date('Y');
        return "$current_year-" . ($current_year + 1);
    }

    public function getYearsList() {
        $start_year = 2020; /* Année de début de votre système */
        $current_year = date('Y');
        $years = [];
        
        for ($year = $start_year; $year <= $current_year; $year++) {
            $years[] = "$year-" . ($year + 1);
        }
        
        return array_reverse($years);
    }
}
?>