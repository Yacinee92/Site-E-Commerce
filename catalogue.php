<?php
session_start();

$products = [
    ["id" => 1, "name" => "Produit 1", "image" => "images/coque1.jpg", "price" => 20],
    ["id" => 2, "name" => "Produit 2", "image" => "images/coque2.jpg", "price" => 50],
    ["id" => 3, "name" => "Produit 3", "image" => "images/coque3.jpg", "price" => 50],
    ["id" => 4, "name" => "Produit 4", "image" => "images/coque4.jpg", "price" => 25],
    ["id" => 5, "name" => "Produit 5", "image" => "images/coque5.jpg", "price" => 35],
];

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;

if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<h1>Votre Panier</h1>

<?php if (!empty($cart_items)): ?>
    <div class="cart-container">
        <ul class="cart-list">
            <?php foreach ($cart_items as $item_id): ?>
                <?php
                $product = array_filter($products, function($prod) use ($item_id) {
                    return $prod['id'] == $item_id;
                });
                $product = array_values($product)[0];
                $total += $product['price'];
                ?>
                <li class="cart-item">
                    <img class="cart-image" src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <div class="cart-details">
                        <p class="cart-name"><?php echo $product['name']; ?></p>
                        <p class="cart-price">Prix : €<?php echo $product['price']; ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="cart-total">Total : €<?php echo $total; ?></p>
        <form method="post" class="cart-actions">
            <button type="submit" name="clear_cart" class="clear-cart-btn">Vider le Panier</button>
        </form>
    </div>
<?php else: ?>
    <p class="empty-cart">Votre panier est vide.</p>
<?php endif; ?>

<a href="index.php" class="rac">Retourner au catalogue</a>

<?php include 'footer.php'; ?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    h1 {
        text-align: center;
        color: #333;
        margin: 20px 0;
    }

    .cart-container {
        max-width: 800px;
        margin: 0 auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .cart-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .cart-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
    }

    .cart-image {
        width: 120px;
        height: auto;
        margin-right: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .cart-details {
        flex-grow: 1;
    }

    .cart-name {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin: 0;
    }

    .cart-price {
        font-size: 16px;
        color: #666;
        margin: 5px 0 0;
    }

    .cart-total {
        text-align: right;
        font-size: 20px;
        font-weight: bold;
        margin-top: 20px;
        color: #333;
    }

    .cart-actions {
        text-align: right;
        margin-top: 20px;
    }

    .clear-cart-btn {
        background-color: #ff0000;
        color: #fff;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .clear-cart-btn:hover {
        background-color: #cc0000;
    }

    .rac {
        display: block;
        width: 200px;
        margin: 30px auto;
        text-align: center;
        background-color: #007bff;
        color: #fff;
        padding: 10px;
        text-decoration: none;
        border-radius: 5px;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    .rac:hover {
        background-color: #0056b3;
    }

    .empty-cart {
        text-align: center;
        color: #333;
        font-size: 18px;
        margin-top: 30px;
    }

    ul {
    display: flex; /* Met les éléments sur une ligne */
    gap: 15px; /* Espace entre les produits */
    padding: 0;
    list-style-type: none;
    justify-content: center; /* Centre les produits horizontalement */
}

li {
    display: flex;
    flex-direction: column; /* Empile les éléments à l'intérieur du produit verticalement */
    align-items: center; /* Centre le contenu à l'intérieur */
    background-color: #fff;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 10px;
    width: 200px; /* Largeur de chaque produit */
    text-align: center; /* Centre le texte */
}

li img {
    width: 150px; /* Taille des images */
    height: auto; /* Maintient les proportions */
    border-radius: 5px;
    margin-bottom: 10px; /* Espace entre l'image et le texte */
}

p.total {
    text-align: center;
    font-size: 1.2em;
    color: #333;
    margin-top: 20px;
}

button {
    margin-top: 15px;
}

</style>

</body>
</html>
