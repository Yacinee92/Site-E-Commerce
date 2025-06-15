<?php
session_start();

$products = [
    ["id" => 1, "name" => "Produit 1", "image" => "images/coque1.jpg", "price" => 20, "description" => "Coque téléphone"],
    ["id" => 2, "name" => "Produit 2", "image" => "images/coque2.jpg", "price" => 50, "description" => "Coque téléphone"],
    ["id" => 3, "name" => "Produit 3", "image" => "images/coque3.jpg", "price" => 50, "description" => "Coque téléphone"],
    ["id" => 4, "name" => "Produit 4", "image" => "images/coque4.jpg", "price" => 25, "description" => "Coque téléphone"],
    ["id" => 5, "name" => "Produit 5", "image" => "images/coque5.jpg", "price" => 35, "description" => "Coque téléphone"],
    ["id" => 6, "name" => "Produit 6", "image" => "images/coque6.jpg", "price" => 59, "description" => "Coque téléphone"],
    ["id" => 7, "name" => "Produit 7", "image" => "images/coque7.jpg", "price" => 59, "description" => "Coque téléphone"],
    ["id" => 8, "name" => "Produit 8", "image" => "images/coqueb.jpg", "price" => 59, "description" => "Coque téléphone"],
    ["id" => 9, "name" => "Produit 9", "image" => "images/coques.jpg", "price" => 30, "description" => "Coque téléphone"],
    ["id" => 10, "name" => "Produit 10", "image" => "images/coques1.jpg", "price" => 45, "description" => "Coque téléphone"],
    ["id" => 11, "name" => "Produit 11", "image" => "images/coqueperso.jpg", "price" => 50, "description" => "Coque téléphone"],
    ["id" => 12, "name" => "Produit 11", "image" => "images/coques2.jpg", "price" => 35, "description" => "Coque téléphone"],
    ["id" => 13, "name" => "Produit 11", "image" => "images/coques3.jpg", "price" => 40, "description" => "Coque téléphone"],
];

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cart_quantities = isset($_SESSION['cart_quantities']) ? $_SESSION['cart_quantities'] : [];
$total = 0;
$item_count = 0;

// Gestion des quantités
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = max(1, intval($_POST['quantity']));
    
    $cart_quantities[$product_id] = $new_quantity;
    $_SESSION['cart_quantities'] = $cart_quantities;
    
    header("Location: cart.php");
    exit;
}

// Supprimer un produit
if (isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];
    
    // Trouver et supprimer l'article du panier
    $key = array_search($product_id, $cart_items);
    if ($key !== false) {
        unset($cart_items[$key]);
        $cart_items = array_values($cart_items); // Réindexer le tableau
    }
    
    // Supprimer sa quantité
    if (isset($cart_quantities[$product_id])) {
        unset($cart_quantities[$product_id]);
    }
    
    $_SESSION['cart'] = $cart_items;
    $_SESSION['cart_quantities'] = $cart_quantities;
    
    header("Location: catalogue.php");
    exit;
}

// Vider le panier
if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    unset($_SESSION['cart_quantities']);
    header("Location: catalogue.php");
    exit;
}

// Calculer le total et le nombre d'articles
foreach ($cart_items as $item_id) {
    $product = array_filter($products, function($prod) use ($item_id) {
        return $prod['id'] == $item_id;
    });
    
    if (!empty($product)) {
        $product = array_values($product)[0];
        $quantity = isset($cart_quantities[$item_id]) ? $cart_quantities[$item_id] : 1;
        $total += $product['price'] * $quantity;
        $item_count += $quantity;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Panier</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="cart-container">
    <div class="cart-header">
        <h1>VOTRE PANIER</h1>
        <p class="cart-summary"><?php echo $item_count; ?> ARTICLES</p>
    </div>

    <?php if (!empty($cart_items)): ?>
        <div class="cart-content">
            <div class="cart-items-container">
                <?php foreach ($cart_items as $item_id): ?>
                    <?php
                    $product = array_filter($products, function($prod) use ($item_id) {
                        return $prod['id'] == $item_id;
                    });
                    
                    if (!empty($product)) {
                        $product = array_values($product)[0];
                        $quantity = isset($cart_quantities[$item_id]) ? $cart_quantities[$item_id] : 1;
                    } else {
                        continue;
                    }
                    ?>
                    <div class="cart-item">
                        <div class="item-image">
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                        <div class="item-details">
                            <h3><?php echo $product['name']; ?></h3>
                            <p class="item-description"><?php echo $product['description']; ?></p>
                            
                            <div class="item-actions">
                                <form method="post" class="quantity-form">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <div class="quantity-selector">
                                        <label for="quantity-<?php echo $product['id']; ?>">Quantité:</label>
                                        <select name="quantity" id="quantity-<?php echo $product['id']; ?>" onchange="this.form.submit()">
                                            <?php for($i = 1; $i <= 10; $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo ($quantity == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="update_quantity" class="hidden-btn">Mettre à jour</button>
                                </form>
                                
                                <form method="post" class="remove-form">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="remove_item" class="remove-btn">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="item-price">
                            <p>€<?php echo number_format($product['price'] * $quantity, 2, ',', ' '); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary-container">
                <div class="summary-box">
                    <h2>RÉCAPITULATIF</h2>
                    
                    <div class="summary-row">
                        <span><?php echo $item_count; ?> produits</span>
                        <span>€<?php echo number_format($total, 2, ',', ' '); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Livraison</span>
                        <span>GRATUITE</span>
                    </div>
                    
                    <div class="summary-row total-row">
                        <span>TOTAL</span>
                        <span>€<?php echo number_format($total, 2, ',', ' '); ?></span>
                    </div>
                    
                    <form action="paiement.php" method="post" class="checkout-form">
                        <input type="hidden" name="total" value="<?php echo $total; ?>">
                        <button type="submit" class="checkout-btn">COMMANDER</button>
                    </form>
                    
                    <div class="payment-methods">
                        <p>MOYENS DE PAIEMENT ACCEPTÉS</p>
                        <div class="payment-icons">
                            <i class="fab fa-cc-visa"></i>
                            <i class="fab fa-cc-mastercard"></i>
                            <i class="fab fa-cc-paypal"></i>
                            <i class="fab fa-cc-amex"></i>
                        </div>
                    </div>
                </div>
                
                <div class="promo-box">
                    <h3>CODE PROMO</h3>
                    <form class="promo-form">
                        <input type="text" placeholder="Entrez votre code promo">
                        <button type="submit">APPLIQUER</button>
                    </form>
                </div>
                
                <a href="index.php" class="continue-shopping">
                    <i class="fas fa-arrow-left"></i> CONTINUER MES ACHATS
                </a>
            </div>
        </div>
        
        <form method="post" class="clear-cart-form">
            <button type="submit" name="clear_cart" class="clear-cart-btn">
                <i class="fas fa-trash-alt"></i> VIDER LE PANIER
            </button>
        </form>
        
    <?php else: ?>
        <div class="empty-cart-container">
            <div class="empty-cart-content">
                <i class="fas fa-shopping-cart empty-cart-icon"></i>
                <h2>VOTRE PANIER EST VIDE</h2>
                <p>Vous n'avez pas encore ajouté d'articles à votre panier.</p>
                <a href="index.php" class="continue-shopping-btn">DÉCOUVRIR NOS PRODUITS</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<style>
    /* Style global */
    body {
        font-family: 'Helvetica Neue', Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f9f9f9;
        color: #000;
    }
    
    * {
        box-sizing: border-box;
    }
    
    /* Container principal */
    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 50px;
    }
    
    /* En-tête du panier */
    .cart-header {
        padding: 20px 0;
        border-bottom: 1px solid #e5e5e5;
        margin-bottom: 30px;
    }
    
    .cart-header h1 {
        font-size: 32px;
        margin: 0;
        font-weight: 700;
        letter-spacing: 1px;
    }
    
    .cart-summary {
        font-size: 16px;
        color: #666;
        margin-top: 10px;
    }
    
    /* Disposition du contenu du panier */
    .cart-content {
        display: flex;
        flex-direction: row;
        gap: 30px;
    }
    
    .cart-items-container {
        flex: 2;
    }
    
    .cart-summary-container {
        flex: 1;
    }
    
    /* Style des articles dans le panier */
    .cart-item {
        display: flex;
        background-color: #fff;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .item-image {
        width: 120px;
        margin-right: 20px;
    }
    
    .item-image img {
        width: 100%;
        height: auto;
        border-radius: 3px;
    }
    
    .item-details {
        flex: 1;
    }
    
    .item-details h3 {
        margin-top: 0;
        margin-bottom: 8px;
        font-size: 18px;
        font-weight: 600;
    }
    
    .item-description {
        color: #666;
        margin-bottom: 15px;
        font-size: 14px;
    }
    
    .item-actions {
        display: flex;
        align-items: center;
        margin-top: 15px;
    }
    
    .quantity-form {
        margin-right: 20px;
    }
    
    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .quantity-selector label {
        font-size: 14px;
        color: #555;
    }
    
    .quantity-selector select {
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        background-color: #fff;
    }
    
    .hidden-btn {
        display: none;
    }
    
    .remove-btn {
        background: none;
        border: none;
        color: #666;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 0;
    }
    
    .remove-btn:hover {
        color: #000;
    }
    
    .item-price {
        font-weight: 700;
        font-size: 18px;
        width: 100px;
        text-align: right;
    }
    
    /* Récapitulatif du panier */
    .summary-box {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    
    .summary-box h2 {
        margin-top: 0;
        font-size: 22px;
        font-weight: 700;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-size: 16px;
    }
    
    .total-row {
        font-weight: 700;
        font-size: 18px;
        padding-top: 20px;
        margin-top: 10px;
        border-top: 1px solid #eee;
    }
    
    .checkout-form {
        margin-top: 20px;
    }
    
    .checkout-btn {
        background-color: #000;
        color: #fff;
        border: none;
        width: 100%;
        padding: 15px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 1px;
        cursor: pointer;
        border-radius: 3px;
        transition: background-color 0.3s;
    }
    
    .checkout-btn:hover {
        background-color: #333;
    }
    
    .payment-methods {
        margin-top: 20px;
        text-align: center;
    }
    
    .payment-methods p {
        font-size: 12px;
        color: #777;
        margin-bottom: 10px;
    }
    
    .payment-icons {
        display: flex;
        justify-content: center;
        gap: 15px;
        font-size: 24px;
        color: #555;
    }
    
    /* Code promo */
    .promo-box {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    
    .promo-box h3 {
        margin-top: 0;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .promo-form {
        display: flex;
        gap: 10px;
    }
    
    .promo-form input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
        font-size: 14px;
    }
    
    .promo-form button {
        background-color: #000;
        color: #fff;
        border: none;
        padding: 10px 15px;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.5px;
        cursor: pointer;
        border-radius: 3px;
    }
    
    /* Continuer les achats */
    .continue-shopping {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #000;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        margin-top: 10px;
    }
    
    .continue-shopping:hover {
        text-decoration: underline;
    }
    
    /* Vider le panier */
    .clear-cart-form {
        margin-top: 30px;
        text-align: center;
    }
    
    .clear-cart-btn {
        background: none;
        border: none;
        color: #666;
        font-size: 16px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        transition: color 0.3s;
    }
    
    .clear-cart-btn:hover {
        color: #000;
    }
    
    /* Panier vide */
    .empty-cart-container {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 50px 20px;
        text-align: center;
    }
    
    .empty-cart-icon {
        font-size: 60px;
        color: #ccc;
        margin-bottom: 20px;
    }
    
    .empty-cart-content h2 {
        font-size: 24px;
        margin-bottom: 10px;
    }
    
    .empty-cart-content p {
        color: #666;
        margin-bottom: 30px;
    }
    
    .continue-shopping-btn {
        display: inline-block;
        background-color: #000;
        color: #fff;
        padding: 15px 30px;
        text-decoration: none;
        font-weight: 600;
        border-radius: 3px;
        transition: background-color 0.3s;
    }
    
    .continue-shopping-btn:hover {
        background-color: #333;
    }
    
    /* Responsive */
    @media (max-width: 900px) {
        .cart-content {
            flex-direction: column;
        }
        
        .item-price {
            width: 80px;
        }
    }
    
    @media (max-width: 600px) {
        .cart-item {
            flex-direction: column;
        }
        
        .item-image {
            width: 100%;
            margin-right: 0;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .item-image img {
            max-width: 150px;
        }
        
        .item-price {
            width: 100%;
            text-align: left;
            margin-top: 15px;
        }
        
        .item-actions {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .quantity-form {
            margin-right: 0;
        }
    }
</style>

<script>
    // Script pour le chargement automatique de la quantité
    document.addEventListener('DOMContentLoaded', function() {
        const quantitySelects = document.querySelectorAll('.quantity-selector select');
        quantitySelects.forEach(select => {
            select.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });
    });
</script>

</body>
</html>