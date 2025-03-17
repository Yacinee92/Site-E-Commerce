<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Commande</title>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1"></script>
    <style>
        /* Style global inspiré d'Apple */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "San Francisco", "Helvetica Neue", Arial, sans-serif;
            background-color: #fafafa;
            color: #1d1d1f;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            line-height: 1.5;
        }

        /* Conteneur principal */
        .container {
            max-width: 600px;
            padding: 20px;
            animation: fadeIn 0.6s ease-in-out;
        }

        /* Animation d'entrée */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Cercle de validation */
        .success-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto;
            position: relative;
            animation: scaleIn 0.5s ease-in-out;
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        /* Style du texte */
        h1 {
            font-size: 2.5rem;
            font-weight: 600;
            color: #1d1d1f;
            margin: 20px 0;
        }

        p {
            font-size: 1.2rem;
            color: #6e6e73;
            margin: 10px 0;
        }

        /* Bouton de retour */
        .bouton-retour {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 24px;
            background-color: #0071e3;
            color: #fff;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            border-radius: 25px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .bouton-retour:hover {
            background-color: #005bb5;
            transform: scale(1.05);
        }

        /* Responsive design */
        @media (max-width: 600px) {
            h1 {
                font-size: 1.8rem;
            }

            p {
                font-size: 1rem;
            }

            .success-icon {
                width: 80px;
                height: 80px;
            }

            .bouton-retour {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container">
        <!-- Icône de succès (SVG) -->
        <div class="success-icon">
            <svg viewBox="0 0 200 200" style="width: 100%; height: 100%;">
                <circle cx="100" cy="100" r="90" fill="#34c759" opacity="0.1"/>
                <circle cx="100" cy="100" r="70" fill="#34c759"/>
                <path d="M70 100 L90 120 L130 80" stroke="white" stroke-width="12" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            </svg>
        </div>

        <!-- Titre -->
        <h1>Commande Confirmée</h1>

        <!-- Message de confirmation -->
        <p>Merci pour votre commande ! Un e-mail de confirmation vous a été envoyé. Vous pourrez suivre l’expédition de votre colis grâce au lien de suivi fourni dans l’e-mail.</p>
        <p>Nous vous remercions de votre confiance et avons hâte de vous revoir !</p>

        <!-- Bouton de retour -->
        <a href="index.php" class="bouton-retour">Retour à la boutique</a>
    </div>


    <!-- Script pour les confettis -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            confetti({
                particleCount: 200,  // Nombre de confettis
                spread: 100,         // Dispersion
                origin: { y: 0.6 },  // Position de départ (0 = haut, 1 = bas)
                colors: ['#0071e3', '#34c759', '#ff9500'] // Couleurs festives
            });
        });
    </script>

</body>
</html>