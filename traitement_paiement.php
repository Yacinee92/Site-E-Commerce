<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $numero_carte = htmlspecialchars($_POST['numero_carte']);
    $expiration = htmlspecialchars($_POST['expiration']);
    $cvv = htmlspecialchars($_POST['cvv']);
    $total = htmlspecialchars($_POST['total']);

    // Simulation d'une validation de paiement
    if (!empty($nom) && !empty($numero_carte) && !empty($expiration) && !empty($cvv)) {
        // Paiement réussi : on vide le panier
        unset($_SESSION['cart']);
        header("Location: confirmation.php");
        exit;
    } else {
        header("Location: paiement.php?error=1");
        exit;
    }
} else {
    header("Location: catalogue.php");
    exit;
}
?>
