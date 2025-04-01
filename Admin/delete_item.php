<?php
include '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = intval($data['id'] ?? 0);

    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM redeemable_items WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'Error: ' . $conn->error;
            error_log('Database error: ' . $conn->error);
        }

        $stmt->close();
    } else {
        echo 'Invalid item ID.';
        error_log('Invalid ID received: ' . $id);
    }

    $conn->close();
} else {
    echo 'Invalid request.';
    error_log('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
}
