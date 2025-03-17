<?php

session_start();

$error_message = ''; // Variable pour stocker les messages d'erreur

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    include 'bdd.php';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['loggedin'] = true;
                header("Location: acceuil.php");
                exit();
            } else {
                $error_message = 'Mot de passe incorrect.'; // Message d'erreur pour mot de passe incorrect
            }
        } else {
            $error_message = 'Email ou mot de passe incorrect. 😥'; // Message d'erreur pour email non trouvé
        }
    } catch (PDOException $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
    $conn = null;
}
?>

<!DOCTYPE HTML>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="signup-container">
        <h2>Connexion</h2>

        <?php if ($error_message): ?>
            <div class="error-card">
                <p style="color: red;"><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>

        <form action="connexion.php" method="POST">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Se connecter">
        </form>

        <p>Pas encore inscrit ? <a href="inscription.php" class="register-link">Inscrivez-vous ici</a></p>
    </div>
    <?php include 'footer.php'; ?>
</body>
<style>
    /* Style pour la card d'erreur */
    .error-card {
        color: #721c24;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
        text-align: center;
    }

    /* Assurez-vous que le body prend toute la hauteur de la page */
    html, body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    /* Le conteneur principal doit prendre tout l'espace disponible */
    .signup-container {
    }

    /* Le footer doit rester en bas */
    footer {
        background-color: #f1f1f1;
        text-align: center;
        padding: 10px;
        position: relative;
        bottom: 0;
        width: 100%;
    }
</style>
</html>