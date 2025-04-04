<?php
include '../conn.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $tags = $_POST['tags'] ?? '';

    if (!$userId || !$tags) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit;
    }

    $sql = "UPDATE user_tags SET tags = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error preparing the SQL statement: ' . $conn->error]); // Debugging: Log SQL error
        exit;
    }

    $stmt->bind_param('si', $tags, $userId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update tags: ' . $stmt->error]); // Debugging: Log execution error
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
