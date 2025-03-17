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
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;

if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit;
}

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][] = $product_id;
    header("Location: cart.php");
    exit;
}

if (isset($_POST['remove_item'])) {
    $item_id = $_POST['item_id'];
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        $key = array_search($item_id, $_SESSION['cart']);

        if ($key !== false) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);     
        }
    }

    header("Location: cart.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier</title>

</head>
<body>
    <?php include 'navbar.php'; ?>


    <?php if (!empty($cart_items)): ?>
        <table>
            <thead>
                <tr>
                    <th>Images</th>
                    <th>Nom de Produits</th>
                    <th>Prix</th>
                    <th>Actions</th>

                </tr>
            </thead>
        </table>
        <tbody>
            <?php foreach ($cart_items as $item_id): ?>
                <?php $product = array_filter($products, function($prod) use ($item_id) {
                    return $prod['id'] == $item_id;
                });
                $product = array_values($product)[0]; // Récupère le premier élément
                $total += $product['price'];
                ?>
                <tr>
                    <td><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" width="100"></td>
                    <td><?php echo $product['name']; ?></td>
                    <td>€<?php echo $product['price']; ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" name="add_to_cart">Ajouter</button>
                        </form>
                            <form method="post" style="display:inline;">
                            <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                            <button type="submit" name="remove_item">Supprimer</button>
                        </form>  
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p>Total: €<?php echo $total; ?></p>
    <form method="post">
        <button type="submit" name="clear_cart">Vider le Panier</button>
    </form>
    <form method="get" action="checkout.php">
        <button type="submit">Finaliser le Panier</button>
    </form>

    <?php else: ?>
        <div class="empty-cart-container">
            <div class="empty-cart-content">
                <i class="fas fa-shopping-cart empty-cart-icon"></i>
                <h2>VOTRE PANIER EST VIDE</h2>
                <p>Vous n'avez pas encore ajouté d'articles à votre panier.</p>
                <a href="index.php" class="continue-shopping-btn">Retourner au catalogue</a>
            </div>
        </div>
    <?php endif; ?>
</div>

    <?php include 'footer.php'; ?>

    <style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.panier {
    text-align: center;
    font-size: 32px;
    font-weight: bold;
    color: #fff;
    background-color: #1B4332;
    padding: 15px 30px;
    border-radius: 40px;
    width: fit-content;
    margin: 20px auto;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Panier vide */
.empty-cart-container {
        background-color: #fff;
        border-radius: 5px;
        padding: 150px 100px;
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
        border-radius: 25px;
        transition: background-color 0.3s;
    }
    
    .continue-shopping-btn:hover {
        background-color: #333;
    }
    
h1 {
    text-align: center;
    color: #333;
}

ul {
    list-style-type: none;
    padding: 0;
}

li {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    background-color: #fff;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

li img {
    margin-right: 10px;
}

button {
    background-color: #ff0000;
    color: #fff;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 5px;
}

button:hover {
    background-color: #cc0000;
}

</style>

</body>
</html>