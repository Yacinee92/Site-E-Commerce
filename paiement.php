<?php
session_start();

if (!isset($_POST['total'])) {
    header("Location: catalogue.php");
    exit;
}

$total = $_POST['total'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body>

<?php include 'navbar.php'; ?>

<div style="display: flex; justify-content: center;">
    <form action="traitement_paiement.php" method="post" style="max-width: 550px; margin: 20px auto; padding: 20px;">
        <input type="hidden" name="total" value="<?php echo htmlspecialchars($total); ?>">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div style="display: flex; align-items: center;">
                <h1 style="margin: 0; font-size: 1.5em;">Paiement</h1>
                <div style="margin-left: 15px; display: flex; gap: 10px;">
                    <i class="ri-visa-line" style="font-size: 2em; color: #1A1F71;"></i>
                    <i class="ri-mastercard-line" style="font-size: 2em; color:rgb(235, 118, 0);"></i>
                    <i class="ri-bank-card-fill" style="font-size: 2em; color:rgb(0, 108, 23);"></i>
                </div>
            </div>
            <p style="margin: 0; margin-left: 20px; font-size: 1em;"><strong>Total à payer : €<span id="totalPayer"><?php echo htmlspecialchars($total); ?></span></strong></p>
        </div>

        <!-- Ajout de l'option de livraison -->
        <div class="form-group" style="flex: 1 1 45%; margin-right: 5%;">
            <label for="livraison">Type de livraison :</label>
            <select id="livraison" name="livraison" required onchange="updateLivraisonDetails()">
                <option value="standard">Livraison Standard (3.99€)</option>
                <option value="acceleree">Livraison Accélérée (7€)</option>
                <option value="retrait">Point de retrait (Gratuit)</option>
            </select>
        </div>

        <!-- Détails dynamiques sur la livraison -->
        <div id="livraisonDetails" style="margin-top: 20px;">
            <p><strong>Prix de la livraison : <span id="prixLivraison">3.99€</span></strong></p>
            <p><strong>Estimation de la livraison : <span id="delaiLivraison">6 à 7 jours</span></strong></p>
        </div>

        <div class="form-group" style="flex: 1 1 45%; margin-right: 5%;">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required oninput="validateName(event)">
        </div>

        <div class="form-group" style="flex: 1 1 45%;">
            <label for="adresse">Adresse :</label>
            <input type="text" id="adresse" name="adresse" required>
        </div>

        <div class="form-group" style="flex: 1 1 45%; margin-right: 5%;">
            <label for="ville">Ville :</label>
            <input type="text" id="ville" name="ville" required oninput="validateName(event)">
        </div>

        <div class="form-group" style="flex: 1 1 45%;">
            <label for="code_postal">Code Postal :</label>
            <input type="text" id="code_postal" name="code_postal" required maxlength="5"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5);" pattern="\d{5}" title="Veuillez entrer 5 chiffres">
        </div>

        <div class="form-group" style="flex: 1 1 45%; margin-right: 5%;">
            <label for="departement">Département :</label>
            <input type="text" id="departement" name="departement" required>
        </div>

        <div class="form-group" style="flex: 1 1 45%;">
            <label for="numero_carte">Numéro de carte :</label>
            <input type="text" id="numero_carte" name="numero_carte" required placeholder="1234-5678-9012-3456" oninput="formatCardNumber(event)">
        </div>

        <div class="form-group" style="flex: 1 1 45%; margin-right: 5%;">
            <label for="expiration">Date d'expiration :</label>
            <input type="month" id="expiration" name="expiration" required>
        </div>

        <div class="form-group" style="flex: 1 1 45%;">
            <label for="cvv">CVV :</label>
            <input type="text" id="cvv" name="cvv" required placeholder="123" maxlength="3" oninput="validateCVV(event)">
        </div>

        <button type="submit" style="width: 100%; margin-top: 20px; padding: 12px; font-size: 1em;">Valider le paiement</button>
    </form>
</div>

<?php include 'footer.php'; ?>

<style>
    body {
        margin-top: 60px;
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        padding: 0;
    }

    #navbar {
        position: fixed;
        top: 0;
        width: 100%;
        background-color: #333;
        color: white;
        z-index: 1000;
        height: 60px;
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 20px;
    }

    form {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgb(255, 187, 0);
        display: flex;
        flex-wrap: wrap;
    }

    .form-group {
        text-align: left;
        margin-top: 15px;
        flex: 1 1 45%;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-size: 0.9em;
    }

    input, select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 0.9em;
    }

    button {
        background-color: #007bff;
        color: #fff;
        border: none;
        cursor: pointer;
        border-radius: 20px;
        font-size: 1em;
    }

    button:hover {
        background-color: #0056b3;
    }
</style>

<script>
    function validateName(event) {
        event.target.value = event.target.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
    }

    function formatCardNumber(event) {
        let value = event.target.value.replace(/\D/g, '');
        
        // Limiter à 16 chiffres
        if (value.length > 16) {
            value = value.slice(0, 16);
        }
        
        // Ajouter des tirets tous les 4 chiffres
        const groups = value.match(/.{1,4}/g);
        if (groups) {
            event.target.value = groups.join('-');
        } else {
            event.target.value = value;
        }
    }

    function validateCVV(event) {
        event.target.value = event.target.value.replace(/[^0-9]/g, '');
        if (event.target.value.length > 3) {
            event.target.value = event.target.value.slice(0, 3);
        }
    }

    function updateLivraisonDetails() {
        const livraison = document.getElementById("livraison").value;
        const prixLivraison = document.getElementById("prixLivraison");
        const delaiLivraison = document.getElementById("delaiLivraison");
        const totalPayer = document.getElementById("totalPayer");

        let livraisonPrix = 0;
        if (livraison === "standard") {
            livraisonPrix = 3.99;
            delaiLivraison.textContent = "6 à 7 jours";
        } else if (livraison === "acceleree") {
            livraisonPrix = 7;
            delaiLivraison.textContent = "1 à 2 jours";
        } else if (livraison === "retrait") {
            livraisonPrix = 0;
            delaiLivraison.textContent = "3 à 4 jours";
        }

        prixLivraison.textContent = `${livraisonPrix}€`;

        const totalAvecLivraison = parseFloat("<?php echo $total; ?>") + livraisonPrix;
        totalPayer.textContent = totalAvecLivraison.toFixed(2);
    }
</script>

</body>
</html>
