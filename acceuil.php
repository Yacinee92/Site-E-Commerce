<?php
session_start();



// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = "Visiteur"; // Valeur par défaut si non connecté
}

// Si l'utilisateur clique sur "Se déconnecter"
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: connexion.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coques iPhone 16 - Accueil</title>
    <link rel="icon" type="image/x-icon" href="images/logosite.png">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <section class="hero-section">
    <div class="hero-content">
        <h1>IProtect</h1>
        <h2>Protégez votre iPhone 16 avec style</h2>
        <p>Découvrez nos coques élégantes et résistantes</p>
        <a href="index.php" class="btn">Voir la boutique</a>
    </div>
</section>

<section class="about-section">
    <div class="about-content">
        <h2>À propos de nous</h2>
        <p>Chez IProtect, nous sommes passionnés par la protection de votre iPhone 16. Nous croyons que chaque téléphone mérite d'être protégé avec style et élégance. Nos coques sont conçues pour offrir une protection maximale tout en mettant en valeur le design unique de votre appareil.</p>
        <p>Nous nous engageons à fournir des produits de haute qualité qui répondent aux besoins de nos clients. Notre équipe travaille sans relâche pour s'assurer que chaque coque est fabriquée avec les meilleurs matériaux et les dernières technologies.</p>
        <p>Merci de nous faire confiance pour protéger votre précieux iPhone 16.</p>
    </div>
    <div class="icons-section">
        <div class="icon-item">
            <img src="images/shield.png" alt="Bouclier" class="icon">
            <h3>Qualité</h3>
        </div>
        <div class="icon-item">
            <img src="images/brush.png" alt="Design" class="icon">
            <h3>Design</h3>
        </div>
        <div class="icon-item">
            <img src="images/faste.png" alt="Livraison rapide" class="icon">
            <h3>Livraison rapide</h3>
        </div>
    </div>
</section>

<section class="showcase-section fade-in-section">
    <div class="showcase-content">
        <h2>Nos Produits Vedettes</h2>
        <div class="product-grid">
            <div class="product-item">
                <img src="images/coque3.jpg" alt="Produit 1" class="product-image">
                <h3>Coque iPhone 16 - Classique</h3>
                <p>Protection élégante et discrète</p>
                <a href="product-detail.php?id=3" class="btn">Voir le produit</a>
            </div>
            <div class="product-item">
                <img src="images/coque5.jpg" alt="Produit 2" class="product-image">
                <h3>Coque iPhone 16 - Résistante</h3>
                <p>Protection maximale contre les chocs</p>
                <a href="product-detail.php?id=5" class="btn">Voir le produit</a>
            </div>
            <div class="product-item">
                <img src="images/coqueb.jpg" alt="Produit 3" class="product-image">
                <h3>Coque iPhone 16 - Transparente</h3>
                <p>Protection invisible et légère</p>
                <a href="product-detail.php?id=8" class="btn">Voir le produit</a>
            </div>
        </div>
    </div>
</section>

<section class="categories-section fade-in-section">
    <div class="categories-content">
        <h2>Nos Catégories</h2>
        <div class="categories-grid">
            <div class="category-item">
            <img src="images/coques.jpg" alt="Catégorie 1" class="category-image" style="width: 80%; height: auto;">
            <h3>Coques Samsung</h3>
            <a href="product-detail.php?id=9" class="btn">Voir les produits</a>
            </div>
            <div class="category-item">
                <img src="images/coque5.jpg" alt="Catégorie 2" class="category-image">
                <h3>Coques Apple</h3>
                <a href="product-detail.php?id=5" class="btn">Voir les produits</a>
            </div>
            <div class="category-item">
                <img src="images/coqueperso.jpg" alt="Catégorie 3" class="category-image " style="width: 75%; height: auto;">
                <h3>Coques Transparentes</h3>
                <a href="product-detail.php?id=11" class="btn">Voir les produits</a>
            </div>
        </div>
    </div>
</section>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration de l'Intersection Observer
    const options = {
        root: null, // observe par rapport à la viewport
        rootMargin: '0px',
        threshold: 0.1 // déclenche quand 10% de l'élément est visible
    };
    
    // Fonction de callback
    const handleIntersect = (entries, observer) => {
        entries.forEach(entry => {
            // Si l'élément est visible
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                
                // Pour animer les éléments individuels avec délai
                if (entry.target.classList.contains('showcase-section') || 
                    entry.target.classList.contains('categories-section')) {
                    const items = entry.target.querySelectorAll('.product-item, .category-item');
                    items.forEach((item, index) => {
                        setTimeout(() => {
                            item.classList.add('is-visible');
                        }, 150 * index); // Délai croissant pour chaque élément
                    });
                }
                
                // On arrête d'observer une fois que l'animation est déclenchée
                observer.unobserve(entry.target);
            }
        });
    };
    
    // Création de l'observer
    const observer = new IntersectionObserver(handleIntersect, options);
    
    // Observer les sections avec fade-in-section
    document.querySelectorAll('.fade-in-section').forEach(section => {
        observer.observe(section);
    });
});
</script>
<style>
    
.categories-section {
    background: #fff;
    padding: 40px;
    text-align: center;
    width: 100%;
}

.categories-content {
    max-width: 1200px;
    margin: 0 auto;
}



.categories-content h2 {
    font-size: 2rem;
    color: rgb(9, 187, 0);
    margin-bottom: 20px;
}

.categories-grid {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

.category-item {
    background: white;
    padding: 20px;
    margin: 10px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
}

.category-image {
    width: 100%;
    height: auto;
    border-radius: 10px;
}

.category-item h3 {
    font-size: 1.2rem;
    color: #333;
    margin: 10px 0;
}
</style>

<style>
.showcase-section {
    background: #f9f9f9;
    padding: 40px;
    text-align: center;
    width: 100%;
}

.showcase-content {
    max-width: 1200px;
    margin: 0 auto;
}

.showcase-content h2 {
    font-size: 2rem;
    color: rgb(9, 187, 0);
    margin-bottom: 20px;
}

.product-grid {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

.product-item {
    background: white;
    padding: 20px;
    margin: 10px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
}

.product-image {
    width: 100%;
    height: auto;
    border-radius: 10px;
}

.product-item h3 {
    font-size: 1.2rem;
    color: #333;
    margin: 10px 0;
}

.product-item p {
    font-size: 1rem;
    color: #666;
    margin-bottom: 20px;
}
</style>

<style>
.about-section {
    background: #fff;
    padding: 40px;
    text-align: center;
    width: 100%;
}

.about-content {
    max-width: 800px;
    margin: 0 auto;
}

.about-content h2 {
    font-size: 2rem;
    color: rgb(9, 187, 0);
    margin-bottom: 20px;
}

.about-content p {
    font-size: 1.1rem;
    color: #333;
    margin-bottom: 20px;
}

.icons-section {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.icon-item {
    margin: 0 20px;
    text-align: center;
}

.icon {
    width: 50px;
    height: 50px;
}

.icon-item h3 {
    margin-top: 10px;
    font-size: 1rem;
    color: #333;
}

.fade-in-section {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 1s ease-out, transform 1s ease-out;
}

.fade-in-section.is-visible {
    opacity: 1;
    transform: translateY(0);
}

/* Style optionnel pour les éléments individuels avec délai */
.product-item, .category-item {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}

.product-item.is-visible, .category-item.is-visible {
    opacity: 1;
    transform: translateY(0);
}

/* Styles pour les petits écrans */
@media (max-width: 768px) {
    .hero-section {
        height: auto;
        padding: 20px;
    }

    .hero-content h1 {
        font-size: 2rem;
    }

    .hero-content h2 {
        font-size: 1.5rem;
    }

    .hero-content p {
        font-size: 1rem;
    }

    .btn {
        padding: 10px 20px;
        font-size: 1rem;
    }

    .about-content, .showcase-content, .categories-content {
        padding: 20px;
    }

    .product-item, .category-item {
        width: 100%;
        margin: 10px 0;
    }

    .icons-section {
        flex-direction: column;
    }

    .icon-item {
        margin: 10px 0;
    }
}

/* Styles pour les très petits écrans */
@media (max-width: 480px) {
    .hero-content h1 {
        font-size: 1.5rem;
    }

    .hero-content h2 {
        font-size: 1.2rem;
    }

    .hero-content p {
        font-size: 0.9rem;
    }

    .btn {
        padding: 8px 16px;
        font-size: 0.9rem;
    }

    .about-content, .showcase-content, .categories-content {
        padding: 10px;
    }

    .product-item, .category-item {
        padding: 10px;
    }
}
</style>

<main class="container">
    <section class="presentation">
        <h2>Pourquoi protéger votre téléphone ?</h2>
        <p>Votre iPhone 16 est un bijou de technologie, mais il est aussi fragile. Une simple chute peut l'endommager. Avec nos coques spécialement conçues, offrez-lui une protection optimale tout en conservant son élégance.</p>
        
        <h3>Les avantages de nos coques :</h3>
        <ul>
            <li>Protection contre les chocs et les rayures</li>
            <li>Design élégant et ergonomique</li>
            <li>Matériaux résistants et durables</li>
            <li>Compatibles avec la recharge sans fil</li>
        </ul>

        <a href="index.php" class="btn">Voir la boutique</a>
    </section>
</main>
    
    <?php include 'footer.php'; ?>


    <style>
     /* Réinitialisation des marges et styles de base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background: #f4f4f9;
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* SECTION HERO - Centrage vertical */
.hero-section {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 90vh; /* Ajustement pour ne pas trop dépasser */
    text-align: center;
    padding: 40px;
    background: linear-gradient(135deg,rgb(20, 93, 32),rgb(0, 219, 58)); /* Dégradé */
    width: 100%;
}

.hero-content {
    max-width: 600px;
    color: white;
}   

.hero-content h2 {
    font-size: 2.5rem;
    margin-bottom: 10px;
    animation: titre 2s ease 0s 1 normal none;
    color: white;
}
@keyframes titre {
  0% {
	transform: scale(0.5);
	transform-origin: 50% 0%;
  }

  100% {
	transform: scale(1);
	transform-origin: 50% 0%;
  }
}
.hero-content p {
    font-size: 1.2rem;
    margin-bottom: 20px;
}

/* Boutons plus arrondis et modernes */
.btn {
    display: inline-block;
    background: white;
    color:rgb(4, 202, 40);
    padding: 12px 30px;
    text-decoration: none;
    font-size: 1.2rem;
    font-weight: bold;
    border-radius: 30px;
    transition: 0.3s;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

.btn:hover {
    background:rgb(0, 196, 10);
    color: white;
    transform: scale(1.05);
    animation: vibre 2s ease 0s infinite normal none;
}

@keyframes vibre {
  0% {
	transform: translate(0);
  }

  20% {
	transform: translate(-2px, 2px);
  }

  40% {
	transform: translate(-2px, -2px);
  }

  60% {
	transform: translate(2px, 2px);
  }

  80% {
	transform: translate(2px, -2px);
  }

  100% {
	transform: translate(0);
  }
}

/* CONTAINER PRINCIPAL - DISPOSITION EN COLONNE */
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    padding: 20px;
}

.presentation {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 800px;
    text-align: center;
    margin-bottom: 40px;
}

.presentation h2 {
    color:rgb(9, 187, 0);
}

.presentation ul {
    list-style-type: none;
    padding: 0;
    text-align: center;
}

.presentation ul li {
    background:rgb(223, 255, 213);
    padding: 10px;
    margin: 10px 0;
    border-radius: 10px;
}

/* FOOTER */
footer {
    background: #222;
    color: white;
    text-align: center;
    padding: 1rem;
    width: 100%;
}


/* Compense la hauteur de la navbar */
body {
    padding-top: 60px; /* Doit être égal à la hauteur de la navbar */
}


    </style>
</body>
</html>
