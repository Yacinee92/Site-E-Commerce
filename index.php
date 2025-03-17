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

    $filters = [];
    $sql = "SELECT id, name, image, video, price, type FROM products WHERE 1=1";

    $filterConditions = [];
    if (isset($_GET['filter_php'])) {
        $filterConditions[] = "type = 'PHP'";
    }
    if (isset($_GET['filter_bleu'])) {
        $filterConditions[] = "type = 'bleu'";
    }
    if (isset($_GET['filter_js'])) {
        $filterConditions[] = "type = 'JS'";
    }
    if (isset($_GET['filter_mysql'])) {
        $filterConditions[] = "type = 'MySQL'";
    }

    if (!empty($filterConditions)) {
        $sql .= " AND (" . implode(" OR ", $filterConditions) . ")";
    }    

    if (isset($_GET['price_min']) && isset($_GET['price_max'])) {
        $min_price = $_GET['price_min'];
        $max_price = $_GET['price_max'];
        $sql .= " AND price BETWEEN $min_price AND $max_price";
    }


        
    $stmt = $pdo->query("$sql");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . htmlspecialchars($e->getMessage());
    exit;
}

// Gestion ajout au panier
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

// Gestion ajout aux favoris
if (isset($_POST['add_to_favorites'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($product_id !== false) {
        if (!isset($_SESSION['favorites'])) {
            $_SESSION['favorites'] = [];
        }
        if (!in_array($product_id, $_SESSION['favorites'])) {
            $_SESSION['favorites'][] = $product_id;
        }
        header("Location: index.php");
        exit;
    }
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
    <div class="catalogue-container">
        <h1 class="headerr">Catalogue de Produits</h1>
        <form method="get" id="filterForm">
    <label>
        <input type="checkbox" name="filter_php" <?php echo isset($_GET['filter_php']) ? 'checked' : ''; ?>>
        En PHP&nbsp;&nbsp;&nbsp;
        <input type="checkbox" name="filter_bleu" <?php echo isset($_GET['filter_bleu']) ? 'checked' : ''; ?>>
        En Bleu&nbsp;&nbsp;&nbsp;
        <input type="checkbox" name="filter_js" <?php echo isset($_GET['filter_js']) ? 'checked' : ''; ?>>
        En JS&nbsp;&nbsp;&nbsp;
        <input type="checkbox" name="filter_mysql" <?php echo isset($_GET['filter_mysql']) ? 'checked' : ''; ?>>
        En MySQL&nbsp;&nbsp;&nbsp;
        <button type="submit" id="updatePrice"></button>
        <button type="button" id="resetFilters"></button>


    </label>
    <div class="price-slider">
        <input type="range" name="price_min" min="<?php echo $minPrice; ?>" max="<?php echo $maxPrice; ?>" 
            value="<?php echo isset($_GET['price_min']) ? $_GET['price_min'] : $minPrice; ?>" step="1" 
            style="width: 25%;" id="minPrice">
        <input type="range" name="price_max" min="<?php echo $minPrice; ?>" max="<?php echo $maxPrice; ?>" 
            value="<?php echo isset($_GET['price_max']) ? $_GET['price_max'] : $maxPrice; ?>" step="1" 
            style="width: 25%;" id="maxPrice">
    </div>
    <div class="price-values">
        <span>Prix min : <span id="price-min"><?php echo isset($_GET['price_min']) ? $_GET['price_min'] : $minPrice; ?></span></span>
        <span>Prix max : <span id="price-max"><?php echo isset($_GET['price_max']) ? $_GET['price_max'] : $maxPrice; ?></span></span>
    </div>
</form>

        
        
        
        <div class="product-list">
            <?php foreach ($products as $product): ?>
            <div class="product-item">
                <a href="product-detail.php?id=<?php echo htmlspecialchars($product['id']); ?>">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </a>
                <div class="product-info">
                    <a href="product-detail.php?id=<?php echo htmlspecialchars($product['id']); ?>">
                        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                    </a>
                    <p>Prix: €<?php echo htmlspecialchars($product['price']); ?></p>
                    <div class="button-container">
                        <form method="post" class="cart-form">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                            <button type="submit" name="add_to_cart">Ajouter au Panier</button>
                        </form>
                        <form method="post" class="favorites-form">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                            <button type="submit" name="add_to_favorites" class="favorites-button">
                                <i class="far fa-heart"></i> Favoris
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

            <!-- <div class="action-buttons">
            <a href="cart.php" class="checkout-button">Voir le Panier</a>
            <a href="favorites.php" class="favorites-button-link">Voir mes Favoris</a>
        </div>-->
    </div>

    <?php include 'footer.php'; ?>
    <script>
        const minSlider = document.getElementById('minPrice');
const maxSlider = document.getElementById('maxPrice');
const minPriceLabel = document.getElementById('price-min');
const maxPriceLabel = document.getElementById('price-max');

minSlider.addEventListener('input', function () {
    if (parseInt(minSlider.value) > parseInt(maxSlider.value)) {
        maxSlider.value = minSlider.value;
    }
    minPriceLabel.textContent = minSlider.value;
});

maxSlider.addEventListener('input', function () {
    if (parseInt(maxSlider.value) < parseInt(minSlider.value)) {
        minSlider.value = maxSlider.value;
    }
    maxPriceLabel.textContent = maxSlider.value;
});


    document.getElementById('resetFilters').addEventListener('click', function () {
        window.location.href = window.location.pathname; // Recharge la page sans paramètres
    });

        
    </script>
</body>
</html>




<style>
    
::-webkit-scrollbar {
    width: 10px;
  }
  
  /* Track */
  ::-webkit-scrollbar-track {
    background: #121212; 
  }
   
  /* Handle */
  ::-webkit-scrollbar-thumb {
    background: rgb(38, 38, 38); 
  }
  
  /* Handle on hover */
  ::-webkit-scrollbar-thumb:hover {
    background: rgb(38, 38, 38); 
  }

/* Styles généraux */
body {
    font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
    background-color: #f8f9fa;
    color: #333;
    margin: 0;
    padding-top: 60px;}

.price-slider {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 5px 0;
}

.price-values {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    font-size: 16px;
    font-weight: 500;
    
}

/* Style pour les curseurs de prix */
input[type="range"] {
    -webkit-appearance: none;
    width: 100%;
    height: 8px;
    background: #d3d3d3;
    outline: none;
    opacity: 0.7;
    transition: opacity 0.2s;
    border-radius: 5px;
}

input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: #1b4332;
    cursor: pointer;
    border-radius: 50%;
    box-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
}

input[type="range"]::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: #1b4332;
    cursor: pointer;
    border-radius: 50%;
    box-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
}
    /* Style pour les boutons à cocher */
    input[type="checkbox"] {
        appearance: none;
        width: 15px;
        height: 15px;
        border: 2px solid #1b4332;
        border-radius: 4px;
        outline: none;
        cursor: pointer;
        position: relative;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    input[type="checkbox"]:checked {
        background-color: #1b4332;
        border-color: #1b4332;
    }

    input[type="checkbox"]:checked::after {
        content: '✔';
        color: white;
        font-size: 14px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    button#updatePrice {
        background: url('images/loupev.png') no-repeat center center;
        background-size: contain;
        border: none;
        width: 30px; /* Adjust the size as needed */
        height: 30px; /* Adjust the size as needed */
        cursor: pointer;
        transition: transform 0.2s ease;
        position: relative;
        top: -5px;
    }

    button#updatePrice:hover {
        transform: scale(1.05);
    }

    label {
        display: flex;
        justify-content: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 500;
        color: #333;
        margin-bottom: 20px;
    }
    .product-item a {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: color 0.2s ease;
    }

    .product-item a:hover {
        color: #0f3e26; /* Légère variation de couleur au survol pour indiquer qu'il s'agit d'un lien */
    }

    button#resetFilters {
        background: url('images/remove.png') no-repeat center center;
        background-size: contain;
        border: none;
        width: 30px; /* Adjust the size as needed */
        height: 30px; /* Adjust the size as needed */
        cursor: pointer;
        transition: transform 0.2s ease;
        position: relative;
        top: -5px;
    }

    button#resetFilters:hover {
        transform: scale(1.05);
    }

/* Ajuster le conteneur du catalogue pour qu'il soit en dessous de la navbar */
.catalogue-container {
    padding-top: 20px;
}

/* Container principal */
.catalogue-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* En-tête du catalogue - Corrigé pour affichage correct */
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

/* Grille de produits - Cadres plus petits */
.product-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
    margin-top: 20px;
}

/* Carte de produit - Cadres plus petits */
.product-item {
    background-color: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    max-width: 220px;
    margin: 0 auto;
    text-decoration: none; /* Supprime le soulignement de tous les liens dans les cartes produit */
    color: inherit; /* Garde la couleur par défaut du texte */

}

.product-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 12px rgba(0, 0, 0, 0.1);
}

/* Image du produit - Rognée */
.product-item img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    object-position: center;
    border-bottom: 1px solid #f0f0f0;
}

.product-item a img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    object-position: center;
    border-bottom: 1px solid #f0f0f0;
    transition: opacity 0.2s ease;
}

.product-item a:hover img {
    opacity: 0.9; /* Effet léger au survol de l'image */
}

/* Contenu du produit */
.product-info {
    padding: 12px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

/* Titre du produit */
.product-item h2 {
    font-size: 16px;
    margin: 5px 0;
    color: #1b4332;
    height: 40px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    font-weight: 500;
}

.product-item h2 a {
    text-decoration: none;
    color: #1b4332;
    transition: color 0.2s ease;
}

.product-item h2 a:hover {
    color: #0f3e26; /* Légère variation de couleur au survol pour indiquer qu'il s'agit d'un lien */
}



/* Prix du produit */
.product-item p {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 8px 0;
}

/* Bouton d'ajout au panier - Style Apple */
.product-item button {
    border: none;
    outline: 0;
    padding: 10px;
    color: white;
    background-color: #1b4332;
    text-align: center;
    cursor: pointer;
    width: 100%;
    font-size: 14px;
    border-radius: 20px; /* Style Apple arrondi */
    transition: all 0.2s ease;
    margin-top: auto;
    font-weight: 500;
}

.product-item button:hover {
    background-color: #0f3e26;
    transform: scale(1.02);
}

/* Bouton voir le panier - Style Apple */
.checkout-button {
    display: block;
    width: max-content;
    margin: 0 auto 40px;
    background-color: #145a32;
    color: white;
    padding: 12px 24px;
    text-decoration: none;
    font-size: 16px;
    border-radius: 30px; /* Style Apple plus arrondi */
    transition: all 0.2s ease;
    text-align: center;
    box-shadow: 0 2px 5px rgba(20, 90, 50, 0.15);
    font-weight: 500;
}

.checkout-button:hover {
    background-color: #0f3e26;
    transform: scale(1.03);
}

.button-container {
    display: flex;
    gap: 10px;
    margin-top: auto;
    }

    .cart-form {
    flex: 1;
    }

    .favorites-form {
    position: absolute;
    top: 10px;
    left: 10px;
    }

    .favorites-button {
    border: none;
    outline: 0;
    padding: 10px;
    color: white;
    background-color: transparent;
    text-align: center;
    cursor: pointer;
    font-size: 20px;
    transition: all 0.2s ease;
    }

    .favorites-button i {
    color: #0f3e26;
    }

    .favorites-button:hover i {
    color:rgb(172, 0, 0);
    transform: scale(1.2);
    }

    /* Style pour les liens d'action en bas */
    .action-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 40px;
    }

    .favorites-button-link {
    display: block;
    width: max-content;
    background-color: #0f3e26;
    color: white;
    padding: 12px 24px;
    text-decoration: none;
    font-size: 16px;
    border-radius: 30px;
    transition: all 0.2s ease;
    text-align: center;
    box-shadow: 0 2px 5px rgba(44, 120, 115, 0.15);
    font-weight: 500;
    }

    .favorites-button-link:hover {
    background-color: #0f3e26;
    transform: scale(1.03);
    }

    /* Ajustements responsifs */
    @media (max-width: 480px) {
        .button-container {
            flex-direction: column;
            gap: 5px;
        }

        .action-buttons {
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .product-list {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .product-item img {
            height: 120px;
        }

        .headerr {
            font-size: 20px;
            padding: 10px;
            width: 100%;
        }
        .catalogue-container {
            padding: 10px;
        }

        .price-slider {
            flex-direction: row;
            align-items: center;
        }

        .price-slider input {
            width: 45%;
            margin: 0 5px;
        }

        .price-values {
            flex-direction: row;
            align-items: center;
        }

        .price-values span {
            margin: 0 10px;
        }

        label {
            flex-direction: row;
            align-items: center;
        }
        }

        /* Styles responsifs */
        @media (max-width: 768px) {
        .product-list {
            grid-template-columns: repeat(2, 1fr);
        }

        .headerr {
            font-size: 24px;
            padding: 15px;
            width: 95%;
        }
        }


</style>


</body>
</html>
