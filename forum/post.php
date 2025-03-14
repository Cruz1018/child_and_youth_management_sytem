<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../conn.php'; // Include your database connection

if (!isset($_SESSION['user_id'])) {
    die('User not logged in');
}

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// ...existing code for handling post/comment...

// Add 5 points to the user for posting or commenting
$update_query = "UPDATE user_points SET points = points + 5 WHERE user_id = ?";
$update_stmt = $conn->prepare($update_query);
if ($update_stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$update_stmt->bind_param("i", $user_id);
$update_stmt->execute();

// ...existing code...
?>