<?php
include '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $tags = $_POST['tags'];

    // Fetch the user ID based on the first name and last name
    $sql = "SELECT id FROM user WHERE firstname = ? AND lastname = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $firstname, $lastname);
    $stmt->execute();
    $stmt->bind_result($userId);
    $stmt->fetch();
    $stmt->close();

    if ($userId) {
        // Check if the user already has tags
        $sql = "SELECT id FROM user_tags WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Update existing tags
            $sql = "UPDATE user_tags SET tags = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $tags, $userId);
        } else {
            // Insert new tags
            $sql = "INSERT INTO user_tags (user_id, tags) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('is', $userId, $tags);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Tags updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update tags.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
