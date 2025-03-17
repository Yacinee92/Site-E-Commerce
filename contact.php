<?php
session_start();
?>
<html>
<body>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <script src="https://web3forms.com/client/script.js" async defer></script>
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* width */
::-webkit-scrollbar {
    width: 10px;
  }
  
  /* Track */
  ::-webkit-scrollbar-track {
    background: #121212; 
  }
   
  /* Handle */
  ::-webkit-scrollbar-thumb {
    background: rgb(38, 38, 38); 
  }
  
  /* Handle on hover */
  ::-webkit-scrollbar-thumb:hover {
    background: rgb(38, 38, 38); 
  }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            min-height: 100vh;
            position: relative;
            padding: 80px 0 60px 0; /* Espace pour navbar et footer */
        }

        /* Container principal */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            min-height: calc(100vh - 140px); /* Hauteur totale moins navbar et footer */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Style du formulaire */
        form {
            background-color: #fff;
            width: 100%;
            max-width: 500px;
            padding: 30px;
            border-radius: 30px;
            box-shadow: 0 4px 20px rgb(0, 116, 8);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
            font-size: 1.8rem;
        }

        /* Groupe de champs */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 0.95rem;
        }

        input, 
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus, 
        textarea:focus {
            outline: none;
            border-color:rgb(0, 111, 41);
            box-shadow: 0 0 0 2px rgba(0, 255, 76, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        /* Boutons */
        .button-group {
            margin-top: 25px;
        }

        .buttonn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            width: 100%;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 15px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .return-button {
            display: block;
            background-color: #155632;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 25px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .return-button:hover {
            background-color: #0a2b19;
        }

        /* Media Queries */
        @media screen and (max-width: 768px) {
            .container {
                padding: 15px;
            }

            form {
                padding: 20px;
            }

            h2 {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }

            input, 
            textarea {
                padding: 10px;
            }
        }

        @media screen and (max-width: 480px) {
            body {
                padding: 60px 0 40px 0;
            }

            .container {
                padding: 10px;
            }

            form {
                padding: 15px;
                border-radius: 10px;
            }

            h2 {
                font-size: 1.3rem;
                margin-bottom: 15px;
            }

            label {
                font-size: 0.9rem;
            }

            input, 
            textarea,
            button,
            .return-button {
                font-size: 0.95rem;
                padding: 10px 15px;
            }

            .button-group {
                margin-top: 20px;
            }
        }

        /* Styles pour les appareils très petits */
        @media screen and (max-width: 320px) {
            form {
                padding: 12px;
            }

            h2 {
                font-size: 1.2rem;
            }

            input, 
            textarea {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <form action="https://api.web3forms.com/submit" method="POST">
            <h2>Contactez-nous</h2>

            <input type="hidden" name="access_key" value="4fd4bb4c-a788-4913-b95a-daa6fcc645f3">
            <input type="hidden" name="from_name" value="Site Web">
            <input type="hidden" name="replyto" value="email">

            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" name="name" id="nom" required>
            </div>

            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="sujet">Sujet :</label>
                <input type="text" name="subject" id="sujet" required>
            </div>

            <div class="form-group">
                <label for="message">Message :</label>
                <textarea name="message" id="message" rows="5" required></textarea>
            </div>

            <div class="h-captcha" data-sitekey="10000000-ffff-ffff-ffff-000000000001"></div>

            <div class="button-group">
                <button type="submit" class="buttonn">Envoyer</button>
                <a href="acceuil.php" class="return-button">Retour à l'accueil</a>
            </div>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>