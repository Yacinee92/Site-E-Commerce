<?php
include 'bdd.php';

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=$dbname", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Initialisation des variables
    $search = '';
    $results = [];
    
    // Traitement de la recherche
    if (isset($_GET['query']) && !empty($_GET['query'])) {
        $search = htmlspecialchars($_GET['query']);
        
        // Préparation et exécution de la requête
        $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :query");
        $stmt->execute(['query' => '%' . $search . '%']);
        
        // Récupération des résultats
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="images/logosite.png">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="catalogue-container">
    <div class="sidebar">
        <h2>Recherche</h2>
        <form method="get" action="search.php">
            <input type="text" name="query" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher un produit..." class="search-input">
            <button type="submit" class="filter-button">Rechercher</button>
        </form>
    </div>

    <div class="main-content">
        <h1>Résultats pour "<?= htmlspecialchars($search) ?>"</h1>
        
        <?php if (!empty($results)): ?>
            <div class="product-grid">
                <?php foreach ($results as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <a href="product-detail.php?id=<?= htmlspecialchars($product['id']) ?>">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                <?php elseif (!empty($product['video'])): ?>
                                    <video controls>
                                        <source src="<?= htmlspecialchars($product['video']) ?>" type="video/mp4">
                                        Votre navigateur ne supporte pas les vidéos.
                                    </video>
                                <?php endif; ?>
                            </a>
                            <form method="post" class="favorites-form" data-product-id="<?= htmlspecialchars($product['id']) ?>">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                                <input type="hidden" name="csrf_token" value="<?= isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '' ?>">
                                <button type="button" name="add_to_favorites" class="favorites-button"
                                        <?php echo (isset($_SESSION['favorites']) && in_array($product['id'], $_SESSION['favorites'])) ? 'data-favorited="true"' : ''; ?>>
                                    <i class="<?php echo (isset($_SESSION['favorites']) && in_array($product['id'], $_SESSION['favorites'])) ? 'fas' : 'far'; ?> fa-heart"></i>
                                </button>
                            </form>
                        </div>
                        <div class="product-details">
                            <h3><a href="product-detail.php?id=<?= htmlspecialchars($product['id']) ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                            <p class="price">€<?= htmlspecialchars($product['price']) ?></p>
                            <form method="post" action="add_to_cart.php" class="cart-form">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                                <input type="hidden" name="csrf_token" value="<?= isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '' ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart">Ajouter au panier</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucun résultat trouvé pour votre recherche.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    // Toast notification handling (for consistency with index.php)
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

                fetch('index.php', {
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

        // Toast notification function
        function showToast(message, isSuccess = true) {
            const toast = document.createElement('div');
            toast.className = 'toast-notification' + (isSuccess ? ' success' : ' error');
            toast.innerHTML = `<i class="fas fa-heart"></i><span>${message}</span><button class="toast-close">×</button>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
            toast.querySelector('.toast-close').addEventListener('click', () => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            });
        }
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
        background-color: #1a1a1a;
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

    .toast-close {
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
        color: #2e7d32;
    }

    .search-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .filter-button {
        width: 100%;
        padding: 10px;
        background: #2e7d32;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .filter-button:hover {
        background: #1b5e20;
    }

    /* Main Content */
    .main-content {
        flex: 1;
    }

    .main-content h1 {
        font-size: 24px;
        color: #2e7d32;
        margin-bottom: 20px;
        text-align: left;
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

    .product-image img, .product-image video {
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
        color: #2e7d32;
        margin-bottom: 10px;
    }

    .add-to-cart {
        width: 100%;
        padding: 10px;
        background: #1a1a1a;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .add-to-cart:hover {
        background: #333;
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

        .product-image img, .product-image video {
            height: 150px;
        }
    }
</style>