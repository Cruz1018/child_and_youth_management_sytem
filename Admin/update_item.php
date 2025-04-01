<?php
include '../conn.php';

if (isset($_POST['id'], $_POST['item_name'], $_POST['points_required'], $_POST['description'])) {
  $id = intval($_POST['id']);
  $item_name = $conn->real_escape_string($_POST['item_name']);
  $points_required = intval($_POST['points_required']);
  $description = $conn->real_escape_string($_POST['description']);

  $stmt = $conn->prepare("UPDATE redeemable_items SET item_name = ?, points_required = ?, description = ? WHERE id = ?");
  $stmt->bind_param("sisi", $item_name, $points_required, $description, $id);

  if ($stmt->execute()) {
    echo 'success';
  } else {
    echo 'Error updating item: ' . $conn->error;
  }

  $stmt->close();
} else {
  echo 'Invalid input';
}

$conn->close();
?>
