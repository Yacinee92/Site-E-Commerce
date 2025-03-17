<?php
session_start();
include 'bdd.php';

// Vérification si l'utilisateur est un administrateur (à adapter selon votre système d'authentification)
// if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
//     header('Location: login.php');
//     exit;
// }

$message = '';

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    
    // Validation basique
    if (empty($name) || $price === false || $price <= 0) {
        $message = "Veuillez remplir tous les champs correctement.";
    } else {
        // Gestion de l'upload d'image
        $target_dir = "uploads/";
        
        // Créer le répertoire s'il n'existe pas
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $image_path = '';
        $video_path = '';
        
        // Traitement de l'image
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
            $allowed_types = ["image/jpeg", "image/png", "image/gif"];
            if (in_array($_FILES["image"]["type"], $allowed_types)) {
                $filename = basename($_FILES["image"]["name"]);
                $target_file = $target_dir . time() . "_" . $filename;
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path = $target_file;
                } else {
                    $message = "Erreur lors du téléchargement de l'image.";
                }
            } else {
                $message = "Seuls les fichiers JPG, PNG et GIF sont autorisés pour l'image.";
            }
        }
        
        // Traitement de la vidéo (optionnel)
        if (isset($_FILES["video"]) && $_FILES["video"]["error"] == 0) {
            $allowed_types = ["video/mp4", "video/webm"];
            if (in_array($_FILES["video"]["type"], $allowed_types)) {
                $filename = basename($_FILES["video"]["name"]);
                $target_file = $target_dir . time() . "_" . $filename;
                if (move_uploaded_file($_FILES["video"]["tmp_name"], $target_file)) {
                    $video_path = $target_file;
                } else {
                    $message = "Erreur lors du téléchargement de la vidéo.";
                }
            } else {
                $message = "Seuls les fichiers MP4 et WebM sont autorisés pour la vidéo.";
            }
        }
        
        // Insertion dans la base de données si tout est OK
        if (empty($message)) {
            try {
                $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $pdo->prepare("INSERT INTO products (name, image, video, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $image_path, $video_path, $price]);
                
                $message = "Le produit a été ajouté avec succès.";
                // Redirection ou réinitialisation du formulaire
                header("Location: admin-products.php"); // À adapter selon votre structure
                exit;
            } catch (PDOException $e) {
                $message = "Erreur de base de données : " . htmlspecialchars($e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Produit - iProtect Admin</title>
    <style>
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 800px;
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        
        button {
            background-color: #1b4332;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            margin: 30px auto 0;
            transition: background-color 0.2s;
        }
        
        button:hover {
            background-color: #0f3e26;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 8px;
            text-align: center;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <h1>Ajouter un Nouveau Produit</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'Erreur') !== false ? 'error' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Nom du produit</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="price">Prix (€)</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="image">Image du produit</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>
            
            <div class="form-group">
                <label for="video">Vidéo du produit (optionnel)</label>
                <input type="file" id="video" name="video" accept="video/mp4,video/webm">
            </div>
            
            <button type="submit">Ajouter le Produit</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>