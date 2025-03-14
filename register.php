<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Include the database connection
include 'conn.php';

// Function to register the user
function registerUser($firstname, $lastname, $email, $username, $password) {
    global $conn;

    // Check if the user exists in the 'cy' (profiling) table
    $stmt = $conn->prepare("SELECT name, lastname FROM cy WHERE name = ? AND lastname = ?");
    if (!$stmt) {
        die("<script>alert('Database error (profiling check): " . $conn->error . "');</script>");
    }
    $stmt->bind_param("ss", $firstname, $lastname);
    $stmt->execute();
    $stmt->store_result();

    // Debugging: Check if the name is found in `cy`
    if ($stmt->num_rows == 0) {
        die("<script>alert('No matching record found in profiling database.');</script>");
    }
    $stmt->close();

    // Check if username or email already exists in the 'user' table
    $stmt = $conn->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
    if (!$stmt) {
        die("<script>alert('Database error (user check): " . $conn->error . "');</script>");
    }
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("<script>alert('Username or Email already exists.');</script>");
    }
    $stmt->close();

    // Hash the password before inserting
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the 'user' table
    $stmt = $conn->prepare("INSERT INTO user (firstname, lastname, email, username, password) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("<script>alert('Database error (insert): " . $conn->error . "');</script>");
    }
    $stmt->bind_param("sssss", $firstname, $lastname, $email, $username, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! You can now log in.'); window.location='login.php';</script>";
    } else {
        die("<script>alert('Error executing query: " . $stmt->error . "');</script>");
    }

    // Close statement
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($firstname) || empty($lastname) || empty($email) || empty($username) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required.');</script>";
    } elseif ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        registerUser($firstname, $lastname, $email, $username, $password);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register.php" method="post">
        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="firstname" required><br><br>

        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastname" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <input type="checkbox" onclick="togglePasswordVisibility('password')"> Show Password<br><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <input type="checkbox" onclick="togglePasswordVisibility('confirm_password')"> Show Password<br><br>

        <button type="submit" name="register">Register</button>
    </form>

    <script>
        function togglePasswordVisibility(fieldId) {
            var field = document.getElementById(fieldId);
            field.type = field.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
