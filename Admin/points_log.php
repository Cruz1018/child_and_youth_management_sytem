<?php
include '../conn.php';

$result = $conn->query("SELECT pl.id, u.username, pl.action, pl.points, pl.reason, pl.created_at 
                        FROM points_log pl 
                        JOIN user u ON pl.user_id = u.id 
                        ORDER BY pl.created_at DESC");

if (!$result) {
    die("Query failed: " . $conn->error);
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
<body class="vertical light">
  <div class="wrapper">
    <?php include 'sections/navbar.php'; ?>
    <?php include 'sections/sidebar.php'; ?>

    <main role="main" class="main-content">
      <div class="container mt-5">
        <h2>Points Log</h2>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Username</th>
              <th>Action</th>
              <th>Points</th>
              <th>Reason</th>
              <th>Created At</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo ucfirst($row['action']); ?></td>
                <td><?php echo $row['points']; ?></td>
                <td><?php echo $row['reason']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
              </tr>
            <?php endwhile; ?>
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
          data: [<?php echo $userCount; ?>, <?php echo $cyCount; ?>, <?php echo $programsCount; ?>]
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
          categories: ['Users', 'CY', 'Programs'],
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
<?php $conn->close(); ?>
