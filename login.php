<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['rôle'];
            $_SESSION['nom'] = $user['nom'];
            
            // NE RIEN AFFICHER AVANT LA REDIRECTION
            header('Location: dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = "Email ou mot de passe incorrect";
            header('Location: index.php');
            exit();
        }
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
} else {
    // NE PAS AFFICHER DE TEXTE AVANT REDIRECTION
    header('Location: index.php');
    exit();
}

