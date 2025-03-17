<?php
session_start();

include 'bdd.php';

// Établir la connexion PDO (ajoutez ce code après l'include)
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . htmlspecialchars($e->getMessage());
    exit;
}

// Initialiser le tableau de favoris s'il n'existe pas
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

// Reste du code...

// Initialiser le tableau de favoris s'il n'existe pas
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

// Traitement de la suppression d'un favori
if (isset($_POST['remove_favorite'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($product_id !== false) {
        // Trouver l'index du produit dans le tableau des favoris
        $key = array_search($product_id, $_SESSION['favorites']);
        if ($key !== false) {
            // Supprimer le produit des favoris
            unset($_SESSION['favorites'][$key]);
            // Réindexer le tableau
            $_SESSION['favorites'] = array_values($_SESSION['favorites']);
        }
        header("Location: favorites.php");
        exit;
    }
}

// Traitement de l'ajout d'un favori au panier
if (isset($_POST['add_favorite_to_cart'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($product_id !== false) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][] = $product_id;
        header("Location: favorites.php");
        exit;
    }
}

// Récupérer les informations des produits favoris
$favorites = [];
if (!empty($_SESSION['favorites'])) {
    try {
        // Préparer les placeholders pour la requête SQL
        $placeholders = implode(',', array_fill(0, count($_SESSION['favorites']), '?'));
        
        // Préparer et exécuter la requête
        $stmt = $pdo->prepare("SELECT id, name, image, price FROM products WHERE id IN ($placeholders)");
        $stmt->execute($_SESSION['favorites']);
        $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . htmlspecialchars($e->getMessage());
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="favorites-container">
        <h1 class="headerr">Mes Produits Favoris</h1>
        
        <?php if (empty($favorites)): ?>
            <div class="empty-favorites">
                <i class="far fa-heart empty-icon"></i>
                <p>Vous n'avez pas encore de produits favoris.</p>
                <a href="index.php" class="return-button">Retour au catalogue</a>
            </div>
        <?php else: ?>
            <div class="favorites-list">
                <?php foreach ($favorites as $product): ?>
                <div class="favorite-item">
                    <a href="product-detail.php?id=<?php echo htmlspecialchars($product['id']); ?>">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </a>
                    <div class="favorite-info">
                        <a href="product-detail.php?id=<?php echo htmlspecialchars($product['id']); ?>">
                            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                        </a>
                        <p>Prix: €<?php echo htmlspecialchars($product['price']); ?></p>
                        <div class="favorite-actions">
                            <form method="post" class="cart-form">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                                <button type="submit" name="add_favorite_to_cart">Ajouter au Panier</button>
                            </form>
                            <form method="post" class="remove-form">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                                <button type="submit" name="remove_favorite" class="remove-button">
                                    <i class="fas fa-trash-alt"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="action-buttons">
                <a href="index.php" class="return-button">Retour au catalogue</a>
                <a href="catalogue.php" class="checkout-button">Voir le Panier</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

<style>
    /* Styles généraux */
    body {
        font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        background-color: #f8f9fa;
        color: #333;
        margin: 0;
        padding-top: 60px;
    }

    /* Container principal */
    .favorites-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    /* En-tête */
    .headerr {
        background: linear-gradient(to right, #1b4332, #145a32);
        color: white;
        text-align: center;
        padding: 18px 15px;
        font-size: 28px;
        font-weight: 600;
        text-transform: uppercase;
        border-radius: 16px;
        margin: 15px auto 25px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        width: 90%;
        max-width: 800px;
        display: block;
        margin-top: 20px;
    }

    /* Message pour favoris vides */
    .empty-favorites {
        text-align: center;
        padding: 50px 20px;
        background-color: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin: 30px auto;
        max-width: 600px;
    }

    .empty-icon {
        font-size: 50px;
        color: #ccc;
        margin-bottom: 20px;
    }

    .empty-favorites p {
        font-size: 18px;
        margin-bottom: 30px;
        color: #666;
    }

    /* Liste des favoris */
    .favorites-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    /* Élément favori */
    .favorite-item {
        background-color: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .favorite-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 12px rgba(0, 0, 0, 0.1);
    }

    .favorite-item img {
        width: 100%;
        height: 200px;
        object-fit: contain; /* Change object-fit to contain */
        object-position: center;
        border-bottom: 1px solid #f0f0f0;
    }

    .favorite-info {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .favorite-item h2 {
        font-size: 18px;
        margin: 5px 0;
        color: #1b4332;
        font-weight: 500;
    }

    .favorite-item a {
        text-decoration: none;
        color: inherit;
    }

    .favorite-item p {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin: 8px 0;
    }

    /* Actions sur les favoris */
    .favorite-actions {
        display: flex;
        gap: 10px;
        margin-top: auto;
    }

    .cart-form, .remove-form {
        flex: 1;
    }

    .favorite-actions button {
        border: none;
        outline: 0;
        padding: 10px;
        color: white;
        text-align: center;
        cursor: pointer;
        width: 100%;
        font-size: 14px;
        border-radius: 20px;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .favorite-actions button[name="add_favorite_to_cart"] {
        background-color: #1b4332;
    }

    .favorite-actions button[name="add_favorite_to_cart"]:hover {
        background-color: #0f3e26;
    }

    .remove-button {
        background-color: #e74c3c;
    }

    .remove-button:hover {
        background-color: #c0392b;
    }

    /* Boutons d'action */
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin: 20px 0 40px;
    }

    .return-button, .checkout-button {
        display: block;
        width: max-content;
        padding: 12px 24px;
        text-decoration: none;
        font-size: 16px;
        border-radius: 30px;
        transition: all 0.2s ease;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        font-weight: 500;
    }

    .return-button {
        background-color: #6c757d;
        color: white;
    }

    .return-button:hover {
        background-color: #5a6268;
        transform: scale(1.03);
    }

    .checkout-button {
        background-color: #145a32;
        color: white;
    }

    .checkout-button:hover {
        background-color: #0f3e26;
        transform: scale(1.03);
    }

    /* Styles responsifs */
    @media (max-width: 768px) {
        .favorites-list {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .headerr {
            font-size: 24px;
            padding: 15px;
            width: 95%;
        }
    }

    @media (max-width: 576px) {
        .favorites-list {
            grid-template-columns: 1fr;
        }
        
        .favorite-actions {
            flex-direction: column;
            gap: 5px;
        }
        
        .action-buttons {
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
    }
</style>

</body>
</html>