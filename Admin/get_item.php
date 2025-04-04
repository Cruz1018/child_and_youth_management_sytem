<?php
include '../conn.php';

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $stmt = $conn->prepare("SELECT id, item_name, points_required, description, image_path FROM redeemable_items WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
  } else {
    echo json_encode(['error' => 'Item not found']);
  }

  $stmt->close();
} else {
  echo json_encode(['error' => 'Invalid request']);
}

$conn->close();
?>
