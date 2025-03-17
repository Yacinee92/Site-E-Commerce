<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'AdihausDIN', Arial, sans-serif;
        }
        
        .footer {
            background-color: #000000;
            color: #ffffff;
            padding: 30px 0;
            width: 100%;
            font-family: 'AdihausDIN', Arial, sans-serif;
        }
        
        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
            text-align: center;
        }
        
        .logo-adidas {
            margin: 0 auto 20px;
            width: 50px;
        }
        
        .footer-nav {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }
        
        .footer-nav a {
            color: #ffffff;
            text-decoration: none;
            margin: 0 20px;
            font-size: 14px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
            padding: 5px 0;
            position: relative;
            transition: all 0.2s ease;
        }
        
        .footer-nav a:hover {
            color: #999999;
        }
        
        .footer-nav a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: #fff;
            bottom: 0;
            left: 0;
            transition: width 0.3s ease;
        }
        
        .footer-nav a:hover:after {
            width: 100%;
        }
        
        .footer-bottom {
            width: 100%;
            border-top: 1px solid #333333;
            padding-top: 20px;
            margin-top: 10px;
            font-size: 12px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .footer-nav {
                flex-direction: column;
            }
            
            .footer-nav a {
                margin: 10px 0;
                font-size: 13px;
            }
            
            .footer-bottom p {
                font-size: 11px;
            }
        }
        
        @media (max-width: 480px) {
            .footer-nav {
                flex-direction: column;
            }
            
            .footer-nav a {
                margin: 5px 0;
                font-size: 12px;
            }
            
            .footer-bottom p {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <footer class="footer">
        <div class="footer-container">
            <nav class="footer-nav">
                <a href="index.php">Boutique</a>
                <a href="index.php">Collections</a>
                <a href="contact.php">Nous contacter</a>
                <a href="acceuil.php">À propos</a>
                <a href="contact.php">Aide</a>
            </nav>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Yacine Boulares. Tous droits réservés.</p>
                <p>
                    <a href="privacy.php" style="color: #999; text-decoration: none; margin: 0 10px;">Politique de confidentialité</a> | 
                    <a href="terms.php" style="color: #999; text-decoration: none; margin: 0 10px;">Conditions d'utilisation</a>
                </p>
            </div>
        </div>
    </footer>
</body>
</html>