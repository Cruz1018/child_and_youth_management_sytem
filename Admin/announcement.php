<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="assets/images/unified-lgu-logo.png">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <title>Announcements</title>

  <!-- Simple bar CSS (for scrollbar)-->
  <link rel="stylesheet" href="css/simplebar.css">
  <!-- Fonts CSS -->
  <link
    href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
    rel="stylesheet">
  <!-- Icons CSS -->
  <link rel="stylesheet" href="css/feather.css">
  <!-- App CSS -->
  <link rel="stylesheet" href="css/main.css">
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8f9fa;
    }
    .announcement-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      padding: 20px;
    }
    .announcement {
      background: #ffffff;
      border: 1px solid #dddddd;
      border-top: 5px solid #007bff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      position: relative;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .announcement:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    .announcement h3 {
      margin-top: 0;
      color: #333333;
      font-size: 1.5em;
    }
    .announcement p {
      color: #555555;
      line-height: 1.6;
    }
    .announcement img {
      max-width: 100%;
      border-radius: 10px;
      margin-top: 10px;
      cursor: pointer;
      transition: transform 0.3s ease;
    }
    .announcement img:hover {
      transform: scale(1.05);
    }
    .announcement small {
      display: block;
      margin-top: 10px;
      color: #777777;
    }
    .announcement-actions {
      margin-top: 10px;
      display: flex;
      gap: 10px;
    }
    .announcement-actions button {
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .announcement-actions button.edit-btn {
      background-color: #007bff;
      color: white;
    }
    .announcement-actions button.edit-btn:hover {
      background-color: #0056b3;
    }
    .announcement-actions button.delete-btn {
      background-color: #dc3545;
      color: white;
    }
    .announcement-actions button.delete-btn:hover {
      background-color: #c82333;
    }
    .add-announcement-btn {
      margin-bottom: 20px;
      background-color: #007bff;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .add-announcement-btn:hover {
      background-color: #0056b3;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.4);
      padding-top: 60px;
    }
    .modal-content {
      background-color: #ffffff;
      margin: 5% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 500px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      animation: fadeIn 0.5s;
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    .modal-content h3 {
      margin-top: 0;
      color: #007bff;
    }
    .modal-content form label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }
    .modal-content form input[type="text"],
    .modal-content form textarea,
    .modal-content form input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .modal-content form button {
      margin-top: 20px;
      background-color: #007bff;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .modal-content form button:hover {
      background-color: #0056b3;
    }
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }
    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
    /* Zoom Modal Styles */
    .zoom-modal {
      display: none;
      position: fixed;
      z-index: 2;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.8);
      padding-top: 60px;
    }
    .zoom-modal-content {
      margin: auto;
      display: block;
      width: 80%;
      max-width: 700px;
      animation: zoomIn 0.6s;
    }
    @keyframes zoomIn {
      from { transform: scale(0); }
      to { transform: scale(1); }
    }
    .zoom-modal-content img {
      width: 100%;
      border-radius: 10px;
    }
    .zoom-close {
      position: absolute;
      top: 15px;
      right: 35px;
      color: #fff;
      font-size: 40px;
      font-weight: bold;
      transition: 0.3s;
    }
    .zoom-close:hover,
    .zoom-close:focus {
      color: #bbb;
      text-decoration: none;
      cursor: pointer;
    }
  </style>
</head>

<body class="vertical  light">
  <div class="wrapper">
    <?php include '/CYMS/Admin/sections/navbar.php'; ?>
    <?php include '/CYMS/Admin/sections/sidebar.php'; ?>

    <main role="main" class="main-content">
      <div class="content">
        <h2>Announcements</h2>
        <button class="add-announcement-btn" onclick="openModal()">Add New Announcement</button>
        <div class="announcement-container">
          <?php
          // Include database connection
          include '../conn.php';

          // Fetch announcements from 'announcements' table
          $result = $conn->query("SELECT id, title, description, image_path, created_at FROM announcements ORDER BY created_at DESC");

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo '<div class="announcement">';
              echo '<h3>' . $row['title'] . '</h3>';
              echo '<p>' . $row['description'] . '</p>';
              if (!empty($row['image_path'])) {
                echo '<img src="' . $row['image_path'] . '" alt="Announcement Image" onclick="zoomImage(this)">';
              }
              echo '<small>' . $row['created_at'] . '</small>';
              echo '<div class="announcement-actions">';
              echo '<button class="edit-btn" onclick="editAnnouncement(' . $row['id'] . ')">Edit</button>';
              echo '<button class="delete-btn" onclick="deleteAnnouncement(' . $row['id'] . ')">Delete</button>';
              echo '</div>';
              echo '</div>';
            }
          } else {
            echo '<p>No announcements available.</p>';
          }

          $conn->close();
          ?>
        </div>
      </div>
    </main>
  </div>

  <!-- Add Announcement Modal -->
  <div id="announcementModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h3>Add New Announcement</h3>
      <form action="add_announcement.php" method="post" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <label for="image">Image:</label>
        <input type="file" id="image" name="image">
        <button type="submit">Add Announcement</button>
      </form>
    </div>
  </div>

  <!-- Zoom Image Modal -->
  <div id="zoomModal" class="zoom-modal">
    <span class="zoom-close" onclick="closeZoomModal()">&times;</span>
    <div class="zoom-modal-content">
      <img id="zoomedImage" src="" alt="Zoomed Image">
    </div>
  </div>

  <script>
    function openModal() {
      document.getElementById('announcementModal').style.display = "block";
    }

    function closeModal() {
      document.getElementById('announcementModal').style.display = "none";
    }

    function editAnnouncement(id) {
      // Redirect to edit page with the announcement ID
      window.location.href = 'edit_announcement.php?id=' + id;
    }

    function deleteAnnouncement(id) {
      if (confirm('Are you sure you want to delete this announcement?')) {
        // Redirect to delete page with the announcement ID
        window.location.href = 'delete_announcement.php?id=' + id;
      }
    }

    function zoomImage(img) {
      var modal = document.getElementById('zoomModal');
      var zoomedImage = document.getElementById('zoomedImage');
      zoomedImage.src = img.src;
      modal.style.display = "block";
    }

    function closeZoomModal() {
      document.getElementById('zoomModal').style.display = "none";
    }

    // Close the modal when clicking outside of it
    window.onclick = function(event) {
      var modal = document.getElementById('announcementModal');
      var zoomModal = document.getElementById('zoomModal');
      if (event.target == modal) {
        modal.style.display = "none";
      }
      if (event.target == zoomModal) {
        zoomModal.style.display = "none";
      }
    }
  </script>

  <!-- Include jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="js/jquery.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/moment.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/simplebar.min.js"></script>
  <script src='js/daterangepicker.js'></script>
  <script src='js/jquery.stickOnScroll.js'></script>
  <script src="js/tinycolor-min.js"></script>
  <script src="js/d3.min.js"></script>
  <script src="js/topojson.min.js"></script>
  <script src="js/Chart.min.js"></script>
  <script src="js/gauge.min.js"></script>
  <script src="js/jquery.sparkline.min.js"></script>
  <script src="js/apexcharts.min.js"></script>
  <script src="js/apexcharts.custom.js"></script>
  <script src='js/jquery.mask.min.js'></script>
  <script src='js/select2.min.js'></script>
  <script src='js/jquery.steps.min.js'></script>
  <script src='js/jquery.validate.min.js'></script>
  <script src='js/jquery.timepicker.js'></script>
  <script src='js/dropzone.min.js'></script>
  <script src='js/uppy.min.js'></script>
  <script src='js/quill.min.js'></script>
  <script src="js/apps.js"></script>
  <script src="js/preloader.js"></script>
  <script src="js/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
  <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src='js/jquery.dataTables.min.js'></script>
  <script src='js/dataTables.bootstrap4.min.js'></script>
</body>

</html>
