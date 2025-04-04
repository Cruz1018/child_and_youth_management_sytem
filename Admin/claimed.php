<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="https://smartbarangayconnect.com/assets/img/logo.jpg">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <title>Claimed Items</title>

  <!-- Simple bar CSS (for scrollbar)-->
  <link rel="stylesheet" href="css/simplebar.css">
  <!-- Fonts CSS -->
  <link
    href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900&display=swap"
    rel="stylesheet">
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
    rel="stylesheet">
  <!-- Icons CSS -->
  <link rel="stylesheet" href="css/feather.css">
  <!-- App CSS -->
  <link rel="stylesheet" href="css/main.css">
</head>

<body class="vertical light">
  <div class="wrapper">
    <?php include 'sections/navbar.php'; ?>
    <?php include 'sections/sidebar.php'; ?>

    <main role="main" class="main-content">
      <div class="content">
        <h2>Claimed Items</h2>

        <form method="GET" action="">
          <div class="form-group">
            <label for="search">Search by Stub Number or User Name:</label>
            <input type="text" name="search" id="search" class="form-control" placeholder="Enter stub number or user name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
          </div>
          <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <?php
        // Include database connection
        include '../conn.php';

        // Initialize search query
        $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

        // Fetch data from 'claimed_items' table with user and item names
        $query = "
          SELECT 
            claimed_items.id, 
            user.username AS user_name, 
            redeemable_items.item_name AS item_name, 
            claimed_items.stub_number, 
            claimed_items.claimed_at 
          FROM claimed_items
          INNER JOIN user ON claimed_items.user_id = user.id
          INNER JOIN redeemable_items ON claimed_items.item_id = redeemable_items.id
        ";

        // Add search condition if search term is provided
        if (!empty($search)) {
          $query .= " WHERE claimed_items.stub_number LIKE '%$search%' OR user.username LIKE '%$search%'";
        }

        $result = $conn->query($query);

        if ($result) {
          if ($result->num_rows > 0) {
            echo '<table class="table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>User Name</th>';
            echo '<th>Item Name</th>';
            echo '<th>Stub Number</th>';
            echo '<th>Claimed At</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            while ($row = $result->fetch_assoc()) {
              echo '<tr>';
              echo '<td>' . $row['user_name'] . '</td>';
              echo '<td>' . $row['item_name'] . '</td>';
              echo '<td>' . $row['stub_number'] . '</td>';
              echo '<td>' . $row['claimed_at'] . '</td>';
              echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
          } else {
            echo '<p>No claimed items found.</p>';
          }
        } else {
          echo '<p>Error: ' . $conn->error . '</p>';
        }

        $conn->close();
        ?>
      </div>
    </main>
  </div>

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
