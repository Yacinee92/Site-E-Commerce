<?php
session_start();

if (!isset($_POST['total'])) {
    header("Location: catalogue.php");
    exit;
}

$total = $_POST['total'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100 font-sans min-h-screen pt-16">
    
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Barre de progression -->
        <div class="max-w-2xl mx-auto mb-8">
            <div class="flex justify-between items-center">
                <div class="flex-1 text-center">
                    <div class="w-10 h-10 mx-auto bg-gray-300 rounded-full flex items-center justify-center text-white">1</div>
                    <p class="mt-2 text-sm font-medium text-gray-600">Panier</p>
                </div>
                <div class="flex-1">
                    <div class="h-1 bg-gray-300 mt-5"></div>
                </div>
                <div class="flex-1 text-center">
                    <div class="w-10 h-10 mx-auto bg-blue-600 rounded-full flex items-center justify-center text-white">2</div>
                    <p class="mt-2 text-sm font-medium text-blue-600">Paiement</p>
                </div>
                <div class="flex-1">
                    <div class="h-1 bg-gray-300 mt-5"></div>
                </div>
                <div class="flex-1 text-center">
                    <div class="w-10 h-10 mx-auto bg-gray-300 rounded-full flex items-center justify-center text-white">3</div>
                    <p class="mt-2 text-sm font-medium text-gray-600">Confirmation</p>
                </div>
            </div>
        </div>

        <form action="traitement_paiement.php" method="post" class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-lg">
            <input type="hidden" name="total" value="<?php echo htmlspecialchars($total); ?>">

            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-800">Paiement</h1>
                    <div class="flex gap-3 ml-4">
                        <i class="ri-visa-line text-3xl text-[#1A1F71]"></i>
                        <i class="ri-mastercard-line text-3xl text-[#EB7600]"></i>
                        <i class="ri-bank-card-fill text-3xl text-[#006C17]"></i>
                    </div>
                </div>
                <p class="text-lg font-semibold text-gray-700">Total : €<span id="totalPayer"><?php echo htmlspecialchars($total); ?></span></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Livraison -->
                <div>
                    <label for="livraison" class="block text-sm font-medium text-gray-700 mb-1">Type de livraison</label>
                    <select id="livraison" name="livraison" required onchange="updateLivraisonDetails()" 
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                        <option value="standard">Livraison Standard (3.99€)</option>
                        <option value="acceleree">Livraison Accélérée (7€)</option>
                        <option value="retrait">Point de retrait (Gratuit)</option>
                    </select>
                </div>

                <!-- Détails livraison -->
                <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-md">
                    <p class="text-sm"><strong>Prix de la livraison : </strong><span id="prixLivraison">3.99€</span></p>
                    <p class="text-sm"><strong>Estimation : </strong><span id="delaiLivraison">6 à 7 jours</span></p>
                </div>

                <!-- Champs formulaire -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" id="nom" name="nom" required oninput="validateName(event)" 
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <input type="text" id="adresse" name="adresse" required 
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="ville" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                    <input type="text" id="ville" name="ville" required oninput="validateName(event)" 
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="code_postal" class="block text-sm font-medium text-gray-700 mb-1">Code Postal</label>
                    <input type="text" id="code_postal" name="code_postal" required maxlength="5"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5);"
                        pattern="\d{5}" title="Veuillez entrer 5 chiffres"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="departement" class="block text-sm font-medium text-gray-700 mb-1">Département</label>
                    <input type="text" id="departement" name="departement" required 
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="numero_carte" class="block text-sm font-medium text-gray-700 mb-1">Numéro de carte</label>
                    <input type="text" id="numero_carte" name="numero_carte" required 
                        placeholder="1234-5678-9012-3456" oninput="formatCardNumber(event)"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="expiration" class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                    <input type="month" id="expiration" name="expiration" required 
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="cvv" class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                    <input type="text" id="cvv" name="cvv" required placeholder="123" maxlength="3"
                        oninput="validateCVV(event)"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <button type="submit" 
                class="w-full mt-6 bg-blue-600 text-white py-3 rounded-full hover:bg-blue-700 transition duration-300">
                Valider le paiement
            </button>
        </form>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        function validateName(event) {
            event.target.value = event.target.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
        }

        function formatCardNumber(event) {
            let value = event.target.value.replace(/\D/g, '');
            if (value.length > 16) value = value.slice(0, 16);
            const groups = value.match(/.{1,4}/g);
            event.target.value = groups ? groups.join('-') : value;
        }

        function validateCVV(event) {
            event.target.value = event.target.value.replace(/[^0-9]/g, '').slice(0, 3);
        }

        function updateLivraisonDetails() {
            const livraison = document.getElementById("livraison").value;
            const prixLivraison = document.getElementById("prixLivraison");
            const delaiLivraison = document.getElementById("delaiLivraison");
            const totalPayer = document.getElementById("totalPayer");

            let livraisonPrix = 0;
            if (livraison === "standard") {
                livraisonPrix = 3.99;
                delaiLivraison.textContent = "6 à 7 jours";
            } else if (livraison === "acceleree") {
                livraisonPrix = 7;
                delaiLivraison.textContent = "1 à 2 jours";
            } else if (livraison === "retrait") {
                livraisonPrix = 0;
                delaiLivraison.textContent = "3 à 4 jours";
            }

            prixLivraison.textContent = `${livraisonPrix}€`;
            const totalAvecLivraison = parseFloat("<?php echo $total; ?>") + livraisonPrix;
            totalPayer.textContent = totalAvecLivraison.toFixed(2);
        }
    </script>
</body>
</html>