<?php
// Vérifier si la session n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Démarrer la session si ce n'est pas déjà fait
}

// Si l'utilisateur clique sur "Déconnexion"
if (isset($_GET['logout'])) {
    session_destroy(); // Détruire la session
    header("Location: connexion.php"); // Rediriger vers la page de connexion
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <style>
        /* Styles pour la barre de navigation */
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
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* Ajout d'un espace pour éviter que le contenu ne soit caché par la navbar */
        body {
            margin-top: 80px;
        }

        .navbar-logo {
            height: 60px;
        }

        .search-bar {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .search-bar input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 300px;
        }

        .search-bar button {
            background: none;
            border: none;
            padding: 0;
            margin-left: 5px;
            cursor: pointer;
        }

        .search-icon {
            width: 30px;
        }

        .navbar-right a {
            color: white;
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
    <!-- Barre de navigation -->
    <div class="navbar">
        <!-- Logo à gauche -->
        <div class="navbar-left">
            <img src="images/logosite.png" alt="Logo" class="navbar-logo">
        </div>

        <!-- Barre de recherche
        <div class="search-bar">
            <form action="search.php" method="get" style="display: flex; align-items: center;">
                <input type="text" name="query" placeholder="Rechercher une vidéo..." required>
                <button type="submit">
                    <img src="images/loupe.png" alt="Rechercher" class="search-icon">
                </button>
            </form>
        </div>-->

        <!-- Liens à droite -->
        <div class="navbar-right">
            <?php 
            // Vérification de connexion
            if (!isset($_SESSION['loggedin'])): ?>
                <a href="index.php">Produits du site</a>
                <a href="acceuil.php">Page d'accueil</a>
                <a href="connexion.php">Se connecter</a>
                <a href="inscription.php">S'inscrire</a>
            <?php else: ?>
                <a href="index.php">Produits</a>
                <a href="compte.php">Mon compte</a>
                <a href="?logout=true">Déconnexion</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
