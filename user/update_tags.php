<?php
header('Content-Type: application/json');
ini_set('display_errors', 0); // Suppress warnings and notices in the output
error_reporting(E_ALL);

include '../conn.php';
session_start();

$response = ['success' => false, 'message' => 'Unknown error occurred'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_POST['user_id'] ?? null;
        $tags = $_POST['tags'] ?? null;

        if (!$userId || !$tags) {
            throw new Exception('Invalid input data.');
        }

        $sql = "UPDATE user_tags SET tags = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            throw new Exception('Error preparing the SQL statement: ' . $conn->error);
        }

        $stmt->bind_param('si', $tags, $userId);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Tags updated successfully.';
        } else {
            throw new Exception('Error executing the SQL statement: ' . $stmt->error);
        }

        $stmt->close();
    } else {
        throw new Exception('Invalid request method.');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
