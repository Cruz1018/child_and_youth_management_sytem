<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="https://smartbarangayconnect.com/assets/img/logo.jpg">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <title>Home</title>

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

  <!-- Add custom CSS for styling -->
  <style>
    .card {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 20px;
      margin: 10px;
      text-align: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .card h3 {
      margin: 0;
      font-size: 24px;
    }

    .card p {
      margin: 5px 0 0;
      font-size: 18px;
      color: #555;
    }

    .footer {
      text-align: center; /* Center the footer content */
      padding: 10px;
      background: #f8f9fa;
      border-top: 1px solid #ddd;
      margin-top: 20px;
    }

    .welcome-banner {
      background: linear-gradient(90deg, #325b85, #1b2126); /* Darker blue gradient */
      color: white;
      padding: 20px;
      border-radius: 8px;
      text-align: center;
      margin-bottom: 20px;
    }

    .welcome-banner h1 {
      color: #459ed4; /* Updated font color */
    }

    .quick-links {
      display: flex;
      justify-content: space-around;
      margin: 20px 0;
    }

    .quick-link {
      text-align: center;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      width: 150px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s;
    }

    .quick-link:hover {
      transform: scale(1.05);
    }

    .quick-link i {
      font-size: 24px;
      color: #2c3e50; /* Darker blue for icons */
      margin-bottom: 10px;
    }

    .recent-activities ul {
      list-style: none;
      padding: 0;
    }

    .recent-activities li {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .recent-activities li i {
      font-size: 20px;
      color: #2c3e50; /* Darker blue for icons */
      margin-right: 10px;
    }
  </style>
</head>

<body class="vertical  light">
  <div class="wrapper">
    <?php include 'sections/navbar.php'; ?>
    <?php include 'sections/sidebar.php'; ?>

    <main role="main" class="main-content">
      <div class="content">
        <!-- Welcome Banner -->
        <div class="welcome-banner">
          <h1>Welcome to the Admin Center</h1>
          <p>Manage your system efficiently and effectively.</p>
        </div>

        <!-- Quick Links Section -->
        <div class="quick-links">
          <a href="profiling.php" class="quick-link">
            <i class="fas fa-user"></i>
            <p>Profiling</p>
          </a>
          <a href="datareport.php" class="quick-link">
            <i class="fas fa-chart-bar"></i>
            <p>Data Report</p>
          </a>
          <a href="announcement.php" class="quick-link">
            <i class="fas fa-bullhorn"></i>
            <p>Announcement</p>
          </a>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <p>&copy; <?php echo date('Y'); ?> Admin Center. All rights reserved.</p>
    </footer>
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
      // Remove chart initialization script
    });

    // Initialize Bootstrap dropdowns
    $(document).ready(function () {
      $('.dropdown-toggle').dropdown();
    });
  </script>
</body>

</html>
