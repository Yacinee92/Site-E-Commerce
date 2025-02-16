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

<style>
    /* Reset et styles de base */
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f5f5;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Container principal */
.profil-container {
    width: 90%;
    max-width: 600px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    box-sizing: border-box;
    position: relative;
    overflow: visible;
}

/* Titre */
h2 {
    text-align: center;
    color: #333;
    margin-bottom: 2rem;
    font-size: 1.8rem;
}

/* Formulaire */
form {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
    width: 100%;
}

label {
    color: #333;
    font-weight: 600;
    margin-bottom: 0.3rem;
    display: block;
}

input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-sizing: border-box;
    margin-bottom: 0.5rem;
}

input[type="submit"] {
    background-color: #1B4332;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    margin-top: 1rem;
    width: 100%;
}

input[type="submit"]:hover {
    background-color: #2d5a46;
}

/* Messages */
p[style*="color: green"] {
    background-color: #d4edda;
    color: #155724 !important;
    padding: 10px;
    border-radius: 5px;
    margin: 1rem 0;
    text-align: center;
}

p[style*="color: red"] {
    background-color: #f8d7da;
    color: #721c24 !important;
    padding: 10px;
    border-radius: 5px;
    margin: 1rem 0;
    text-align: center;
}

/* Media Queries */
@media screen and (max-width: 768px) {
    .profil-container {
        width: 95%;
        margin: 1rem auto;
        padding: 1.5rem;
    }
}

@media screen and (max-width: 480px) {
    .profil-container {
        width: 100%;
        margin: 0;
        border-radius: 0;
        padding: 1rem;
    }

    h2 {
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
    }

    input[type="submit"] {
        padding: 10px;
    }
}

/* Assure que le footer reste en bas */
footer {
    margin-top: auto;
}
</style>
</html>
