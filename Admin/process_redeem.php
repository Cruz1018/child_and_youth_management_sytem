<?php
include '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'];
    $points_required = $_POST['points_required'];
    $description = $_POST['description']; // Retrieve the description field

    $stmt = $conn->prepare("INSERT INTO redeemable_items (item_name, points_required, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $item_name, $points_required, $description); // Bind the description field

    if ($stmt->execute()) {
        header("Location: ad_redeem.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
