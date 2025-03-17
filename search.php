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
</head>
<body>
<?php include 'navbar.php'; ?>


<div class="container" style="margin-top: 80px; padding: 20px;">
    <h1>Résultats pour "<?= htmlspecialchars($search) ?>"</h1>
    
    <div class="product-list">
        <?php if (!empty($results)): ?>
            <?php foreach ($results as $product): ?>
                <div class="product-item">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <?php endif; ?>
                    
                    <?php if (!empty($product['video'])): ?>
                        <video controls>
                            <source src="<?= htmlspecialchars($product['video']) ?>" type="video/mp4">
                            Votre navigateur ne supporte pas les videos.
                        </video>
                    <?php endif; ?>
                    
                    <h2>
                        <a href="product-detail.php?id=<?= htmlspecialchars($product['id']) ?>">
                            <?= htmlspecialchars($product['name']) ?>
                        </a>
                    </h2>
                    <p>Prix: €<?= htmlspecialchars($product['price']) ?></p>
                    <form method="post" action="add_to_cart.php">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                        <button type="submit" name="add_to_cart">Ajouter au Panier</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun résultat trouvé pour votre recherche.</p>
        <?php endif; ?>
    </div>
</div>
<style>
    /* Barre de défilement */
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
    padding-top: 60px;
}

.container {
    margin-top: 80px;
    padding: 20px;
}

/* Style pour les curseurs de prix */
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

/* Style pour les cases à cocher */
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

/* Boutons spécifiques */
button#updatePrice {
    background: url('images/loupev.png') no-repeat center center;
    background-size: contain;
    border: none;
    width: 30px;
    height: 30px;
    cursor: pointer;
    transition: transform 0.2s ease;
    position: relative;
    top: -5px;
}

button#updatePrice:hover {
    transform: scale(1.05);
}

button#resetFilters {
    background: url('images/remove.png') no-repeat center center;
    background-size: contain;
    border: none;
    width: 30px;
    height: 30px;
    cursor: pointer;
    transition: transform 0.2s ease;
    position: relative;
    top: -5px;
}

button#resetFilters:hover {
    transform: scale(1.05);
}

/* Labels */
label {
    display: flex;
    justify-content: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 500;
    color: #333;
    margin-bottom: 20px;
}

/* En-tête du catalogue */
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

/* Grille de produits */
.product-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
    margin-top: 20px;
}

/* Carte de produit */
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
    text-decoration: none;
    color: inherit;
    position: relative; /* Pour positionner les favoris */
}

.product-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 12px rgba(0, 0, 0, 0.1);
}

/* Liens dans les produits */
.product-item a {
    text-decoration: none;
    color: inherit;
    display: block;
    transition: color 0.2s ease;
}

.product-item a:hover {
    color: #0f3e26;
}

/* Images des produits */
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
    opacity: 0.9;
}

/* Vidéos des produits */
.product-item video {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-bottom: 1px solid #f0f0f0;
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
    color: #0f3e26;
}

/* Prix du produit */
.product-item p {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 8px 0;
}

/* Bouton d'ajout au panier */
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
    border-radius: 20px;
    transition: all 0.2s ease;
    margin-top: auto;
    font-weight: 500;
}

.product-item button:hover {
    background-color: #0f3e26;
    transform: scale(1.02);
}

/* Conteneur de boutons */
.button-container {
    display: flex;
    gap: 10px;
    margin-top: auto;
}

.cart-form {
    flex: 1;
}

/* Boutons de favoris */
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
    color: rgb(172, 0, 0);
    transform: scale(1.2);
}

/* Boutons d'action */
.action-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 40px;
}

.checkout-button, .favorites-button-link {
    display: block;
    width: max-content;
    margin: 0 auto 40px;
    background-color: #145a32;
    color: white;
    padding: 12px 24px;
    text-decoration: none;
    font-size: 16px;
    border-radius: 30px;
    transition: all 0.2s ease;
    text-align: center;
    box-shadow: 0 2px 5px rgba(20, 90, 50, 0.15);
    font-weight: 500;
}

.checkout-button:hover, .favorites-button-link:hover {
    background-color: #0f3e26;
    transform: scale(1.03);
}

.favorites-button-link {
    background-color: #0f3e26;
    box-shadow: 0 2px 5px rgba(44, 120, 115, 0.15);
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

@media (max-width: 480px) {
    .product-list {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .product-item {
        max-width: 100%;
    }
    
    .product-item img {
        height: 150px;
    }
    
    .button-container {
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
</style>
<?php include 'footer.php'; ?>
</body>
</html>