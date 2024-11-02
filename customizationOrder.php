<?php
session_start(); // Start the session

include 'db_connection.php'; // Database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php'); // Redirect to homepage
    exit();
}

// Fetch customization orders with user names
$query = "
    SELECT co.*, u.username 
    FROM customization_orders co
    JOIN users u ON co.user_id = u.id
    LIMIT 10"; // Adjust LIMIT based on your needs
$result = $db->query($query);

// Begin HTML output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Customization Orders</title>
</head>
<body class="bg-gray-100 p-6">
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-4">Customization Orders</h1>

        <?php
        if ($result->num_rows > 0) {
            echo "<table class='min-w-full bg-white border border-gray-300'>";
            // Table header
            echo "<thead><tr class='bg-gray-200'>";
            echo "<th class='py-2 px-4 border-b'>ID</th>";
            echo "<th class='py-2 px-4 border-b'>Username</th>"; // Changed to Username
            echo "<th class='py-2 px-4 border-b'>Product Name</th>";
            echo "<th class='py-2 px-4 border-b'>Size</th>";
            echo "<th class='py-2 px-4 border-b'>Front Text</th>";
            echo "<th class='py-2 px-4 border-b'>Back Text</th>";
            echo "<th class='py-2 px-4 border-b'>File Path</th>";
            echo "<th class='py-2 px-4 border-b'>Order Date</th>";
            echo "<th class='py-2 px-4 border-b'>Status</th>";
            echo "<th class='py-2 px-4 border-b'>Action</th>"; // New column for action
            echo "</tr></thead>";
            
            // Table body
            echo "<tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr class='hover:bg-gray-100'>";
                echo "<td class='border-b px-4 py-2'>{$row['id']}</td>";
                echo "<td class='border-b px-4 py-2'>" . htmlspecialchars($row['username']) . "</td>"; // Display Username
                echo "<td class='border-b px-4 py-2'>{$row['product_name']}</td>";
                echo "<td class='border-b px-4 py-2'>{$row['size']}</td>";
                echo "<td class='border-b px-4 py-2'>" . htmlspecialchars($row['front_text']) . "</td>";
                echo "<td class='border-b px-4 py-2'>" . htmlspecialchars($row['back_text']) . "</td>";
                echo "<td class='border-b px-4 py-2'>" . htmlspecialchars($row['file_path']) . "</td>";
                echo "<td class='border-b px-4 py-2'>{$row['order_date']}</td>";
                
                // Add status dropdown
                echo "<td class='border-b px-4 py-2'>
                        <form action='updateCustomizationOrderStatus.php' method='POST' class='inline'>
                            <input type='hidden' name='order_id' value='{$row['id']}'>
                            <select name='status' class='border rounded p-1' onchange='this.form.submit()'>";
                
                // Status options
                $statuses = ['Pending', 'Processing', 'Delivery', 'Completed', 'Cancelled'];
                foreach ($statuses as $status) {
                    $selected = ($status === $row['status']) ? 'selected' : '';
                    echo "<option value='$status' $selected>$status</option>";
                }

                echo "      </select>
                        </form>
                      </td>";
                
                // Add a download link
                echo "<td class='border-b px-4 py-2'>
                        <a href='" . htmlspecialchars($row['file_path']) . "' download class='text-blue-500 hover:underline'>Download</a>
                      </td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='text-gray-700'>No customization orders found.</p>";
        }

        $db->close(); // Close the database connection
        ?>
    </div>
</body>
</html>
