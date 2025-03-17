<?php
session_start();
include 'bdd.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: connexion.php");
    exit();
}

$username = $_SESSION['username'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
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
        $successMessage = "Informations mises à jour avec succès.";
    }
} catch (PDOException $e) {
    $errorMessage = "Erreur : " . $e->getMessage();
}
$conn = null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "San Francisco", "Helvetica Neue", Arial, sans-serif;
            background-color: #fafafa;
            color: #1d1d1f;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }

        /* Conteneur principal */
        .profil-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Animation d'entrée */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Titre */
        h2 {
            font-size: 2rem;
            font-weight: 600;
            color: #1d1d1f;
            text-align: center;
            margin-bottom: 2rem;
        }

        /* Formulaire */
        form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        label {
            font-size: 1rem;
            font-weight: 500;
            color: #1d1d1f;
            text-align: left;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #d2d2d7;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #0071e3;
            outline: none;
        }

        /* Bouton de soumission */
        input[type="submit"] {
            background-color: #0071e3;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        input[type="submit"]:hover {
            background-color: #005bb5;
            transform: scale(1.02);
        }

        input[type="submit"]:disabled {
            background-color: #d2d2d7;
            cursor: not-allowed;
        }

        /* Messages de succès ou d'erreur */
        .message {
            padding: 12px;
            border-radius: 10px;
            font-size: 1rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Footer */
        footer {
            margin-top: auto;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .profil-container {
                margin: 1rem;
                padding: 1.5rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            input[type="submit"] {
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .profil-container {
                margin: 0;
                border-radius: 0;
                padding: 1rem;
            }

            h2 {
                font-size: 1.3rem;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"] {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="profil-container">
        <h2>Mon Profil</h2>

        <!-- Affichage des messages -->
        <?php if (isset($successMessage)): ?>
            <div class="message success"><?= htmlspecialchars($successMessage); ?></div>
        <?php elseif (isset($errorMessage)): ?>
            <div class="message error"><?= htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <form action="compte.php" method="POST">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username'] ?? ''); ?>" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" required>

            <label for="password">Nouveau mot de passe (laisser vide pour ne pas modifier) :</label>
            <input type="password" id="password" name="password" placeholder="Entrez un nouveau mot de passe">

            <input type="submit" value="Mettre à jour">
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>