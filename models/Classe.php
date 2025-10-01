<?php
class Classe {
    public function getAll() {
        // Exemple de récupération des classes depuis la base de données
        // À adapter selon ton système
        // return $pdo->query("SELECT * FROM classes")->fetchAll(PDO::FETCH_ASSOC);
        return []; // Retourne un tableau vide si pas de base de données
    }
}
?>