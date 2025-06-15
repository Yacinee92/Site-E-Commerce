<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous</title>
    <script src="https://web3forms.com/client/script.js" async defer></script>
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "San Francisco", "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }

        /* Conteneur principal */
        .contact-wrapper {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .contact-container {
            max-width: 700px;
            width: 100%;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            animation: slideUp 0.6s ease-out;
        }

        /* Animation d'entrée */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Section de gauche (formulaire) */
        .contact-form {
            padding: 2.5rem;
        }

        .contact-form h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
        }

        .form-field {
            margin-bottom: 1.2rem;
        }

        .form-field label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: #555;
            margin-bottom: 0.5rem;
        }

        .form-field input,
        .form-field textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            background: #fafafa;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-field input:focus,
        .form-field textarea:focus {
            border-color: #007aff;
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1);
            outline: none;
            background: #fff;
        }

        .form-field textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Section de droite (informations) */
        .contact-info {
            background: linear-gradient(135deg,#FAFEFD 0%, #C9E8CA 100%);
            color: black;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .contact-info h3 {
            font-size: 1.4rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .contact-info p {
            font-size: 0.9rem;
            line-height: 1.8;
            opacity: 0.9;
        }

        /* Boutons */
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary {
            background: #007aff;
            color: #fff;
            border: none;
        }

        .btn-primary:hover {
            background: #005bb5;
            transform: scale(1.02);
        }

        .btn-primary:disabled {
            background: #b0b0b0;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #fff;
            color: #007aff;
            border: 1px solid #007aff;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: #f5f7fa;
            transform: scale(1.02);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .contact-container {
                grid-template-columns: 1fr;
            }

            .contact-info {
                text-align: center;
            }

            .contact-form, .contact-info {
                padding: 1.5rem;
            }

            .contact-form h2 {
                font-size: 1.5rem;
            }

            .contact-info h3 {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .contact-wrapper {
                padding: 1rem;
            }

            .contact-container {
                border-radius: 0;
            }

            .contact-form, .contact-info {
                padding: 1rem;
            }

            .contact-form h2 {
                font-size: 1.3rem;
            }

            .contact-info h3 {
                font-size: 1.1rem;
            }

            .form-actions {
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn {
                padding: 10px;
            }
        }

        /* Scrollbar personnalisée */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f5f7fa;
        }

        ::-webkit-scrollbar-thumb {
            background: #b0b0b0;
            border-radius: 8px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #909090;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="contact-wrapper">
        <div class="contact-container">
            <!-- Section de gauche (formulaire) -->
            <div class="contact-form">
                <h2>Contactez-nous</h2>
                <form action="https://api.web3forms.com/submit" method="POST">
                    <input type="hidden" name="access_key" value="4fd4bb4c-a788-4913-b95a-daa6fcc645f3">
                    <input type="hidden" name="from_name" value="Site Web">
                    <input type="hidden" name="replyto" value="email">

                    <div class="form-field">
                        <label for="nom">Nom</label>
                        <input type="text" name="name" id="nom" required>
                    </div>

                    <div class="form-field">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>

                    <div class="form-field">
                        <label for="sujet">Sujet</label>
                        <input type="text" name="subject" id="sujet" required>
                    </div>

                    <div class="form-field">
                        <label for="message">Message</label>
                        <textarea name="message" id="message" required></textarea>
                    </div>

                    <div class="h-captcha" data-sitekey="10000000-ffff-ffff-ffff-000000000001"></div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                        <a href="acceuil.php" class="btn btn-secondary">Retour</a>
                    </div>
                </form>
            </div>

            <!-- Section de droite (informations) -->
            <div class="contact-info">
                <h3>Nous sommes là pour vous aider</h3>
                <p>Notre équipe est disponible pour répondre à toutes vos questions. Remplissez le formulaire et nous vous contacterons dans les plus brefs délais.</p>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>