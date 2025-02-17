<?php
session_start(); // Start the session

// Include your database connection file
require 'db_connection.php'; // Adjust the path as needed

// Initialize an error message variable
$error = '';

// Check for success message
$successMessage = isset($_SESSION['message']) ? $_SESSION['message'] : '';

// Unset the message after displaying it
if (isset($_SESSION['message'])) {
    unset($_SESSION['message']);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the email and password from the form submission
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute a query to find the user
    $stmt = $db->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            
            // Redirect based on user role
            if ($_SESSION['user_role'] === 'admin') {
                header('Location: adminDashboard.php');
            } else {
                $redirect_url = $_GET['redirect'] ?? 'index.php';
                header("Location: $redirect_url");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-cover bg-no-repeat min-h-screen flex flex-col items-center justify-center" style="background-image: url('http://www.pixelstalk.net/wp-content/uploads/2016/10/Black-and-Orange-Background-Full-HD.jpg');">
    <nav class="w-full bg-black bg-opacity-70 p-4 fixed top-0 flex justify-between items-center z-50">
        <div class="logo mx-12">
            <img src="images/logo.png" alt="Logo" class="h-10">
        </div>
        <ul class="flex space-x-4">
            <li><a href="#" class="text-white hover:text-orange-500">Home</a></li>
            <li><a href="#" class="text-white hover:text-orange-500">About</a></li>
            <li><a href="#" class="text-white hover:text-orange-500">Contact</a></li>
        </ul>
    </nav>
    <div class="container bg-black bg-opacity-50 backdrop-filter backdrop-blur-lg p-10 rounded-lg shadow-lg text-center mt-32 w-80 sm:w-96">
        <img src="images/logo.png" alt="Logo" class="w-32 mx-auto mb-6">
        <form action="" method="POST" class="flex flex-col space-y-4">
            <div>
                <label for="email" class="block text-left text-white">Your Email Address:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required class="w-full p-2 bg-black text-white rounded">
            </div>
            <div>
                <label for="password" class="block text-left text-white">Your Password:</label>
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="Enter password" required class="w-full p-2 bg-black text-white rounded">
                    <button type="button" class="absolute right-2 top-2 text-white toggle-password" onclick="togglePassword()">Show</button>
                </div>
            </div>
            <button type="submit" class="bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600">LOG IN</button>
        </form>


        <a href="registration.php" class="text-yellow-500 underline mt-4 inline-block hover:no-underline">REGISTER</a>
        <?php if ($successMessage): ?>
            <p class="text-green-500 mt-4"><?php echo $successMessage; ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="text-red-500 mt-4"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById('password');
            var toggleButton = document.querySelector('.toggle-password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = 'Hide';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = 'Show';
            }
        }
    </script>
</body>
</html>
