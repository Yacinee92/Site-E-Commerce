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
        body {
            margin: 0;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            padding-top: 70px;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            background-color: white;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            z-index: 1000;
        }

        .navbar-left {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            padding-left: 30px;
        }

        .navbar-center {
            text-align: center;
        }

        .navbar-logo {
            height: 30px;
        }

        .brand-name {
            font-size: 24px;
            font-weight: 400;
            letter-spacing: 1px;
            text-decoration: none;
            color: #000;
            text-transform: uppercase;
        }

        .navbar-right {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-right: 30px;
        }

        .navbar-left a, .navbar-right a {
            color: #000;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 14px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: color 0.3s;
        }

        .navbar-right a, .search-container {
            display: flex;
            align-items: center;
            height: 100%;
        }

        .search-container form {
            height: 100%;
            display: flex;
            align-items: center;
        }

        .search-button {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 0 15px;
        }

        .search-button i {
            font-size: 16px;
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .navbar-left a:hover, .navbar-right a:hover {
            color: #888;
        }

        .item-count {
            display: inline-block;
            background-color: #000;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            font-size: 10px;
            text-align: center;
            line-height: 16px;
            margin-left: 4px;
        }

        .menu-button {
            background-color: white;
            color: black;
            border: 1px solid black;
            padding: 8px 15px;
            cursor: pointer;
            font-size: 14px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }

        .menu-button:hover {
            background-color:rgb(75, 230, 145);
        }

        .menu-button i {
            margin-left: 5px;
        }

        .search-icon, .user-icon, .cart-icon {
            font-size: 16px;
            padding: 0 15px;
            display: flex;
            align-items: center;
        }

        .main-menu {
            position: fixed;
            top: 70px;
            left: -250px;
            width: 250px;
            height: 100%;
            background-color: white;
            overflow: hidden;
            transition: left 0.5s ease-in-out, opacity 0.5s ease-in-out;
            z-index: 1001;
            box-shadow: 0 5px 5px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            opacity: 0;
        }

        .main-menu.active {
            left: 0;
            opacity: 1;
        }

        .main-menu a {
            display: block;
            color: #000;
            text-decoration: none;
            padding: 12px 20px;
            font-size: 16px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 5px 0;
            transition: color 0.3s;
            text-align: center;
            animation: slideIn 1.5s forwards;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .main-menu a:hover {
            color: #888;
        }

        .search-container {
            position: relative;
            display: inline-flex;
            align-items: center;
            height: 100%;
        }

        .search-container input {
            padding: 8px 30px 8px 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 14px;
            width: 0;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .search-container:hover input, .search-container input:focus {
            width: 180px;
            opacity: 1;
        }

        .search-container button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            position: absolute;
            right: 10px;
            display: flex;
            align-items: center;
        }

        .search-icon {
            font-size: 15px;
            color: #000;
        }

        @media screen and (max-width: 768px) {
            .navbar {
                grid-template-columns: 1fr 1fr;
                padding: 10px 0;
            }

            .navbar-left {
                padding-left: 10px;
            }

            .navbar-right {
                padding-right: 10px;
            }

            .navbar-center {
                display: none;
            }

            .search-container input {
                width: 120px;
                opacity: 1;
            }

            .main-menu {
                top: 50px;
            }
        }

        @media screen and (max-width: 1024px) {
            .navbar-right {
                padding-right: 20px;
            }

            .navbar-left {
                padding-left: 20px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="navbar">
        <div class="navbar-left">
            <button class="menu-button" onclick="toggleMenu()">
                Menu <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="navbar-center">
            <a href="acceuil.php" class="brand-name">iProtect</a>
        </div>

        <div class="navbar-right">
            <div class="search-container">
                <form action="search.php" method="GET">
                    <input type="text" name="query" id="search-input" placeholder="Rechercher..." required>
                    <button type="submit" class="search-icon"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <?php if (!isset($_SESSION['loggedin'])): ?>
                <a href="connexion.php" class="user-icon"><i class="fas fa-user"></i></a>
            <?php else: ?>
                <a href="compte.php" class="user-icon"><i class="fas fa-user"></i></a>
                <a href="favorites.php">
                    <i class="far fa-heart"></i>
                    <?php if (isset($_SESSION['favorites']) && count($_SESSION['favorites']) > 0): ?>
                        <span class="item-count"><?php echo count($_SESSION['favorites']); ?></span>
                    <?php endif; ?>
                </a>
                <a href="catalogue.php" class="cart-icon">
                    <i class="fas fa-shopping-bag"></i>
                    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="item-count"><?php echo count($_SESSION['cart']); ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="main-menu">
        <?php if (!isset($_SESSION['loggedin'])): ?>
            <a href="index.php">Produits du site</a>
            <a href="acceuil.php">Page d'accueil</a>
            <a href="connexion.php">Se connecter</a>
            <a href="inscription.php">S'inscrire</a>
        <?php else: ?>
            <a href="index.php">Produits</a>
            <a href="contact.php">Contact</a>
            <a href="favorites.php">Favoris</a>
            <a href="catalogue.php">Panier</a>
            <a href="compte.php">Mon compte</a>
            <a href="?logout=true">Déconnexion</a>
        <?php endif; ?>
    </div>

    <script>
        function toggleMenu() {
            const mainMenu = document.querySelector('.main-menu');
            mainMenu.classList.toggle('active');
        }
    </script>
</body>
</html>