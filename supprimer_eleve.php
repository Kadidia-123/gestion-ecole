<?php
session_start();
require_once './config.php';
require_once './functions.php'; // Pour les fonctions utilitaires



// Vérification de l'ID
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    setFlash('danger', 'ID invalide');
    header('Location: eleves.php');
    exit();
}

$id = (int)$_GET['id'];

try {
    // Vérifier si l'élève existe avec des informations supplémentaires
    $sql = "SELECT id, photo FROM eleves WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $eleve = $stmt->fetch();

    if ($eleve) {
        // Supprimer la photo si elle existe
        if (!empty($eleve['photo']) && file_exists($eleve['photo'])) {
            unlink($eleve['photo']);
        }

        // Supprimer l'élève avec une transaction pour plus de sécurité
        $pdo->beginTransaction();
        
        // D'abord supprimer les dépendances (si existent)
        // Exemple si vous avez une table notes_eleves :
        // $pdo->prepare("DELETE FROM notes_eleves WHERE eleve_id = ?")->execute([$id]);
        
        // Puis supprimer l'élève
        $sql = "DELETE FROM eleves WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        $pdo->commit();
        
        setFlash('success', 'Élève supprimé avec succès');
    } else {
        setFlash('warning', 'Élève non trouvé');
    }
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    setFlash('danger', 'Erreur lors de la suppression : ' . $e->getMessage());
    error_log('Erreur suppression élève: ' . $e->getMessage());
}

header('Location: eleves.php');
exit();
?>