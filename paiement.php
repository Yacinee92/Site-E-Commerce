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
</head>
<body>

<?php include 'navbar.php'; ?>

<div style="display: flex; justify-content: center;">
    <form action="traitement_paiement.php" method="post" style="max-width: 550px; margin: 20px auto; padding: 20px;">
        <input type="hidden" name="total" value="<?php echo htmlspecialchars($total); ?>">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 1.5em;">Paiement</h1>
            <p style="margin: 0; margin-left: 20px; font-size: 1em;"><strong>Total à payer : €<?php echo htmlspecialchars($total); ?></strong></p>
        </div>

        <div class="form-group" style="flex: 1 1 45%; margin-right: 5%;">
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" required oninput="validateName(event)">
</div>

<script>
    function validateName(event) {
        // Remplacer tout caractère non alphabétique par une chaîne vide
        event.target.value = event.target.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
    }
</script>


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
            <input type="text" id="code_postal" name="code_postal" required  oninput="validateCardNumber(event)">
        </div>

        <div class="form-group" style="flex: 1 1 45%; margin-right: 5%;">
            <label for="departement">Département :</label>
            <input type="text" id="departement" name="departement" required>
        </div>

        <div class="form-group" style="flex: 1 1 45%;">
    <label for="numero_carte">Numéro de carte :</label>
    <input type="text" id="numero_carte" name="numero_carte" required placeholder="1234 5678 9012 3456" oninput="validateCardNumber(event)">
</div>

<script>
    function validateCardNumber(event) {
        // Supprimer tout caractère non numérique
        event.target.value = event.target.value.replace(/[^0-9]/g, '');
        // Limiter la longueur à 16 caractères
        if (event.target.value.length > 16) {
            event.target.value = event.target.value.slice(0, 16);
        }
    }

    function validateCVV(event) {
        // Supprimer tout caractère non numérique
        event.target.value = event.target.value.replace(/[^0-9]/g, '');
        // Limiter la longueur à 3 caractères
        if (event.target.value.length > 3) {
            event.target.value = event.target.value.slice(0, 3);
        }
    }
</script>


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
</div>

<?php include 'footer.php'; ?>

<style>
    body {
    margin-top: 60px; /* Ajustez cette valeur en fonction de la hauteur de votre navbar */
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
    z-index: 1000; /* Assurez-vous que la navbar est au-dessus des autres éléments */
    height: 60px; /* Ajustez cette valeur en fonction de la hauteur de votre navbar */
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
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    flex: 1;
    margin-right: 20px;
}

.summary-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    max-width: 300px;
    flex: 1;
}


    form {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgb(255, 179, 0);
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

    input {
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
    background: rgb(255, 199, 86); 
  }
  
  /* Handle on hover */
  ::-webkit-scrollbar-thumb:hover {
    background: rgb(192, 130, 50); 
  }
</style>

</body>
</html>
