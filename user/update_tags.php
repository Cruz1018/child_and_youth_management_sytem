<?php
include '../conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $tags = $_POST['tags'];

    // Fetch the user's first name and last name from the 'user' table
    $sql = "SELECT firstname, lastname FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Error preparing the SQL statement: ' . $conn->error);
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($userFirstName, $userLastName);
    $stmt->fetch();
    $stmt->close();

    $sql = "UPDATE cy SET tags = ? WHERE name = ? AND lastname = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $tags, $userFirstName, $userLastName);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }

    $stmt->close();
    $conn->close();
}
?>
