<?php
function getPhotoPath($eleveId) {
    $photoPath = UPLOAD_DIR . $eleveId . '.jpg';
    return file_exists($photoPath) ? $photoPath : UPLOAD_DIR . DEFAULT_PHOTO;
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit();
    }
}

if (!function_exists('setFlash')) {
    function setFlash($type, $message) {
        $_SESSION['flash'] = compact('type', 'message');
    }
}

if (!function_exists('getFlash')) {
    function getFlash() {
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}



function getClasseName($classeId, $pdo) {
    if (!$classeId) return 'Non affecté';
    $stmt = $pdo->prepare("SELECT nom FROM classes WHERE id = ?");
    $stmt->execute([$classeId]);
    return $stmt->fetchColumn() ?: 'Non affecté';
}
function processUploadedPhoto($file, $eleveId) {
    $targetDir = UPLOAD_DIR;
    $targetFile = $targetDir . $eleveId . '.jpg';

    $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
        throw new Exception("Seuls les fichiers JPG, JPEG et PNG sont autorisés");
    }

    // Si GD n'est pas disponible, copie simplement le fichier
    if (!function_exists('imagecreatefromstring')) {
        move_uploaded_file($file['tmp_name'], $targetFile);
        return $targetFile;
    }

    // Redimensionnement et conversion en JPG
    list($width, $height) = getimagesize($file['tmp_name']);
    $image = imagecreatefromstring(file_get_contents($file['tmp_name']));
    $newImage = imagecreatetruecolor(PHOTO_WIDTH, PHOTO_HEIGHT);
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, PHOTO_WIDTH, PHOTO_HEIGHT, $width, $height);
    imagejpeg($newImage, $targetFile, 90);
    
    return $targetFile;
    
}

function exigerConnexion() {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }
}

function autoriser($rolesAutorises) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $rolesAutorises)) {
        die("Accès refusé. Vous n'avez pas les permissions nécessaires.");
    }
}

require_once 'config.php';

// Récupérer tous les enseignants
function getAllTeachers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM enseignants ORDER BY nom");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer toutes les matières
function getAllSubjects() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM matieres ORDER BY nom");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupérer un paiement pour l'édition (format JSON)
 */
function getPaiementJson($id) {
    global $pdo;
    
    $sql = "SELECT * FROM paiements WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $paiement = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($paiement) {
        header('Content-Type: application/json');
        echo json_encode($paiement);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Paiement non trouvé']);
    }
    exit;
}
function getElevesByClasseId($classe_id) {
    global $pdo;
    
    // D'abord, récupérer le nom de la classe à partir de l'ID
    $sql_classe = "SELECT nom FROM classes WHERE id = :id";
    $stmt_classe = $pdo->prepare($sql_classe);
    $stmt_classe->execute([':id' => $classe_id]);
    $classe = $stmt_classe->fetch(PDO::FETCH_ASSOC);
    
    if (!$classe) {
        return [];
    }
    
    // Ensuite, récupérer les élèves de cette classe
    $sql = "SELECT * FROM eleves WHERE classe = :classe ORDER BY nom, prenom";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':classe' => $classe['nom']]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
