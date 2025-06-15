<?php
session_start();

include 'bdd.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $priceQuery = $pdo->query("SELECT MIN(price) AS min_price, MAX(price) AS max_price FROM products");
    $priceResult = $priceQuery->fetch(PDO::FETCH_ASSOC);
    $minPrice = $priceResult['min_price'] ?? 0;
    $maxPrice = $priceResult['max_price'] ?? 35.00;

    $sql = "SELECT id, name, image, video, price, type FROM products WHERE 1=1";
    $params = [];

    $filterConditions = [];
    if (isset($_GET['filter_apple'])) {
        $filterConditions[] = "type = :apple";
        $params[':apple'] = 'apple';
    }
    if (isset($_GET['filter_bleu'])) {
        $filterConditions[] = "type = :bleu";
        $params[':bleu'] = 'bleu';
    }
    if (isset($_GET['filter_samsung'])) {
        $filterConditions[] = "type = :samsung";
        $params[':samsung'] = 'samsung';
    }
    if (isset($_GET['filter_transparent'])) {
        $filterConditions[] = "type = :transparent";
        $params[':transparent'] = 'transparent';
    }

    if (!empty($filterConditions)) {
        $sql .= " AND (" . implode(" OR ", $filterConditions) . ")";
    }    

    if (isset($_GET['price_min']) && isset($_GET['price_max'])) {
        $min_price = filter_var($_GET['price_min'], FILTER_VALIDATE_FLOAT);
        $max_price = filter_var($_GET['price_max'], FILTER_VALIDATE_FLOAT);
        
        if ($min_price !== false && $max_price !== false) {
            $sql .= " AND price BETWEEN :min_price AND :max_price";
            $params[':min_price'] = $min_price;
            $params[':max_price'] = $max_price;
        }
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . htmlspecialchars($e->getMessage());
    exit;
}

// Gestion ajout au panier
if (isset($_POST['add_to_cart']) && isset($_POST['product_id']) && isset($_POST['csrf_token'])) {
    if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: index.php?error=csrf");
        exit;
    }
    
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($product_id !== false) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][] = $product_id;
        header("Location: index.php?action=added_to_cart");
        exit;
    }
}

// Gestion AJAX pour les favoris (ajout/suppression)
if (isset($_POST['ajax_toggle_favorites']) && isset($_POST['product_id']) && isset($_POST['csrf_token'])) {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Erreur de sécurité']);
        exit;
    }
    
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($product_id !== false) {
        if (!isset($_SESSION['favorites'])) {
            $_SESSION['favorites'] = [];
        }
        
        $key = array_search($product_id, $_SESSION['favorites']);
        if ($key !== false) {
            unset($_SESSION['favorites'][$key]);
            $_SESSION['favorites'] = array_values($_SESSION['favorites']);
            echo json_encode(['success' => true, 'message' => 'Retiré des favoris', 'action' => 'removed']);
        } else {
            $_SESSION['favorites'][] = $product_id;
            echo json_encode(['success' => true, 'message' => 'Ajouté aux favoris', 'action' => 'added']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'opération']);
    }
    exit;
}

// Gestion ajout aux favoris (fallback pour les navigateurs sans JS)
if (isset($_POST['add_to_favorites']) && isset($_POST['product_id']) && isset($_POST['csrf_token'])) {
    if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: index.php?error=csrf");
        exit;
    }
    
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($product_id !== false) {
        if (!isset($_SESSION['favorites'])) {
            $_SESSION['favorites'] = [];
        }
        if (!in_array($product_id, $_SESSION['favorites'])) {
            $_SESSION['favorites'][] = $product_id;
        }
        header("Location: index.php?action=added_to_favorites");
        exit;
    }
}

// Générer un token CSRF pour la session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue de Produits</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="images/logosite.png">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div id="toast-notification" class="toast-notification">
        <i class="fas fa-heart"></i>
        <span id="toast-message"></span>
        <button id="toast-close">&times;</button>
    </div>

    <div class="catalogue-container">
        <div class="sidebar">
            <h2>Filtres</h2>
            <form method="get" id="filterForm">
                <div class="filter-section">
                    <h3>Marque</h3>
                    <label><input type="checkbox" name="filter_apple" <?php echo isset($_GET['filter_apple']) ? 'checked' : ''; ?>> Apple</label>
                    <label><input type="checkbox" name="filter_samsung" <?php echo isset($_GET['filter_samsung']) ? 'checked' : ''; ?>> Samsung</label>
                    <label><input type="checkbox" name="filter_bleu" <?php echo isset($_GET['filter_bleu']) ? 'checked' : ''; ?>> Bleu</label>
                    <label><input type="checkbox" name="filter_transparent" <?php echo isset($_GET['filter_transparent']) ? 'checked' : ''; ?>> Transparent</label>
                </div>
                <div class="filter-section">
                    <h3>Prix</h3>
                    <div class="price-slider">
                        <input type="range" name="price_min" min="<?php echo $minPrice; ?>" max="<?php echo $maxPrice; ?>" 
                            value="<?php echo isset($_GET['price_min']) ? $_GET['price_min'] : $minPrice; ?>" step="0.01" id="minPrice">
                        <input type="range" name="price_max" min="<?php echo $minPrice; ?>" max="<?php echo $maxPrice; ?>" 
                            value="<?php echo isset($_GET['price_max']) ? $_GET['price_max'] : $maxPrice; ?>" step="0.01" id="maxPrice">
                    </div>
                    <div class="price-values">
                        <span>Prix: <span id="price-min"><?php echo isset($_GET['price_min']) ? $_GET['price_min'] : $minPrice; ?></span>€ - 
                              <span id="price-max"><?php echo isset($_GET['price_max']) ? $_GET['price_max'] : $maxPrice; ?></span>€</span>
                    </div>
                </div>
                <button type="submit" class="filter-button">Appliquer</button>
                <button type="button" id="resetFilters" class="reset-button">Réinitialiser</button>
            </form>
        </div>

        <div class="main-content">
            <h1>Catalogue</h1>
            <?php if (isset($_GET['action']) && $_GET['action'] === 'added_to_cart'): ?>
                <div class="alert success">Produit ajouté au panier !</div>
            <?php endif; ?>
            <?php if (isset($_GET['action']) && $_GET['action'] === 'added_to_favorites'): ?>
                <div class="alert success">Produit ajouté aux favoris !</div>
            <?php endif; ?>
            
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <a href="product-detail.php?id=<?php echo htmlspecialchars($product['id']); ?>">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                        <form method="post" class="favorites-form" data-product-id="<?php echo htmlspecialchars($product['id']); ?>">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="button" name="add_to_favorites" class="favorites-button"
                                    <?php echo (isset($_SESSION['favorites']) && in_array($product['id'], $_SESSION['favorites'])) ? 'data-favorited="true"' : ''; ?>>
                                <i class="<?php echo (isset($_SESSION['favorites']) && in_array($product['id'], $_SESSION['favorites'])) ? 'fas' : 'far'; ?> fa-heart"></i>
                            </button>
                        </form>
                    </div>
                    <div class="product-details">
                        <h3><a href="product-detail.php?id=<?php echo htmlspecialchars($product['id']); ?>"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                        <p class="price">€<?php echo htmlspecialchars($product['price']); ?></p>
                        <form method="post" class="cart-form">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit" name="add_to_cart" class="add-to-cart">Ajouter au panier</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="action-buttons">
                <a href="cart.php" class="action-button">Voir le panier</a>
                <a href="favorites.php" class="action-button secondary">Mes favoris</a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        // Toast notification handling
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.getElementById('toast-notification');
            const closeBtn = document.getElementById('toast-close');
            closeBtn.addEventListener('click', function() {
                toast.classList.remove('show');
            });
        });

        // Price sliders handling
        const minSlider = document.getElementById('minPrice');
        const maxSlider = document.getElementById('maxPrice');
        const minPriceLabel = document.getElementById('price-min');
        const maxPriceLabel = document.getElementById('price-max');

        minSlider.addEventListener('input', function () {
            if (parseFloat(minSlider.value) > parseFloat(maxSlider.value)) {
                maxSlider.value = minSlider.value;
            }
            minPriceLabel.textContent = parseFloat(minSlider.value).toFixed(2);
        });

        maxSlider.addEventListener('input', function () {
            if (parseFloat(maxSlider.value) < parseFloat(minSlider.value)) {
                minSlider.value = maxSlider.value;
            }
            maxPriceLabel.textContent = parseFloat(maxSlider.value).toFixed(2);
        });

        // Reset filters
        document.getElementById('resetFilters').addEventListener('click', function () {
            window.location.href = window.location.pathname;
        });

        // Show toast notification
        function showToast(message, isSuccess = true) {
            const toast = document.getElementById('toast-notification');
            const toastMessage = document.getElementById('toast-message');
            toastMessage.textContent = message;
            toast.className = 'toast-notification show' + (isSuccess ? ' success' : ' error');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // AJAX handling for favorites
        document.addEventListener('DOMContentLoaded', function() {
            const favoriteButtons = document.querySelectorAll('.favorites-button');
            
            favoriteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('.favorites-form');
                    const productId = form.dataset.productId;
                    const csrfToken = form.querySelector('input[name="csrf_token"]').value;
                    const heartIcon = this.querySelector('i');
                    const isFavorited = this.dataset.favorited === 'true';
                    this.disabled = true;

                    fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `ajax_toggle_favorites=1&product_id=${productId}&csrf_token=${csrfToken}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.action === 'added') {
                                heartIcon.className = 'fas fa-heart';
                                this.dataset.favorited = 'true';
                                showToast(data.message, true);
                            } else if (data.action === 'removed') {
                                heartIcon.className = 'far fa-heart';
                                this.dataset.favorited = 'false';
                                showToast(data.message, true);
                            }
                        } else {
                            showToast(data.message, false);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        showToast('Erreur lors de l\'opération', false);
                    })
                    .finally(() => {
                        this.disabled = false;
                    });
                });
            });
        });
    </script>

    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding-top: 60px;
            color: #333;
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #004aad;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-notification.error {
            background-color: #d32f2f;
        }

        .toast-notification i {
            font-size: 16px;
        }

        #toast-close {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }

        /* Catalogue Container */
        .catalogue-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            gap: 20px;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #004aad;
        }

        .filter-section {
            margin-bottom: 20px;
        }

        .filter-section h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }

        .filter-section label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .filter-section input[type="checkbox"] {
            margin-right: 8px;
            accent-color: #004aad;
        }

        .price-slider {
            margin: 10px 0;
        }

        .price-values {
            font-size: 14px;
            text-align: center;
        }

        input[type="range"] {
            width: 100%;
            height: 6px;
            background: #ddd;
            border-radius: 3px;
            outline: none;
        }

        input[type="range"]::-webkit-slider-thumb {
            width: 16px;
            height: 16px;
            background: #004aad;
            border-radius: 50%;
            cursor: pointer;
        }

        input[type="range"]::-moz-range-thumb {
            width: 16px;
            height: 16px;
            background: #004aad;
            border-radius: 50%;
            cursor: pointer;
        }

        .filter-button, .reset-button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }

        .filter-button {
            background: #004aad;
            color: white;
        }

        .filter-button:hover {
            background: #003087;
        }

        .reset-button {
            background: #f5f5f5;
            color: #333;
        }

        .reset-button:hover {
            background: #e0e0e0;
        }

        /* Main Content */
        .main-content {
            flex: 1;
        }

        .main-content h1 {
            font-size: 24px;
            color: #004aad;
            margin-bottom: 20px;
            text-align: left;
        }

        .alert.success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.2s;
        }

        .product-card:hover {
            transform: translateY(-4px);
        }

        .product-image {
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            padding: 10px;
        }

        .product-details {
            padding: 15px;
            text-align: center;
        }

        .product-details h3 {
            font-size: 16px;
            margin: 0 0 10px;
            color: #333;
            height: 40px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-details a {
            text-decoration: none;
            color: inherit;
        }

        .price {
            font-size: 18px;
            font-weight: bold;
            color: #004aad;
            margin-bottom: 10px;
        }

        .add-to-cart {
            width: 100%;
            padding: 10px;
            background:rgb(0, 0, 0);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .add-to-cart:hover {
            background:rgb(41, 41, 41);
        }

        .favorites-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .favorites-button i {
            color: #333;
            font-size: 16px;
        }

        .favorites-button[data-favorited="true"] i {
            color: #d32f2f;
        }

        .favorites-button:hover i {
            color: #d32f2f;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .action-button {
            padding: 10px 20px;
            background: #004aad;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .action-button.secondary {
            background: #f5f5f5;
            color: #333;
        }

        .action-button:hover {
            background: #003087;
        }

        .action-button.secondary:hover {
            background: #e0e0e0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .catalogue-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .product-grid {
                grid-template-columns: 1fr;
            }

            .product-image img {
                height: 150px;
            }
        }
    </style>