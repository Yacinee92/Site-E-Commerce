<?php
session_start();
include 'bdd.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: connexion.php");
    exit();
}

$username = $_SESSION['username'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$dbusername,$dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Utilisateur introuvable.";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $newUsername = htmlspecialchars(trim($_POST['username']));
        $newEmail = htmlspecialchars(trim($_POST['email']));
        $newPassword = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

        $sql = "UPDATE users SET username = :username, email = :email" . ($newPassword ? ", password = :password" : "") . " WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $newUsername);
        $stmt->bindParam(':email', $newEmail);
        if ($newPassword) {
            $stmt->bindParam(':password', $newPassword);
        }
        $stmt->bindParam(':id', $user['id']);
        $stmt->execute();

        $_SESSION['username'] = $newUsername;
        echo "<p style='color: green;'>Informations mises à jour avec succès.</p>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
$conn = null;
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Mon Profil</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <?php include 'navbar.php'; ?>
        <?php 
        if (file_exists('navbar.php')) {
            include 'navbar.php'; 
        } else {
            echo "<p style='color: red;'>Le fichier navbar.php est introuvable.</p>";
        }
        ?>
        <div class="profil-container">
            <h2>Mon Profil</h2>
            <form action="compte.php" method="POST">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username'] ?? ''); ?>" required>
        
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" required>

                <label for="password">Nouveau mot de passe :</label>
                <input type="password" id="password" name="password">

                <input type="submit" value="Mettre à jour">
            </form>
        </div>
        <?php 
        if (file_exists('footer.php')) {
            include 'footer.php'; 
        } else {
            echo "<p style='color: red;'>Le fichier footer.php est introuvable.</p>";
        }
        ?>
    <?php include 'footer.php'; ?>
</body>
</html>
