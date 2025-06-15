<?php 
session_start();
include 'bdd.php';


date_default_timezone_set('Europe/Paris');

$user_id = 1;

try {
    $stmt = $pdo->prepare("SELECT o.*, p.name FROM orders o
                            JOIN products p ON o.product_id = p.id
                            WHERE o.user_id = ?
                            ORDER BY o.order_date DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
    
// Convertir les dates au bon fuseau horaires
foreach ($orders as &$order) {
    $date = new DateTime($order['order_date'], new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone('Europe/Paris'));
    $order['order_date'] = $date->format('Y-m-d H:i:s');
}

// Récupérer les commandes payées non encore envoyées par email
$paidOrders = array_filter($orders, function($order) {
    return $order['status'] == 1 && $order['email_ordered'] == 0; // Status payé = 1 et email_order = 0
});

// Vérifier s'il y a des commandes à envoyer par email
if (!empty($paidOrders)) {
    $to = "test@example.com";
    $subject = "Votre commande a été validée !";

    // Construire le message HTML
    $message = "<html><head><title>Confirmation de commande</title></head><body>";
    $message .= "<h2>Merci pour votre commande !</h2>";
    $message .= "<table border='1' cellpadding='0' cellspacing='5'>";
    $message .= "<tr><th>Nom</th><th>Prix</th></tr>Date</th></tr>";

    $totalAmount = 0;

    foreach ($paidOrders as $order) {
        $message .= "<tr>";
        $message .= "<td>" . htmlspecialchars($order['name']) . "</td>";
        $message .= "<td>" . number_format($order['total_price'], 2, ',', ' ') . "€</td>";
        $message .= "<td>" . htmlspecialchars($order['order_date']) . "</td>";
        $message .= "</tr>";

        $totalAmount += $order['total_price'];
    }

    // En tête de l'email
    $headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headers = "Reply-To: support@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Envoi de l'email
    if (mail($to, $subject, $message, $headers)) {
        echo "<p style='color: green;'>Email de confirmation envoyé avec succès.</p>";

        // Mettre à jour email_ordered à 1 pour les commandes envoyées
        foreach ($paidOrders as $order) {
            $updateStmt = $pdo->prepare("UPDATE orders SET email_ordered = 1 WHERE id = ?");
            $updateStmt->execute([$order['id']]);
        }
    } else {
        echo "<p style='color: red;'>Erreur lors de l'envoi de l'email.</p>";
    }

}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des commandes</title>
    <link rel="icon" href="images/logo.png" type="image/png">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <h1>Historique des commandes</h1>
    <?php if (empty($orders)): ?>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom du produit</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>

                    <td><?php echo htmlspecialchars($order['name']); ?></td>
                    <td><?php echo number_format($order['total_price'], 2, ',', ' ');?>€</td>
                    <td>
                        <?php
                        switch ($order['status']) {
                            case 1:
                                echo "Payé";
                                break;
                            case 2:
                                echo "Annulé";
                                break;
                            default:
                                echo "En attente";
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <p>Vous n'avez aucune commande payée.</p>
    <?php endif; ?>
    <a href="index.php" class="btn btn-primary">Retourner au catalogue</a>
    <?php include 'footer.php'; ?>


</body>
</html>

