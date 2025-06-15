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

    // Gestion ajout aux favoris
    if (isset($_POST['add_to_favorites'])) {
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        if ($product_id !== false) {
            if (!isset($_SESSION['favorites'])) {
                $_SESSION['favorites'] = [];
            }
            if (!in_array($product_id, $_SESSION['favorites'])) {
                $_SESSION['favorites'][] = $product_id;
                $successMessage = "Produit ajouté aux favoris avec succès !";
            } else {
                $successMessage = "Ce produit est déjà dans vos favoris.";
            }
            header("Location: product-detail.php?id=" . $product_id);
            exit;
        }
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

// Enregistrement d'un nouvel avis
if (isset($_POST['submit_review']) && isset($_POST['rating'])) {
    $rating = min(5, max(1, (int)$_POST['rating']));
    $comment = trim($_POST['comment'] ?? '');
    $username = trim($_POST['username'] ?? 'Anonyme');
    
    // Si pas de nom d'utilisateur fourni, utiliser celui de la session ou "Anonyme"
    if (empty($username)) {
        $username = $_SESSION['username'] ?? 'Anonyme';
    }
    
    $stmt = $pdo->prepare("INSERT INTO reviews (product_id, rating, comment, username) VALUES (?, ?, ?, ?)");
    $stmt->execute([$product['id'], $rating, $comment, $username]);
    header("Location: product-detail.php?id=" . $product['id']);
    exit;
}

// Récupérer les avis pour ce produit
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
$stmt->execute([$product['id']]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcul de la note moyenne
$stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE product_id = ?");
$stmt->execute([$product['id']]);
$reviewStats = $stmt->fetch(PDO::FETCH_ASSOC);
$avgRating = $reviewStats['avg_rating'] ? round($reviewStats['avg_rating'], 1) : 0;
$reviewCount = $reviewStats['count'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "San Francisco", "Helvetica Neue", Arial, sans-serif;
            background-color: #fafafa;
            color: #333;
            line-height: 1.6;
        }

        /* Conteneur principal */
        .product-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px 40px;
        }

        /* Section produit */
        .product-detail {
            display: flex;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 60px;
        }

        /* Image du produit */
        .product-image {
            flex: 1;
            padding: 40px;
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .product-image img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            object-fit: contain;
        }

        .product-video {
            margin-top: 20px;
        }

        .product-video video {
            width: 100%;
            max-height: 300px;
            border-radius: 8px;
        }

        /* Informations produit */
        .product-info {
            flex: 1;
            padding: 40px;
        }

        .product-info h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 1rem;
        }

        .product-info .price {
            font-size: 1.5rem;
            font-weight: 500;
            color: #0066cc;
            margin-bottom: 1.5rem;
        }

        .product-info .section {
            margin-bottom: 1.5rem;
        }

        .product-info .section h3 {
            font-size: 1rem;
            font-weight: 500;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .product-info .section p {
            font-size: 0.95rem;
            color: #333;
            line-height: 1.5;
        }

        /* Boutons */
        .product-actions {
            margin-top: 2rem;
        }

        .product-actions button,
        .product-actions a {
            display: inline-block;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .product-actions .add-to-cart-btn {
            background: #0066cc;
            color: #fff;
            margin-bottom: 1rem;
        }

        .product-actions .add-to-cart-btn:hover {
            background: #0052a3;
        }

        .product-actions .back-button {
            background: #f5f5f5;
            color: #666;
            border: 1px solid #ddd;
        }

        .product-actions .back-button:hover {
            background: #e9e9e9;
        }

        /* Section favoris */
        .favorite-section {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #0066cc;
        }

        .favorite-section p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .favorite-section .favorite-button {
            padding: 8px 16px;
            background: #0066cc;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .favorite-section .favorite-button:hover {
            background: #0052a3;
        }

        /* ====== SYSTÈME D'AVIS MINIMALISTE ====== */
        .reviews-section {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .reviews-header {
            padding: 30px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        .reviews-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
        }

        .rating-summary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .rating-score {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
        }

        .stars-display {
            display: flex;
            gap: 3px;
        }

        .star {
            font-size: 1.2rem;
            color: #ffd700;
        }

        .star.empty {
            color: #ddd;
        }

        .rating-text {
            color: #666;
            font-size: 0.9rem;
        }

        /* Formulaire d'avis minimaliste */
        .review-form {
            padding: 30px;
            border-bottom: 1px solid #eee;
        }

        .review-form h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }

        .form-group input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: border-color 0.2s ease;
        }

        .form-group input[type="text"]:focus {
            outline: none;
            border-color: #0066cc;
        }

        .star-rating {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
        }

        .star-input {
            font-size: 1.5rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .star-input:hover,
        .star-input.active {
            color: #ffd700;
        }

        .comment-textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
            font-family: inherit;
            resize: vertical;
            min-height: 80px;
            transition: border-color 0.2s ease;
        }

        .comment-textarea:focus {
            outline: none;
            border-color: #0066cc;
        }

        .submit-btn {
            background: #0066cc;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .submit-btn:hover {
            background: #0052a3;
        }

        /* Liste des avis */
        .reviews-list {
            padding: 0;
        }

        .review-item {
            padding: 25px 30px;
            border-bottom: 1px solid #f0f0f0;
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .reviewer-name {
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .review-rating {
            display: flex;
            gap: 2px;
        }

        .review-star {
            font-size: 1rem;
            color: #ffd700;
        }

        .review-star.empty {
            color: #ddd;
        }

        .review-date {
            color: #888;
            font-size: 0.85rem;
        }

        .review-comment {
            color: #555;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-top: 8px;
        }

        .no-reviews {
            text-align: center;
            padding: 40px;
            color: #888;
        }

        .no-reviews i {
            font-size: 2rem;
            color: #ddd;
            margin-bottom: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .product-detail {
                flex-direction: column;
            }

            .product-image,
            .product-info {
                padding: 20px;
            }

            .reviews-header,
            .review-form,
            .review-item {
                padding: 20px;
            }

            .rating-summary {
                flex-direction: column;
                gap: 10px;
            }

            .review-header {
                flex-direction: column;
                gap: 8px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="product-detail-container">
        <div class="product-detail">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php if ($product['video']): ?>
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
                
                <?php if ($product['description']): ?>
                    <div class="section description">
                        <h3>Description</h3>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($product['duration']): ?>
                    <div class="section duration">
                        <h3>Disponible</h3>
                        <p><?php echo htmlspecialchars($product['duration']); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($product['upload_date'] && $product['upload_date'] != '0000-00-00'): ?>
                    <div class="section upload-date">
                        <h3>Date d'ajout</h3>
                        <p><?php echo date('d/m/Y', strtotime($product['upload_date'])); ?></p>
                    </div>
                <?php endif; ?>

                <div class="favorite-section">
                    <p>Enregistrez ce produit dans vos favoris pour le retrouver facilement.</p>
                    <form method="post">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                        <button type="submit" name="add_to_favorites" class="favorite-button">Ajouter aux favoris</button>
                    </form>
                    <?php if (isset($successMessage)): ?>
                        <p style="color: #0066cc; font-size: 0.85rem; margin-top: 0.5rem;"><?php echo htmlspecialchars($successMessage); ?></p>
                    <?php endif; ?>
                </div>

                <div class="product-actions">
                    <form method="post">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart-btn">Ajouter au Panier</button>
                    </form>
                    <a href="index.php" class="back-button">Retour</a>
                </div>
            </div>
        </div>

        <!-- Section des avis minimaliste -->
        <div class="reviews-section">
            <div class="reviews-header">
                <h2>Avis clients</h2>
                <div class="rating-summary">
                    <span class="rating-score"><?= $avgRating ?></span>
                    <div class="stars-display">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star star <?= $i <= round($avgRating) ? '' : 'empty' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="rating-text"><?= $reviewCount ?> avis</span>
                </div>
            </div>

            <div class="review-form">
                <h3>Laisser un avis</h3>
                <form method="post">
                    <div class="form-group">
                        <label for="username">Votre nom</label>
                        <input type="text" id="username" name="username" 
                               value="<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '' ?>" 
                               placeholder="Votre nom (optionnel)">
                    </div>
                    
                    <div class="form-group">
                        <label>Note</label>
                        <div class="star-rating" id="star-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star star-input" data-value="<?= $i ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="rating-input" value="5">
                    </div>
                    
                    <div class="form-group">
                        <label for="comment">Commentaire</label>
                        <textarea id="comment" name="comment" class="comment-textarea" 
                                  placeholder="Partagez votre avis sur ce produit..."></textarea>
                    </div>
                    
                    <button type="submit" name="submit_review" class="submit-btn">Publier l'avis</button>
                </form>
            </div>

            <div class="reviews-list">
                <?php if ($reviews): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <span class="reviewer-name"><?= htmlspecialchars($review['username'] ?? 'Anonyme') ?></span>
                                    <div class="review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star review-star <?= $i <= $review['rating'] ? '' : 'empty' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <span class="review-date"><?= date('d/m/Y', strtotime($review['created_at'])) ?></span>
                            </div>
                            <?php if ($review['comment']): ?>
                                <div class="review-comment"><?= htmlspecialchars($review['comment']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-reviews">
                        <i class="fas fa-star"></i>
                        <p>Aucun avis pour ce produit.<br>Soyez le premier à donner votre avis !</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star-rating .star-input');
        const ratingInput = document.getElementById('rating-input');
        let currentRating = 5;

        // Initialiser l'affichage
        updateStars(currentRating);

        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                currentRating = index + 1;
                ratingInput.value = currentRating;
                updateStars(currentRating);
            });

            star.addEventListener('mouseover', function() {
                updateStars(index + 1);
            });
        });

        document.querySelector('.star-rating').addEventListener('mouseleave', function() {
            updateStars(currentRating);
        });

        function updateStars(rating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }
    });
    </script>
</body>
</html>