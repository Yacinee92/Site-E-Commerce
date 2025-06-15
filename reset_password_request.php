<?php
include 'bdd.php';

// Définir le fuseau horaire sur 'Europe/Paris'
date_default_timezone_set('Europe/Paris');

// Vérifier si la réquête est une requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer l'email du formulaire
    $email = htmlspecialchars(trim($_POST['email']));

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Suppression des tokens expirés
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE expires_at < NOW()");
        $stmt->execute();

        // Vérifier si l'email existe dans la table users
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Génerer un token unique pour la réinitialisation du mot de passe
            $token = bin2hex(random_bytes(32)); // Générer un token aléatoire de 32 caractères

            // Définir une expiration de 15m pour ce token
            $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes')); // Date d'expiration du token

            // Enregistrer le token dans la table password_resets avec l'email, l'expiration et la date de création
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at, created_at) VALUES (:email, :token, :expires_at, NOW())");
            $stmt->execute([
                ':email' => $email,
                ':token' => $token,
                ':expires_at' => $expiry
            ]);

            // Créer le lien de réinitialisation basé sur le domaine actuel
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/reset_password.php?token=$token";

            // Prépare le sujet de l'email avec un encodage UTF-8 pour caracteres spéciaux
            $subject = "=?UTF-8?B?" . base64_encode("Réinitialisation de votre mot de passe") . "?="; // Encodage du sujet de l'email

            // Prépare le message de l'email en HTML
            $message = "
            <html>
            <head>
                <title>Réinitialisation de votre mot de passe</title>
            </head>
            <body>
                <p>Bonjour,</p>
                <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
                <p><a href='$resetLink' style='color: blue; text-decoration: underline;'>Réinitialiser le mot de passe</a></p>
                <p>Ce lien expirera dans 15 minutes.</p>
                <p>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
            </body>
            </html>
            ";

            // Configurer les en-têtes de l'email pour supporter le HTML et l'encodage UTF-8
            $headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";

            // Envoyer l'email et afficher un message approprié
            if (mail($email, $subject, $message, $headers)) {
                echo "<p style='color: green;'>Un lien de réinitialisation a été envoyé à votre adresse e-mail.</p>";
            } else {
                echo "<p style='color: red;'>Erreur lors de l'envoi de l'email. Veuillez réessayer plus tard.</p>";
            }
        } else {
            // Si aucun compte associé à cet email n'est trouvé
            echo "<p style='color: red;'>Aucun compte associé à cet email n'a été trouvé.</p>";
        }
    } catch (PDOException $e) {
        // Gérer les erreurs de connexion ou d'exéution SQL
        echo "Erreur : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
</head>
<style>
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f4f4f4;
    }
    .form-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-color: #fff;
        padding: 40px;
        box-shadow: 0 0 0 rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .form-container h2 {
        margin-bottom: 20px;
    }

    .form-container form {
        width: 100%;
        max-width: 400px;
    }

    .form-container form label,
    .form-container form input {
        display: block;
        width: 100%;
        margin-bottom: 10px;
    }
    .form-container form input[type="submit"]:hover,
    .form-container form button:hover {
        background-color: #555;
    }

    .register-link {
        color: #333;
    }

    .register-link:hover {
        text-decoration: underline;
    }
</style>
<body>
    <?php include 'navbar.php'; ?>
    <div class="login-container">
        <div class="form-container">
            <h2>Réinitialiser le mot de passe</h2>
            <form action="reset_password_request.php" method="POST">
                <label for="email">Entrez votre adresse e-mail :</label>
                <input type="email" id="email" name="email" required>
                <button type="submit">Envoyer le lien de réinitialisation</button>
            </form>
        </div>   
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>