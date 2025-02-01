<?php
session_start();

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalisation de l'Achat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Finalisation de l'Achat</h1>

<?php if (!empty($cart_items)): ?>
<p>Vous avez <?php echo count($cart_items); ?> article dans votre panier </p>

    <form method="post" action="process_order.php">
        <label for="adresse">Adresse de livraison:</label>
        <input type="text" id="adresse" name="adresse required">

        <label for="payment">Méthode de paiement:</label><br>
        <select id="payment" name="payment_method">
            <option value="credit_card">Carte de Crédit</option>
            <option value="paypal">Paypal</option>
        </select><br>

        <button type="submit" >Finaliser l'Achat</button>
    </form>
<?php else: ?>
    <p>Votre panier est vide</p>
<?php endif; ?>
</body>
</html>
<style>
body {
    font-family: Arial, sans-serif;
    background-color:rgb(35, 35, 35);
    margin: 0;
    padding: 0;
}

h1 {
    text-align: center;
    color: #333;
}

form {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

input[type="text"],
select {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    width: 100%;
    padding: 10px;
    background-color: #28a745;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background-color: #218838;
}

p {
    text-align: center;
    font-size: 18px;
}

h1, p {
    color: #fff;
}

label {
    color: #333;
}

input[type="text"],
select {
    color: #333;
}

button {
    color: #fff;
}
</style>
