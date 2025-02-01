<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    </head>
    <title>Navbar</title>
    <style>
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: black;
            padding: 10px 20px;
            z-index: 1000;
        }

            body {
                margin-top: 80px;
            }
            .navbar-logo {
                height: 60px;
            }
            .navbar-right a {
                color:white;
                text-decoration: none;
                padding: 10px 20px;
                font-family: Arial, sans-serif;
            }
            .navbar-right a:hover {
                background-color: #555;
                border-radius: 5px;
            }
    </style>
        
</head>
<body>
        <div class="navbar">
            <div class="navbar-left">
                <a href="acceuil.php">
                <img src="images/penguin1.png" alt="Logo" class="navbar-logo">
            </div>

            <div class="navbar-right">
                <?php if (!isset($_SESSION[`loggedin`])): ?>
                    <a href="index.php">Produit du site</a>
                    <a href="acceuil.php">Page de connexion</a>
                    <a href="compte.php">Profil</a>
                    <a href="connexion.php">Se connecter</a>
                    <a href="inscription.php">S'inscrire</a>
                    <?php else: ?>
                        <a href="logout.php">Déconnexion</a>
                    <?php endif; ?>
            </div>
    </div>
</body>
</html>             