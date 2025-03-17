<?php
session_start();
include 'bdd.php';

// Vérification si l'utilisateur est un administrateur (à adapter selon votre système d'authentification)
// if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
//     header('Location: login.php');
//     exit;
// }

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Supprimer un produit
    if (isset($_GET['delete'])) {
        $id = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
        if ($id !== false) {
            // Récupérer les informations du produit pour supprimer les fichiers
            $stmt = $pdo->prepare("SELECT image, video FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Supprimer l'image si elle existe
            if (!empty($product['image']) && file_exists($product['image'])) {
                unlink($product['image']);
            }
            
            // Supprimer la vidéo si elle existe
            if (!empty($product['video']) && file_exists($product['video'])) {
                unlink($product['video']);
            }
            
            // Supprimer de la base de données
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            
            // Rediriger pour éviter de supprimer à nouveau en cas de rafraîchissement
            header("Location: admin-products.php");
            exit;
        }
    }
    
    // Récupérer tous les produits
    $stmt = $pdo->query("SELECT id, name, image, price FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits - iProtect Admin</title>
    <style>
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 1000px;
            margin: 80px auto 20px;
            padding: 20px;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #1b4332;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .add-button {
            background-color: #1b4332;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        
        .add-button:hover {
            background-color: #0f3e26;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        td img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .edit-button, .delete-button {
            padding: 6px 12px;
            border-radius: 15px;
            text-decoration: none;
            font-size: 14px;
        }
        
        .edit-button {
            background-color: #17a2b8;
            color: white;
        }
        
        .delete-button {
            background-color: #dc3545;
            color: white;
        }
        
        .no-products {
            text-align: center;
            padding: 30px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="header-actions">
            <h1>Gestion des Produits</h1>
            <a href="admin-add-product.php" class="add-button">+ Ajouter un produit</a>
        </div>
        
        <?php if (count($products) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td>
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <span>Aucune image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>€<?php echo htmlspecialchars($product['price']); ?></td>
                            <td class="action-buttons">
                                <a href="admin-edit-product.php?id=<?php echo $product['id']; ?>" class="edit-button">Modifier</a>
                                <a href="admin-products.php?delete=<?php echo $product['id']; ?>" class="delete-button" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-products">
                <p>Aucun produit n'a été ajouté. <a href="admin-add-product.php">Ajouter un produit</a></p>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>