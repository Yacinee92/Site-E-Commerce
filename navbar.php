<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['loggedin']);
    header("Location: connexion.php");
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
        /* Styles de base */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            margin-top: 60px; /* Adjusted to match the new navbar height */
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: black;
            padding: 5px 20px; /* Reduced padding to decrease height */
            z-index: 1000;
        }

        .navbar-logo {
            height: 50px; /* Adjusted to match the new navbar height */
        }

        .navbar-right {
            display: flex;
            align-items: center;
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

        /* Styles pour le menu hamburger */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 10px;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background-color: white;
            margin: 2px 0;
            transition: 0.4s;
        }

        /* Media queries pour la responsivité */
        @media screen and (max-width: 768px) {
            .hamburger {
            display: flex;
            }

            .navbar-right {
            display: none;
            position: absolute;
            top: 60px; /* Adjusted to match the new navbar height */
            left: 0;
            width: 100%;
            background-color: black;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
            }

            .navbar-right.active {
            display: flex;
            }

            .navbar-right a {
            width: 100%;
            text-align: center;
            padding: 15px 0;
            }

            .navbar-right a:hover {
            background-color: #555;
            border-radius: 0;
            }

            /* Animation du hamburger */
            .hamburger.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
            }

            .hamburger.active span:nth-child(2) {
            opacity: 0;
            }

            .hamburger.active span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-left">
            <img src="images/logosite.png" alt="Logo" class="navbar-logo">
        </div>

        <div class="hamburger" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="navbar-right">
            <?php 
            if (!isset($_SESSION['loggedin'])): ?>
                <a href="index.php">Produits du site</a>
                <a href="acceuil.php">Page d'accueil</a>
                <a href="connexion.php">Se connecter</a>
                <a href="inscription.php">S'inscrire</a>
            <?php else: ?>
                <a href="contact.php">Contact</a>
                <a href="index.php">Produits</a>
                <a href="compte.php">Mon compte</a>
                <a href="?logout=true">Déconnexion</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleMenu() {
            const hamburger = document.querySelector('.hamburger');
            const navbarRight = document.querySelector('.navbar-right');
            
            hamburger.classList.toggle('active');
            navbarRight.classList.toggle('active');
        }
    </script>
</body>
</html>