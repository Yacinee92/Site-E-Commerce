<?php
session_start();
include 'bdd.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_GET['id'])) {
        header('Location: index.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur de connexion : " . htmlspecialchars($e->getMessage());
    exit;
}

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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="product-detail-container">
        <div class="product-detail">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                
                <?php if($product['video']): ?>
                <div class="product-video">
                    <video controls>
                        <source src="<?php echo htmlspecialchars($product['video']); ?>" type="video/mp4">
                        Votre navigateur ne supporte pas la vidéo.
                    </video>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="price">Prix: <?php echo htmlspecialchars($product['price']); ?> €</p>
                
                <?php if($product['description']): ?>
                <div class="description">
                    <h3>Description :</h3>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                </div>
                <?php endif; ?>

                <?php if($product['duration']): ?>
                <div class="duration">
                    <h3>Disponible :</h3>
                    <p><?php echo htmlspecialchars($product['duration']); ?></p>
                </div>
                <?php endif; ?>

                <?php if($product['upload_date'] && $product['upload_date'] != '0000-00-00'): ?>
                <div class="upload-date">
                    <h3>Date d'ajout :</h3>
                    <p><?php echo date('d/m/Y', strtotime($product['upload_date'])); ?></p>
                </div>
                <?php endif; ?>

                <!--  <form method="post" action="catalogue.php">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    <input type="hidden" name="name" value="<?= $product['name'] ?>">
    <input type="hidden" name="price" value="<?= $product['price'] ?>">
    <label for="quantity">Quantité :</label>
    <input type="number" name="quantity" value="1" min="1">
    <button type="submit">Ajouter au panier</button>
</form>-->


                <form method="post">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                    <button type="submit" name="add_to_cart" class="add-to-cart-btn">Ajouter au Panier</button>
                </form>

                    
                <a href="index.php" class="back-button">Retour aux produits</a>
            </div>
        </div>
    </div>


    
    <?php include 'footer.php'; ?>

    <style>
    .product-detail-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .product-detail {
        display: flex;
        gap: 40px;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .product-image {
        flex: 1;
        max-width: 500px;
    }

    .product-image img {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .product-info {
        flex: 1;
    }

    .product-info h1 {
        color: #1b4332;
        margin-bottom: 20px;
    }

    .product-info h3 {
        color: #145a32;
        margin-top: 20px;
        margin-bottom: 10px;
    }

    .description, .duration, .upload-date {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .price {
        font-size: 24px;
        color: #145a32;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .product-video {
        margin: 20px 0;
    }

    .product-video video {
        width: 100%;
        border-radius: 8px;
    }

    .add-to-cart-btn {
        background: #1b4332;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-size: 16px;
        margin: 20px 0;
        transition: background 0.3s ease;
        width: 100%;
    }

    .add-to-cart-btn:hover {
        background: #145a32;
    }

    .back-button {
        display: inline-block;
        color: #1b4332;
        text-decoration: none;
        font-weight: bold;
        background: #1b4332;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-size: 16px;
        margin: 20px 0;
        transition: background 0.3s ease;
        
    }

    .back-button:hover {
        background: #145a32;
    }

    @media (max-width: 768px) {
        .product-detail {
            flex-direction: column;
        }

        .product-image {
            max-width: 100%;
        }
    }
    </style>
</body>
</html>