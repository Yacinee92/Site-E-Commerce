<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'bdd.php';

    // Récupérer et nettoyer les données du formulaire
    $token = htmlspecialchars(trim($_POST['token'])); // Le token unique pour réinitialiser le mot de passe
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validation des mots de passe
    if ($new_password !== $confirm_password) {
        echo "<p style='color: red;'>Les mots de passe ne correspondent pas.</p>";
        exit;
    }

    if (strlen($new_password) < 8) {
        echo "<p style='color: red;'>Le mot de passe doit contenir au moins 8 caractères.</p>";
        exit;
    }

    try {
        // Connexion à la base de données avec PDO
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifier si le token est valide
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW()");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $email = $stmt->fetchColumn();

            // Hacher le nouveau mot de passe
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            // Mettre à jour le mot de passe de l'utilisateur
            $update = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
            $update->execute([
                ':password' => $hashed_password,
                ':email' => $email
            ]);

            // Supprimer le token utilisé
            $delete = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
            $delete->execute([':token' => $token]);

            // Redirection vers la page de connexion
            header('Location: connexion.php');
            exit;
        } else {
            echo "<p style='color: red;'>Le lien de réinitialisation est invalide ou a expiré.</p>";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else if (isset($_GET['token'])) {
    $tokenFromUrl = htmlspecialchars(trim($_GET['token']));
} else {
    $tokenFromUrl = '';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe</title>
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
    .form-container form input[type="submit"],
    .form-container form button {
        background-color: green;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
    }

    .form-container form input[type="submit"]:hover
    .form-container form button {
        background-color: #555;
    }

    .register-link {
        color: #333;
    }

    .register-link:hover {
        text-decoration: underline;
    }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="login-container">
        <div class="form-container">
            <h2>Réinitialisation du mot de passe</h2>
            <?php if (!empty($tokenFromUrl)) : ?>
                <form action="reset_password.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo $tokenFromUrl; ?>">
                    <label for="new_password">Nouveau mot de passe :</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <label for="confirm_password">Confirmer le mot de passe :</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <button type="submit">Réinitialiser</button>
                </form>
            <?php else : ?>
                <p style="color: red;">Aucun lien de réinitialisation fourni.</p>
            <?php endif; ?>
        </div>   
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
