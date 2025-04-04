<?php
include '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = intval($_POST['id']);
  $item_name = $_POST['item_name'];
  $points_required = $_POST['points_required'];
  $description = $_POST['description'];

  // Handle image upload
  $image_path = '';
  if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/';
    
    // Ensure the uploads directory exists
    if (!is_dir($upload_dir)) {
      if (!mkdir($upload_dir, 0777, true)) {
        die('Failed to create uploads directory.');
      }
    }

    $image_path = basename($_FILES['item_image']['name']);
    $target_file = $upload_dir . $image_path;

    if (!move_uploaded_file($_FILES['item_image']['tmp_name'], $target_file)) {
      die('Error uploading image.');
    }
  }

  // Update the database
  if ($image_path) {
    // Update with a new image
    $stmt = $conn->prepare("UPDATE redeemable_items SET item_name = ?, points_required = ?, description = ?, image_path = ? WHERE id = ?");
    $stmt->bind_param('sissi', $item_name, $points_required, $description, $image_path, $id);
  } else {
    // Update without changing the image
    $stmt = $conn->prepare("UPDATE redeemable_items SET item_name = ?, points_required = ?, description = ? WHERE id = ?");
    $stmt->bind_param('sisi', $item_name, $points_required, $description, $id);
  }

  if ($stmt->execute()) {
    echo 'success';
  } else {
    echo 'Error: ' . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>
