<?php
require '../conn.php'; // Include database connection

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $responseId = intval($_POST['response_id'] ?? 0);

    if ($responseId > 0) {
        // Fetch the specific AI response from the database
        $query = "SELECT * FROM ai_responses WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $responseId);
        $stmt->execute();
        $result = $stmt->get_result();
        $responseData = $result->fetch_assoc();

        if ($responseData) {
            // Return the AI response data as JSON
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'user_input' => $responseData['user_input'],
                    'ai_response' => $responseData['ai_response'],
                    'created_at' => $responseData['created_at']
                ]
            ]);
        } else {
            // Response ID not found
            echo json_encode(['status' => 'error', 'message' => 'Response not found.']);
        }
    } else {
        // Invalid response ID
        echo json_encode(['status' => 'error', 'message' => 'Invalid response ID.']);
    }
} else {
    // Invalid request method
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
