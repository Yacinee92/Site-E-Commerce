<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header("Location: connexion.php");
    exit();
}

// Si l'utilisateur clique sur "Se déconnecter"
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: connexion.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'Accueil</title>
    <link rel="icon" type="image/x-icon" href="images/penguin.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <header>
        <div class="header-container">
            <h2>Bienvenue, 
                <?php 
                // Vérification de l'existence de `username`
                echo htmlspecialchars($_SESSION['username'] ?? 'Utilisateur inconnu'); 
                ?> !
            </h2>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" class="logout-btn">Se déconnecter</button>
            </form>
        </div>
    </header>
    <main>
        <p>Ceci est la page d'accueil de votre application</p>
    </main>
    <?php include 'footer.php'; ?>

    <style>
/* Style général */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: #f4f4f9;
    color: #333;
}

/* Header */
header {

    color: white;
    padding: 1rem 2rem;
    text-align: center;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
}

.header-container h2 {
    margin: 0;
}

.logout-btn {
    background:rgb(255, 34, 34);
    border: none;
    color: white;
    padding: 0.5rem 1rem;
    font-size: 1rem;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s;
}

.logout-btn:hover {
    background:rgb(197, 13, 13);
}

/* Main content */
main {
    text-align: center;
    padding: 2rem;
}

main p {
    font-size: 1.2rem;
}

/* Navbar */
nav {
    background: #333;
    color: white;
    padding: 1rem;
    display: flex;
    justify-content: center;
}

nav a {
    color: white;
    text-decoration: none;
    margin: 0 1rem;
    font-size: 1rem;
}

nav a:hover {
    text-decoration: underline;
}

/* Footer */
footer {
    background: #222;
    color: white;
    text-align: center;
    padding: 1rem;
    position: absolute;
    bottom: 0;
    width: 100%;
}

/* Responsiveness */
@media (max-width: 768px) {
    .header-container {
        flex-direction: column;
        text-align: center;
    }

    .logout-btn {
        margin-top: 1rem;
    }

    nav {
        flex-direction: column;
    }

    nav a {
        margin: 0.5rem 0;
    }
}

    </style>
</body>
</html>
