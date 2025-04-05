<?php
include '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $item_name = $_POST['item_name'];
  $points_required = $_POST['points_required'];
  $description = $_POST['description'];
  $max_claims = $_POST['max_claims'];
  $cooldown_hours = $_POST['cooldown_hours'];

  // Handle image upload
  $image_path = '';
  if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/'; // Ensure this path is correct relative to process_redeem.php
    
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

  // Insert into database
  $stmt = $conn->prepare("INSERT INTO redeemable_items (item_name, points_required, description, image_path, max_claims, cooldown_hours) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param('sissii', $item_name, $points_required, $description, $image_path, $max_claims, $cooldown_hours);

  if ($stmt->execute()) {
    header('Location: ad_redeem.php?success=1');
  } else {
    echo 'Error: ' . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>
