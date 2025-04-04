<?php
require_once '../conn.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM ai_responses";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data found']);
}

$conn->close();
?>
