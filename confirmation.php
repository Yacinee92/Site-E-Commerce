<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Réussi</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div style="display: flex; flex-direction: column; align-items: center; gap: 20px;">
    <!-- SVG ici -->
    <svg viewBox="0 0 200 200" style="width: 100px; height: 100px;">
        <circle cx="100" cy="100" r="90" fill="#4CAF50" opacity="0.1"/>
        <circle cx="100" cy="100" r="70" fill="#4CAF50"/>
        <path d="M70 100 L90 120 L130 80" stroke="white" stroke-width="12" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
    </svg>
    <h1>Paiement Réussi</h1>
</div>
<p>Merci pour votre commande ! Vous recevrez bientôt un e-mail de confirmation. Vous pourrez suivre l’expédition de votre colis grâce au lien de suivi envoyé par e-mail. À bientôt ! 😄🎉</p>
<a href="index.php" class="bouton-retour">Retour à la boutique</a>  

<?php include 'footer.php'; ?>

<style>
     body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h1 {
            color: #28a745;
            margin-top: 20px;
        }

        p {
            font-size: 18px;
            margin: 10px 0;
        }

        .bouton-retour {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 20px;
        }

        .bouton-retour:hover {
            background-color: #0056b3;
        }

        .container {
            text-align: center;
        }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        confetti({
            particleCount: 500,  // Nombre de confettis
            spread: 200,          // Dispersion
            origin: { y: 0.6 }   // Position de départ (0 = haut, 1 = bas)
        });
    });
</script>

</body>
</html>
