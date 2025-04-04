<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Include the database connection
include 'conn.php';

// Function to log in the user
function loginUser($usernameOrEmail, $password) {
    global $conn;

    // Check database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind the statement
    $stmt = $conn->prepare("SELECT id, firstname, lastname, username, email, password FROM user WHERE username = ? OR email = ?");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Bind the result
        $stmt->bind_result($id, $firstname, $lastname, $username, $email, $hashedPassword);
        $stmt->fetch();

        // Debugging: Check fetched data
        // echo "Fetched Password Hash: " . $hashedPassword;

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            // Set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;

            // Redirect based on user ID
            if ($id == 10) {
                header("Location: Admin/landing_page.php");
            } else {
                header("Location: user/landing_page.php");
            }
            exit();
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('No user found with that username or email.');</script>";
    }

    // Close connections
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usernameOrEmail = trim($_POST['usernameOrEmail']);
    $password = trim($_POST['password']);

    // Check if fields are empty
    if (!empty($usernameOrEmail) && !empty($password)) {
        loginUser($usernameOrEmail, $password);
    } else {
        echo "<script>alert('Please fill in all fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://media.istockphoto.com/id/2161935288/photo/view-of-city-hall-building-in-manila.jpg?s=612x612&w=0&k=20&c=IU9S2KCJeGv581Wo-kLy1s_owblRW5hKa0A6NMYQtxY=') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            width: 350px;
            text-align: center;
        }
        .login-container h2 {
            margin-top: 0;
            color: #333;
            font-size: 24px;
        }
        .login-container label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            text-align: left;
            font-size: 14px;
        }
        .login-container input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .login-container .forgot-password,
        .login-container .register-link {
            margin-top: 10px;
            display: block;
            color: #007BFF;
            text-decoration: none;
            font-size: 14px;
        }
        .login-container .forgot-password:hover,
        .login-container .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <label for="usernameOrEmail">Username or Email:</label>
            <input type="text" id="usernameOrEmail" name="usernameOrEmail" required><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>
            <button type="submit">Login</button>
            <a href="#" class="forgot-password">Forgot Password?</a>
            <a href="register.php" class="register-link">Register here</a>
        </form>
    </div>
</body>
</html>
