<?php
session_start();

include 'bdd.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les produits depuis la base de données
    $stmt = $pdo->query("SELECT id, name, image, video, price FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . htmlspecialchars($e->getMessage());
    exit;
}

// Ajouter un produit au panier
if (isset($_POST['add_to_cart'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($product_id !== false) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][] = $product_id;
        header("Location: catalogue.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue de Produits</title>
    <link rel="stylesheet" href="style.css">


<?php include 'navbar.php'; ?>

<div class="catalogue-container">
    <h1>Catalogue de Produits</h1>
    <div class="product-list">
    <?php foreach ($products as $product): ?>
        <div class="product-item">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2> <!-- Ajout du nom du produit ici -->
            <p>Prix: €<?php echo htmlspecialchars($product['price']); ?></p>
            <form method="post">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                <button type="submit" name="add_to_cart">Ajouter au Panier</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

</div>

<a href="catalogue.php" class="checkout-button">Voir le Panier</a>

<?php include 'footer.php'; ?>



<style>
    
    h1 {
        text-align: center;
        color: black;
        font-size: 24px;
        margin-top: 20px;
        
    }

    h2 {
        font-size: 20px;
        color: black;
        text-align: center;
    }

    .product-list {
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-around;
        overflow-x: auto;
        
    }

    .product-item {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        min-width: 200px;
        margin: 10px;
        text-align: center;
        background-color: white;
        font-family: arial;
        transition: transform 0.3s ease;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .product-item:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
    }

    .product-item img, .product-item video {
    width: 100%;
    height: auto;
    /* Supprimer ou commenter cette ligne */
    /* border-radius: 5px; */
    margin-bottom: 15px;
}


    .price {
        color: grey;
        font-size: 18px;
    }

    .product-item button {
        border: none;
        outline: 0;
        padding: 10px;
        color: white;
        background-color: #000;
        text-align: center;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
        border-radius: 20px;
    }

    .product-item button:hover {
        opacity: 0.7;
    }

    .checkout-button {
        display: inline-block;
        padding: 10px 20px;
        margin-top: 20px;
        background-color: #28a745;
        color: white;
        text-align: center;
        text-decoration: none;
        font-size: 16px;
        border-radius: 20px;
    }

    .checkout-button:hover {
        background-color: #218838;
    }

    img {
        max-width: 150px;
        max-height: 150px;
    }

/* .product-item h2 {
    color: inherit;
    text-decoration: none;
}

.product-item h2 a:hover {
    color: red;
} */
    


</style>


</body>
</html>
