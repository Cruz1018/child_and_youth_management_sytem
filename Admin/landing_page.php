<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="assets/images/unified-lgu-logo.png">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <title>Landing Page</title>

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
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="vertical  light">
  <div class="wrapper">
    <?php include '..//Admin/sections/navbar.php'; ?>
    <?php include '../CYMS/Admin/sections/sidebar.php'; ?>

    <main role="main" class="main-content">
      <div class="content">
        <h2>Welcome to the Landing Page</h2>
        <p>This is the landing page content.</p>
        <a href="admin_center.php">Go to Admin Center</a>
        <a href="apoints.php">Add Points to Users</a>

        <?php
        // Include database connection
        include '../conn.php';

        // Fetch data from 'user' table
        $userResult = $conn->query("SELECT COUNT(*) as count FROM user");
        $userCount = $userResult->fetch_assoc()['count'];

        // Fetch data from 'volunteer' table
        $volunteerResult = $conn->query("SELECT COUNT(*) as count FROM volunteer");
        $volunteerCount = $volunteerResult->fetch_assoc()['count'];

        // Fetch data from 'events' table
        $eventsResult = $conn->query("SELECT COUNT(*) as count FROM events");
        $eventsCount = $eventsResult->fetch_assoc()['count'];

        $conn->close();
        ?>

        <div id="chart"></div>
        <table id="data-table" class="table">
          <thead>
            <tr>
              <th>Category</th>
              <th>Count</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Users</td>
              <td><?php echo $userCount; ?></td>
            </tr>
            <tr>
              <td>Volunteers</td>
              <td><?php echo $volunteerCount; ?></td>
            </tr>
            <tr>
              <td>Events</td>
              <td><?php echo $eventsCount; ?></td>
            </tr>
          </tbody>
        </table>
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
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var options = {
        series: [{
          name: 'Count',
          data: [<?php echo $userCount; ?>, <?php echo $volunteerCount; ?>, <?php echo $eventsCount; ?>]
        }],
        chart: {
          type: 'bar',
          height: 350
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: ['Users', 'Volunteers', 'Events'],
        }
      };

      var chart = new ApexCharts(document.querySelector("#chart"), options);
      chart.render();
    });

    // Initialize Bootstrap dropdowns
    $(document).ready(function () {
      $('.dropdown-toggle').dropdown();
    });
  </script>
</body>

</html>
