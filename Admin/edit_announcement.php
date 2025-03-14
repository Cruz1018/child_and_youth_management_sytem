<?php
include '../conn.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $image_path = $_POST['existing_image'];

  if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $image_path = 'uploads/' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
  }

  $sql = "UPDATE announcements SET title='$title', description='$description', image_path='$image_path' WHERE id=$id";

  if ($conn->query($sql) === TRUE) {
    echo "Announcement updated successfully";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $conn->close();
  header('Location: announcement.php');
} else {
  $result = $conn->query("SELECT title, description, image_path FROM announcements WHERE id=$id");
  $announcement = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Announcement</title>
</head>
<body>
  <h2>Edit Announcement</h2>
  <form action="edit_announcement.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?php echo $announcement['title']; ?>" required>
    <label for="description">Description:</label>
    <textarea id="description" name="description" required><?php echo $announcement['description']; ?></textarea>
    <label for="image">Image:</label>
    <input type="file" id="image" name="image">
    <input type="hidden" name="existing_image" value="<?php echo $announcement['image_path']; ?>">
    <button type="submit">Update Announcement</button>
  </form>
</body>
</html>
