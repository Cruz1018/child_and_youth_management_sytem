<?php
// Fetch data using curl
function fetchData($url)
{
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($curl);
  curl_close($curl);
  $data = json_decode($response, true);
  return $data['data'] ?? []; // Return the 'data' array or an empty array if not present
}

$data = fetchData('https://backend-api-5m5k.onrender.com/api/cencus');

// Process data for charts
$ageGroups = ['0-18' => 0, '19-35' => 0, '36-60' => 0, '60+' => 0];
$genderRatio = ['Male' => 0, 'Female' => 0];
$employmentStatus = ['Employed' => 0, 'Unemployed' => 0];
$healthStatus = ['Healthy' => 0, 'Unhealthy' => 0];
$fullyImmunized = ['Yes' => 0, 'No' => 0];
$civilStatus = []; // Initialize civilStatus array
$existingHealthCondition = []; // Initialize existingHealthCondition array

foreach ($data as $person) {
    // Safely access keys with null coalescing operator
    $age = $person['age'] ?? null;
    $gender = strtolower(trim($person['gender'] ?? '')); // Normalize gender to lowercase
    $employmentStatusKey = strtolower(trim($person['employmentstatus'] ?? '')); // Normalize employment status
    $healthStatusKey = strtolower(trim($person['healthstatus'] ?? '')); // Normalize health status
    $fullyImmunizedKey = strtolower(trim($person['fullyimmunized'] ?? '')); // Normalize fully immunized status

    // Process age groups
    if ($age !== null) {
        if ($age <= 18) $ageGroups['0-18']++;
        elseif ($age <= 35) $ageGroups['19-35']++;
        elseif ($age <= 60) $ageGroups['36-60']++;
        else $ageGroups['60+']++;
    }

    // Process gender (exclude empty or invalid values)
    if (!empty($gender) && isset($genderRatio[ucfirst($gender)])) {
        $genderRatio[ucfirst($gender)]++;
    }

    // Process employment status (add dynamically if not predefined)
    if (!empty($employmentStatusKey)) {
        $employmentStatus[ucfirst($employmentStatusKey)] = ($employmentStatus[ucfirst($employmentStatusKey)] ?? 0) + 1;
    }

    // Process health status (add dynamically if not predefined)
    if (!empty($healthStatusKey)) {
        $healthStatus[ucfirst($healthStatusKey)] = ($healthStatus[ucfirst($healthStatusKey)] ?? 0) + 1;
    }

    // Process fully immunized status (exclude empty or invalid values)
    if (!empty($fullyImmunizedKey) && isset($fullyImmunized[ucfirst($fullyImmunizedKey)])) {
        $fullyImmunized[ucfirst($fullyImmunizedKey)]++;
    }

    // Process civil status (add dynamically if not predefined)
    $civilStatusKey = strtolower(trim($person['civilstatus'] ?? ''));
    if (!empty($civilStatusKey)) {
        $civilStatus[ucfirst($civilStatusKey)] = ($civilStatus[ucfirst($civilStatusKey)] ?? 0) + 1;
    }

    // Process existing health condition (add dynamically if not predefined)
    $existingHealthConditionKey = strtolower(trim($person['existinghealthcondition'] ?? ''));
    if (!empty($existingHealthConditionKey)) {
        $existingHealthCondition[ucfirst($existingHealthConditionKey)] = ($existingHealthCondition[ucfirst($existingHealthConditionKey)] ?? 0) + 1;
    }
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
  <style>
    body {
      font-family: 'Inter', Arial, sans-serif;
      background-color: #f4f6f9;
      margin: 0;
      padding: 0;
    }

    .container {
      margin-top: 20px;
    }

    .accordion .card {
      border: none;
      border-radius: 8px;
      margin-bottom: 15px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .accordion .card-header {
      background-color: #007bff;
      color: white;
      font-weight: bold;
      padding: 15px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .accordion .card-header:hover {
      background-color: #0056b3;
    }

    .accordion .card-header button {
      color: white;
      text-decoration: none;
      font-size: 16px;
      font-weight: bold;
      background: none;
      border: none;
      outline: none;
      width: 100%;
      text-align: left;
      padding: 0;
    }

    .accordion .card-header i {
      margin-left: 10px;
      transition: transform 0.3s ease;
    }

    .accordion .card-header.collapsed i {
      transform: rotate(-90deg);
    }

    .accordion .card-body {
      background-color: #ffffff;
      padding: 20px;
      border-top: 1px solid #e9ecef;
    }

    .accordion .card-body canvas {
      max-width: 100%;
      height: auto;
    }

    .tooltip {
      position: absolute;
      background-color: #333;
      color: white;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 12px;
      display: none;
      z-index: 1000;
    }

    .zoomed {
      transform: scale(1.2);
      transition: transform 0.3s ease;
    }
  </style>
</head>

<body class="vertical light">
  <div class="wrapper">
    <?php include 'sections/navbar.php'; ?>
    <?php include 'sections/sidebar.php'; ?>

    <main role="main" class="main-content">
      <div class="container">
        <h1 class="text-center mb-4">Detailed Data Report</h1>
        <div class="accordion" id="chartAccordion">
          <div class="card">
            <div class="card-header collapsed" id="headingAge" data-toggle="collapse" data-target="#collapseAge" aria-expanded="true" aria-controls="collapseAge">
              <span>Age Distribution</span>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div id="collapseAge" class="collapse" aria-labelledby="headingAge" data-parent="#chartAccordion">
              <div class="card-body">
                <canvas id="ageDistributionChart"></canvas>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-header collapsed" id="headingGender" data-toggle="collapse" data-target="#collapseGender" aria-expanded="false" aria-controls="collapseGender">
              <span>Gender Ratio</span>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div id="collapseGender" class="collapse" aria-labelledby="headingGender" data-parent="#chartAccordion">
              <div class="card-body">
                <canvas id="genderRatioChart"></canvas>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-header collapsed" id="headingEmployment" data-toggle="collapse" data-target="#collapseEmployment" aria-expanded="false" aria-controls="collapseEmployment">
              <span>Employment Status</span>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div id="collapseEmployment" class="collapse" aria-labelledby="headingEmployment" data-parent="#chartAccordion">
              <div class="card-body">
                <canvas id="employmentStatusChart"></canvas>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-header collapsed" id="headingHealth" data-toggle="collapse" data-target="#collapseHealth" aria-expanded="false" aria-controls="collapseHealth">
              <span>Health Status</span>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div id="collapseHealth" class="collapse" aria-labelledby="headingHealth" data-parent="#chartAccordion">
              <div class="card-body">
                <canvas id="healthStatusChart"></canvas>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-header collapsed" id="headingImmunized" data-toggle="collapse" data-target="#collapseImmunized" aria-expanded="false" aria-controls="collapseImmunized">
              <span>Fully Immunized Status</span>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div id="collapseImmunized" class="collapse" aria-labelledby="headingImmunized" data-parent="#chartAccordion">
              <div class="card-body">
                <canvas id="fullyImmunizedChart"></canvas>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-header collapsed" id="headingCivil" data-toggle="collapse" data-target="#collapseCivil" aria-expanded="false" aria-controls="collapseCivil">
              <span>Civil Status</span>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div id="collapseCivil" class="collapse" aria-labelledby="headingCivil" data-parent="#chartAccordion">
              <div class="card-body">
                <canvas id="civilStatusChart"></canvas>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-header collapsed" id="headingHealthCondition" data-toggle="collapse" data-target="#collapseHealthCondition" aria-expanded="false" aria-controls="collapseHealthCondition">
              <span>Existing Health Condition</span>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div id="collapseHealthCondition" class="collapse" aria-labelledby="headingHealthCondition" data-parent="#chartAccordion">
              <div class="card-body">
                <canvas id="existingHealthConditionChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <<!-- Include jQuery -->
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
    // Prepare data for charts
    const ageGroups = <?php echo json_encode(array_values($ageGroups)); ?>;
    const genderRatio = <?php echo json_encode(array_values($genderRatio)); ?>;
    const employmentStatus = <?php echo json_encode(array_values($employmentStatus)); ?>;
    const healthStatus = <?php echo json_encode(array_values($healthStatus)); ?>;
    const fullyImmunized = <?php echo json_encode(array_values($fullyImmunized)); ?>;
    const civilStatus = <?php echo json_encode(array_values($civilStatus)); ?>;
    const existingHealthCondition = <?php echo json_encode(array_values($existingHealthCondition)); ?>;

    const labels = {
      ageGroups: <?php echo json_encode(array_keys($ageGroups)); ?>,
      genderRatio: <?php echo json_encode(array_keys($genderRatio)); ?>,
      employmentStatus: <?php echo json_encode(array_keys($employmentStatus)); ?>,
      healthStatus: <?php echo json_encode(array_keys($healthStatus)); ?>,
      fullyImmunized: <?php echo json_encode(array_keys($fullyImmunized)); ?>,
      civilStatus: <?php echo json_encode(array_keys($civilStatus)); ?>,
      existingHealthCondition: <?php echo json_encode(array_keys($existingHealthCondition)); ?>
    };

    function createChart(ctx, labels, data, title) {
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
            hoverBackgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40']
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top'
            },
            title: {
              display: true,
              text: title
            }
          }
        }
      });
    }

    // Initialize charts
    createChart(document.getElementById('ageDistributionChart'), labels.ageGroups, ageGroups, 'Age Distribution');
    createChart(document.getElementById('genderRatioChart'), labels.genderRatio, genderRatio, 'Gender Ratio');
    createChart(document.getElementById('employmentStatusChart'), labels.employmentStatus, employmentStatus, 'Employment Status');
    createChart(document.getElementById('healthStatusChart'), labels.healthStatus, healthStatus, 'Health Status');
    createChart(document.getElementById('fullyImmunizedChart'), labels.fullyImmunized, fullyImmunized, 'Fully Immunized Status');
    createChart(document.getElementById('civilStatusChart'), labels.civilStatus, civilStatus, 'Civil Status');
    createChart(document.getElementById('existingHealthConditionChart'), labels.existingHealthCondition, existingHealthCondition, 'Existing Health Condition');

    // Add smooth transitions for icons
    $('.collapse').on('show.bs.collapse', function () {
      $(this).prev('.card-header').removeClass('collapsed').find('i').css('transform', 'rotate(0deg)');
    });

    $('.collapse').on('hide.bs.collapse', function () {
      $(this).prev('.card-header').addClass('collapsed').find('i').css('transform', 'rotate(-90deg)');
    });

    // Add zoom functionality when a tab is clicked
    $('.collapse').on('shown.bs.collapse', function () {
      const canvas = $(this).find('canvas')[0];
      if (canvas) {
        canvas.classList.add('zoomed');
      }
    });

    $('.collapse').on('hidden.bs.collapse', function () {
      const canvas = $(this).find('canvas')[0];
      if (canvas) {
        canvas.classList.remove('zoomed');
      }
    });
  </script>

  
</body>

</html>