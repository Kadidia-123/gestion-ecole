<?php
require_once './config.php';
require_once './functions.php';

// Vérifier l'ID de la note
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: notes.php');
    exit();
}

$id = $_GET['id'];

// Vérifier si la note existe
$stmt = $pdo->prepare("SELECT id FROM notes WHERE id = ?");
$stmt->execute([$id]);
$note = $stmt->fetch();

if ($note) {
    // Supprimer la note
    $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
    $stmt->execute([$id]);
    
    // Message de succès
    $_SESSION['message'] = [
        'type' => 'success',
        'text' => 'La note a été supprimée avec succès.'
    ];
}

header('Location: notes.php');
exit();