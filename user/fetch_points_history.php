<?php
session_start();
include '../conn.php'; // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the user's points history
$history_query = "SELECT date, points_change FROM user_points_log WHERE user_id = ? ORDER BY date DESC";
$history_stmt = $conn->prepare($history_query);
if (!$history_stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}
$history_stmt->bind_param("i", $user_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();

$labels = [];
$data = [];
while ($row = $history_result->fetch_assoc()) {
    $labels[] = $row['date'];
    $data[] = $row['points_change'];
}
$history_stmt->close();

// Return the data as JSON
echo json_encode(['labels' => $labels, 'data' => $data]);
?>
