<?php
// Démarre la session
session_start();

// Initialisation des variables pour gérer les erreurs
$errorMessage = '';
$inscription_reussie = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération et nettoyage des données du formulaire
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirmPassword = htmlspecialchars(trim($_POST['confirm-password']));

    // Vérification de la correspondance des mots de passe
    if ($password !== $confirmPassword) {
        $errorMessage = "Les mots de passe ne correspondent pas.";
    }
    // Vérification de la validité de l'email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Adresse email invalide.";
    }
    // Vérification de la longueur du mot de passe
    elseif (strlen($password) < 6) {
        $errorMessage = "Le mot de passe doit contenir au moins 6 caractères.";
    }
    else {
        include 'bdd.php'; // Inclure la connexion à la base de données

        try {
            // Connexion à la base de données avec PDO
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Vérification si l'email existe déjà dans la base
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Si l'email existe déjà, afficher un message d'erreur
            if ($stmt->rowCount() > 0) {
                $errorMessage = "Cet email est déjà utilisé.";
            } else {
                // Hash du mot de passe pour plus de sécurité
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insertion des données dans la base de données
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);

                if ($stmt->execute()) {
                    // Si l'insertion est réussie, redirection vers la page de connexion
                    $inscription_reussie = true;
                } else {
                    $errorMessage = "Une erreur est survenue. Veuillez réessayer.";
                }
            }
        } catch (PDOException $e) {
            // Gestion des erreurs de connexion à la base de données
            $errorMessage = "Erreur : " . $e->getMessage();
        }

        // Fermeture de la connexion à la base de données
        $conn = null;
    }
}

// Si l'inscription est réussie, redirection
if ($inscription_reussie) {
    header("Location: connexion.php");
    exit(); // Arrêter le script après la redirection
}
?>

<!DOCTYPE HTML>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="signup-container">
        <h2>Inscription</h2>

        <!-- Affichage de l'erreur si une erreur est présente -->
        <?php if ($errorMessage): ?>
            <p style="color: red;"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form action="" method="POST" onsubmit="return validateForm()">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm-password">Confirmer le mot de passe :</label>
            <input type="password" id="confirm-password" name="confirm-password" required>

            <input type="submit" value="S'inscrire">
        </form>
        
        <p id="error-msg" style="color: red;"></p>
    </div>

    <script>
        // Validation du formulaire côté client
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm-password").value;
            var errorMsg = document.getElementById("error-msg");

            if (password !== confirmPassword) {
                errorMsg.textContent = "Les mots de passe ne correspondent pas.";
                return false;
            }
            return true;
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
