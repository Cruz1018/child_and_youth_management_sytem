<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="https://smartbarangayconnect.com/assets/img/logo.jpg">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <title>Manage Points</title>

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
      <div class="content container">
        <h2 class="mb-4">Manage Points for Users</h2>

        <?php if (isset($_GET['status'])): ?>
          <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php
            if ($_GET['status'] === 'success') {
              echo "Points have been successfully updated!";
            } else {
              echo "An error occurred while updating points. Please try again.";
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <form action="process_points.php" method="post" class="needs-validation" novalidate>
          <div class="form-group mb-3">
            <label for="users" class="form-label">Select Users:</label>
            <select id="users" name="users[]" class="form-control select2" multiple="multiple" required style="
    padding-bottom: 100px;
">
              <?php
              include '../conn.php';
              $result = $conn->query("SELECT id, username FROM user");
              if ($result) {
                while ($row = $result->fetch_assoc()) {
                  echo "<option value='{$row['id']}'>{$row['username']}</option>";
                }
              } else {
                echo "<option disabled>Error fetching users: " . $conn->error . "</option>";
              }
              $conn->close();
              ?>
            </select>
            <div class="invalid-feedback">Please select at least one user.</div>
          </div>
          <div class="form-group mb-3">
            <label for="action" class="form-label">Action:</label>
            <select id="action" name="action" class="form-control" required>
              <option value="add">Add Points</option>
              <option value="deduct">Deduct Points</option>
            </select>
            <div class="invalid-feedback">Please select an action.</div>
          </div>
          <div class="form-group mb-3">
            <label for="points" class="form-label">Points:</label>
            <input type="number" id="points" name="points" class="form-control" required>
            <div class="invalid-feedback">Please enter a valid number of points.</div>
          </div>
          <div class="form-group mb-3">
            <label for="reason" class="form-label">Reason:</label>
            <textarea id="reason" name="reason" class="form-control" rows="3" required></textarea>
            <div class="invalid-feedback">Please provide a reason.</div>
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </main>
  </div>

  <!-- Include jQuery and Bootstrap -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  <script src="js/daterangepicker.js"></script>
  <script src="js/jquery.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/moment.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/simplebar.min.js"></script>
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
    document.addEventListener('DOMContentLoaded', function() {
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
    $(document).ready(function() {
      $('.dropdown-toggle').dropdown();
    });

    // Initialize Select2 for user selection with enhanced styling
    $('#users').select2({
      placeholder: "Select users",
      allowClear: true,
      width: '100%', // Ensure dropdown fits the container
      minimumInputLength: 1, // Enable search after typing at least 1 character
      dropdownCssClass: 'custom-select2-dropdown' // Add custom class for styling
    });

    // Remove date range picker initialization
    // $('#date_range').daterangepicker({
    //   locale: { format: 'YYYY-MM-DD' }
    // });

    // Bootstrap form validation
    (function() {
      'use strict';
      var forms = document.querySelectorAll('.needs-validation');
      Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      })();
    });
  </script>
  <style>
    /* Custom styling for Select2 dropdown */
    .custom-select2-dropdown .select2-results__options {
      max-height: 300px;
      /* Increase dropdown height */
      overflow-y: auto;
    }

    .select2-container {
      width: 100% !important;
      /* Ensure full width */
      max-width: 900px;
      /* Set a larger maximum width */
      margin: 20px auto;
      /* Center the container with some margin */
    }

    .select2-container--default .select2-selection--multiple {
      min-height: 500px;
      /* Further increased height */
      border: 3px solid #0d6efd;
      /* Thicker border */
      border-radius: 12px;
      /* Larger border radius */
      background-color: #eaf3ff;
      /* Softer background */
      padding: 15px;
      /* Increased padding */
      font-size: 18px;
      /* Larger font size */
      box-shadow: 0 0 10px rgba(13, 110, 253, 0.3);
      /* More prominent shadow */
      transition: all 0.3s ease-in-out;
    }

    /* Hover/focus effect */
    .select2-container--default .select2-selection--multiple:focus,
    .select2-container--default .select2-selection--multiple:hover {
      border-color: #084298;
      background-color: #d9eaff;
      box-shadow: 0 0 12px rgba(13, 110, 253, 0.5);
    }

    /* Style for selected items (tags) */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
      background-color: #084298;
      color: #fff;
      border: none;
      border-radius: 25px;
      padding: 8px 15px;
      margin: 8px 8px 0 0;
      font-size: 16px;
      font-weight: 600;
    }

    /* Style the dropdown options */
    .select2-container--default .select2-results__option {
      padding: 10px;
      font-size: 16px;
      transition: background 0.2s ease;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
      background-color: #0d6efd;
      color: white;
    }

    /* Custom scrollable dropdown styling */
    .custom-select2-dropdown .select2-results__options {
      max-height: 300px;
      overflow-y: auto;
    }
  </style>
</body>

</html>