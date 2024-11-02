<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch products from the database, including the stock column
$query = "SELECT id, name, description, price, image_url, stock FROM products";
$result = $db->query($query);

// Price ranges
$priceRanges = [
    '0' => [0, 500],
    '500' => [500, 1000],
    '1000' => [1000, 1500],
    '1500' => [1500, 2000],
    '2000' => [2000, PHP_INT_MAX],
];

// Initialize filter variables
$searchTerm = $_GET['search'] ?? '';
$minPrice = $_GET['minPrice'] ?? 0;
$maxPrice = PHP_INT_MAX;

// Check if a minPrice was selected and set the maxPrice accordingly
if (isset($_GET['minPrice']) && array_key_exists($_GET['minPrice'], $priceRanges)) {
    $minPrice = $_GET['minPrice'];
    $maxPrice = $priceRanges[$minPrice][1];
}

// Filter products based on search and price range
$filteredProducts = [];
while ($product = $result->fetch_assoc()) {
    if (
        (stripos($product['name'], $searchTerm) !== false) &&
        ($product['price'] >= $minPrice && $product['price'] <= $maxPrice)
    ) {
        $filteredProducts[] = $product;
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kween P Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="images/headlogo.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <style>
        .sold-out {
            position: relative;
            opacity: 0.5; /* Grayed out effect */
        }
        .sold-out::before {
            content: 'SOLD OUT';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 2rem;
            color: red;
            opacity: 0.7;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <nav class="bg-black shadow-md top-0 left-0 w-full z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-2">
                <div class="flex-1 flex justify-start">
                    <div class="hidden md:flex space-x-4 p-2">
                        <a href="index.php" class="text-white tracking-wider px-4 xl:px-8 py-2 text-lg hover:underline">Home</a>
                        <a href="#about" class="text-white tracking-wider px-4 xl:px-8 py-2 text-lg hover:underline">About</a>
                        <a href="#threats" class="text-white tracking-wider px-4 xl:px-8 py-2 text-lg hover:underline">Services</a>
                    </div>
                </div>
                <div class="flex-1 flex justify-center">
                    <div class="text-center">
                        <img src="images/logo1.png" alt="" width="200px" class="h-20">
                    </div>
                </div>
                <div class="flex-1 flex justify-end">
                    <div class="hidden md:flex space-x-4 p-2">
                        <a href="contacts.html" class="text-white tracking-wider px-4 xl:px-8 py-2 text-lg hover:underline">Contacts</a>
                        <a href="order_history.php" class="text-gray-700 px-2 py-1 font-abhaya-libre uppercase text-white tracking-wider px-4 xl:px-8 py-2 text-sm hover:underline">Order History</a>
                        <a href="logout.php"><button type="submit" class="block font-bold bg-orange-400 text-white py-2 px-6 rounded hover:bg-orange-300 transition">Logout</button></a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <h1 class="text-2xl text-center mt-5 mb-5 font-bold text-orange-700">READYMADE JERSEYS</h1>
<!-- price ranges -->
    <div class="flex justify-center mb-5">
    <form method="GET" class="flex space-x-4">
        <input type="text" name="search" placeholder="Search..." class="border p-2 rounded" value="<?php echo htmlspecialchars($searchTerm); ?>">
        <div class="flex space-x-2">
            <button type="submit" name="minPrice" value="0" class="bg-blue-500 text-white px-4 py-2 rounded">Up to ₱500</button>
            <button type="submit" name="minPrice" value="500" class="bg-blue-500 text-white px-4 py-2 rounded">₱500 to ₱1000</button>
            <button type="submit" name="minPrice" value="1000" class="bg-blue-500 text-white px-4 py-2 rounded">₱1000 to ₱1500</button>
            <button type="submit" name="minPrice" value="1500" class="bg-blue-500 text-white px-4 py-2 rounded">₱1500 to ₱2000</button>
            <button type="submit" name="minPrice" value="2000" class="bg-blue-500 text-white px-4 py-2 rounded">Above ₱2000</button>
        </div>
        
        <input type="hidden" name="maxPrice" value="<?php echo htmlspecialchars($maxPrice); ?>">
    </form>
</div>

    

    <div class="flex flex-wrap justify-start">
        <?php foreach ($filteredProducts as $product): ?>
            <div class="w-full md:w-1/2 lg:w-1/4 p-4">
                <div class="border p-4 bg-white rounded-lg shadow-lg <?php echo $product['stock'] === 0 ? 'sold-out' : ''; ?>">
                    <h5 class="w-full h-10 object-cover rounded-t-lg font-bold text-center uppercase"><?php echo htmlspecialchars($product['name']); ?></h5>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image" class="w-full h-48 object-cover rounded-t-lg">
                    <p class="text-sm font-bold text-center text-gray-700"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="text-xl font-bold text-center text-gray-700">₱ <?php echo htmlspecialchars($product['price']); ?></p>
                    <div class="flex justify-center mt-2">
                        <?php if ($product['stock'] > 0): ?>
                            <a href="orderForm.php?product_id=<?php echo $product['id']; ?>&action=buy">
                                <button class="bg-yellow-500 text-white px-4 py-2 rounded-lg">BUY NOW</button>
                            </a>
                        <?php else: ?>
                            <button class="bg-gray-500 text-white px-4 py-2 rounded-lg" disabled>SOLD OUT</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <footer class="bg-black text-white p-8">
        <div class="container mx-auto">
            <div class="flex justify-between">
                <div>
                    <h2 class="text-lg font-bold">Services</h2>
                    <ul>
                        <li><a href="#" class="hover:underline">Web Development</a></li>
                        <li><a href="#" class="hover:underline">Graphic Design</a></li>
                        <li><a href="#" class="hover:underline">SEO Services</a></li>
                    </ul>
                </div>
                <div>
                    <h2 class="text-lg font-bold">Contact</h2>
                    <p>Email: <a href="mailto:info@example.com" class="hover:underline">info@example.com</a></p>
                </div>
                <div>
                    <h2 class="text-lg font-bold">Follow Us</h2>
                    <div class="flex space-x-4">
                        <a href="https://facebook.com" target="_blank" class="hover:text-blue-600">Facebook</a>
                        <a href="https://instagram.com" target="_blank" class="hover:text-purple-600">Instagram</a>
                        <a href="https://twitter.com" target="_blank" class="hover:text-blue-400">Twitter</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
