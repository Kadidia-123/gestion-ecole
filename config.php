<?php
// Configuration de la base de données
define('DB_HOST', 'localhost:3306');
define('DB_NAME', 'ecole');
define('DB_USER', 'root');
define('DB_PASS', '');

// Chemins d'accès
define('UPLOAD_DIR', __DIR__ . '/uploads/eleves/');
define('DEFAULT_PHOTO', 'default.jpg');

// Paramètres système
define('ITEMS_PER_PAGE', 15);
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('PHOTO_WIDTH', 200);
define('PHOTO_HEIGHT', 200);

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    // Définir l'utilisateur courant pour les triggers
    if (isset($_SESSION['user_id'])) {
        $pdo->exec("SET @user_id = " . $_SESSION['user_id']);
    }
}   catch (PDOException $e) {
    die("Erreur DB: " . $e->getMessage()); // Affiche le vrai message d'erreur
}

// Créer le répertoire uploads si inexistant
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
    file_put_contents(UPLOAD_DIR . 'default.jpg', file_get_contents('https://via.placeholder.com/200'));
}

// Fonctions utilitaires

function redirect($url) {
    header("Location: $url");
    exit();
}

function setFlash($type, $message) {
    $_SESSION['flash'] = compact('type', 'message');
}

function getFlash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>