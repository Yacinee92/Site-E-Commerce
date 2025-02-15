<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = "Visiteur"; // Valeur par défaut si non connecté
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
    <title>Coques iPhone 16 - Accueil</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <section class="hero-section">
    <div class="hero-content">
        <h1>Protégez votre iPhone 16 avec style</h1>
        <p>Découvrez nos coques élégantes et résistantes</p>
        <a href="index.php" class="btn">Voir la boutique</a>
    </div>
</section>

<main class="container">
    <section class="presentation">
        <h2>Pourquoi protéger votre téléphone ?</h2>
        <p>Votre iPhone 16 est un bijou de technologie, mais il est aussi fragile. Une simple chute peut l'endommager. Avec nos coques spécialement conçues, offrez-lui une protection optimale tout en conservant son élégance.</p>
        
        <h3>Les avantages de nos coques :</h3>
        <ul>
            <li>Protection contre les chocs et les rayures</li>
            <li>Design élégant et ergonomique</li>
            <li>Matériaux résistants et durables</li>
            <li>Compatibles avec la recharge sans fil</li>
        </ul>

        <a href="index.php" class="btn">Voir la boutique</a>
    </section>
</main>
    
    <?php include 'footer.php'; ?>


    <style>
     /* Réinitialisation des marges et styles de base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background: #f4f4f9;
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* SECTION HERO - Centrage vertical */
.hero-section {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 90vh; /* Ajustement pour ne pas trop dépasser */
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg,rgb(53, 180, 74),rgb(0, 219, 58)); /* Dégradé */
    width: 100%;
}

.hero-content {
    max-width: 600px;
    color: white;
}

.hero-content h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.hero-content p {
    font-size: 1.2rem;
    margin-bottom: 20px;
}

/* Boutons plus arrondis et modernes */
.btn {
    display: inline-block;
    background: white;
    color:rgb(4, 202, 40);
    padding: 12px 30px;
    text-decoration: none;
    font-size: 1.2rem;
    font-weight: bold;
    border-radius: 30px;
    transition: 0.3s;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

.btn:hover {
    background:rgb(0, 196, 10);
    color: white;
    transform: scale(1.05);
}

/* CONTAINER PRINCIPAL - DISPOSITION EN COLONNE */
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    padding: 20px;
}

.presentation {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 800px;
    text-align: center;
    margin-bottom: 40px;
}

.presentation h2 {
    color:rgb(9, 187, 0);
}

.presentation ul {
    list-style-type: none;
    padding: 0;
    text-align: center;
}

.presentation ul li {
    background:rgb(223, 255, 213);
    padding: 10px;
    margin: 10px 0;
    border-radius: 10px;
}

/* FOOTER */
footer {
    background: #222;
    color: white;
    text-align: center;
    padding: 1rem;
    width: 100%;
}

/* NAVBAR FIXE */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 90px; /* Ajuste la hauteur si besoin */
    background: black;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    z-index: 1000; /* Pour s'assurer que la navbar est bien au-dessus */
}

/* Compense la hauteur de la navbar */
body {
    padding-top: 60px; /* Doit être égal à la hauteur de la navbar */
}


    </style>
</body>
</html>
