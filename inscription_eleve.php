<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    $genre = $_POST['genre'];
    $classe_id = $_POST['classe_id'];
    $matricule = $_POST['matricule'];

    $sql = "INSERT INTO eleves (nom, prenom, date_naissance, genre, classe_id, matricule)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom, $prenom, $date_naissance, $genre, $classe_id, $matricule]);
    echo "Élève inscrit avec succès.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription Élève</title>
</head>
<body>
    <h2>Inscrire un Élève</h2>
    <form method="POST">
        <input name="nom" placeholder="Nom" required><br>
        <input name="prenom" placeholder="Prénom" required><br>
        <input type="date" name="date_naissance" required><br>
        <select name="genre">
            <option value="M">Masculin</option>
            <option value="F">Féminin</option>
        </select><br>
        <input name="matricule" placeholder="Matricule" required><br>
        <input name="classe_id" placeholder="ID Classe" required><br>
        <button>Inscrire</button>
    </form>
</body>
</html>
