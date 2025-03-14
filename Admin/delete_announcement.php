<?php
include '../conn.php';

$id = $_GET['id'];

$sql = "DELETE FROM announcements WHERE id=$id";

if ($conn->query($sql) === TRUE) {
  echo "Announcement deleted successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
header('Location: announcement.php');
?>
