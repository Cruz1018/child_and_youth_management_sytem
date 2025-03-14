<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="assets/images/unified-lgu-logo.png">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <title>Add Points</title>
  <link rel="stylesheet" href="css/simplebar.css">
  <link rel="stylesheet" href="css/main.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900&display=swap"
    rel="stylesheet">
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="css/feather.css">
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <link rel="stylesheet" href="css/daterangepicker.css">
</head>

<body class="vertical light">
  <div class="wrapper">
  <?php include 'sections/navbar.php'; ?>
  <?php include 'sections/sidebar.php'; ?>

    <main role="main" class="main-content">
      <div class="content">
        <h2>Manage Points for Users</h2>
        <form action="process_points.php" method="post">
          <div class="form-group">
            <label for="users">Select Users:</label>
            <div id="users" class="form-control" style="height: auto;">
              <?php
              include '../conn.php';
              $result = $conn->query("SELECT id, username FROM user"); // Assuming 'username' is the correct column name
              if ($result) {
                while ($row = $result->fetch_assoc()) {
                  echo "<div><input type='checkbox' name='users[]' value='{$row['id']}'> {$row['username']}</div>";
                }
              } else {
                echo "Error fetching users: " . $conn->error;
              }
              $conn->close();
              ?>
            </div>
          </div>
          <div class="form-group">
            <label for="action">Action:</label>
            <select id="action" name="action" class="form-control" required>
              <option value="add">Add Points</option>
              <option value="deduct">Deduct Points</option>
            </select>
          </div>
          <div class="form-group">
            <label for="points">Points:</label>
            <input type="number" id="points" name="points" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="date_range">Date Range:</label>
            <input type="text" id="date_range" name="date_range" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </main>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="js/main.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/moment.min.js"></script>
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
  <script>
    $(document).ready(function () {
      // Initialize sidebar
      $('.sidebar').simplebar();

      // Initialize Bootstrap dropdowns
      $('.dropdown-toggle').dropdown();

      // Initialize date range picker
      $('#date_range').daterangepicker({
        locale: {
          format: 'YYYY-MM-DD'
        }
      });
    });
  </script>
</body>

</html>
