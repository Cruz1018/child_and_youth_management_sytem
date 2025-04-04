<?php
include 'conn.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password confirmation
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Fetch profiling data from the API
        $apiUrl = 'https://backend-api-5m5k.onrender.com/api/cencus'; // Updated API URL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response, true);
        $residentData = $data['data'] ?? [];

        // Check if the user's first name and last name exist in the profiling API
        $userExists = false;
        foreach ($residentData as $resident) {
            if (
                strtolower($resident['firstname'] ?? '') === strtolower($firstname) && // Updated key to match new API
                strtolower($resident['lastname'] ?? '') === strtolower($lastname) // Updated key to match new API
            ) {
                $userExists = true;
                break;
            }
        }

        if ($userExists) {
            // Hash the password for security
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert user into the database
            $stmt = $conn->prepare("INSERT INTO user (firstname, lastname, email, username, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $firstname, $lastname, $email, $username, $hashedPassword);

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful!');</script>";
            } else {
                echo "<script>alert('Error: Could not register user.');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('You are not authorized to register.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('https://media.istockphoto.com/id/2161935288/photo/view-of-city-hall-building-in-manila.jpg?s=612x612&w=0&k=20&c=IU9S2KCJeGv581Wo-kLy1s_owblRW5hKa0A6NMYQtxY=') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            width: 350px;
            text-align: center;
        }
        .container h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }
        .container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .container button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .container button:hover {
            background: #0056b3;
        }
        .container p {
            margin-top: 10px;
            font-size: 14px;
        }
        .container a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }
        .container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="register.php" method="post">
            <input type="text" name="firstname" placeholder="First Name" required>
            <input type="text" name="lastname" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
