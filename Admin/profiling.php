<?php
include '../conn.php';

// Fetch profiling data from the resident API
function fetchResidentData($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);
    return $data['data'] ?? []; // Return the 'data' array or an empty array if not present
}

$residentData = fetchResidentData('https://backend-api-5m5k.onrender.com/api/resident');

// Fetch tags for all users from the database
$userTags = [];
$sql = "SELECT user.firstname, user.lastname, user_tags.tags FROM user LEFT JOIN user_tags ON user.id = user_tags.user_id";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $key = strtolower(trim($row['firstname'] . ' ' . $row['lastname']));
        $userTags[$key] = $row['tags'] ?? 'N/A';
    }
}

// Limit data to 100 records
$limitedData = array_slice($residentData, 0, 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profiling</title>
    <link rel="icon" href="assets/images/unified-lgu-logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="css/simplebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/feather.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="path/to/bootstrap.css"> <!-- Add Bootstrap CSS file path here -->
    <style>
        .table thead {
            background-color: #343a40;
            color: #fff;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .filter-container {
            display: flex;
            justify-content: flex-start; /* Change to flex-start to align items to the left */
            align-items: center;
        }
        .pagination {
            justify-content: flex-start; /* Align pagination to the left */
        }
        .table-responsive {
            overflow-x: hidden; /* Remove horizontal scroll */
        }
    </style>
</head>
<body class="vertical light">
    <div class="wrapper">
        <?php include 'sections/navbar.php'; ?>
        <?php include 'sections/sidebar.php'; ?>
        <main role="main" class="main-content">
            <div class="content">
                <h1 class="mt-4">Profiling</h1>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="profilingTable">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Middle Name</th>
                                <th>Date of Birth</th>
                                <th>Gender</th>
                                <th>Civil Status</th>
                                <th>Nationality</th>
                                <th>Mobile Number</th>
                                <th>Address</th>
                                <th>Province</th>
                                <th>Tags</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($limitedData as $item): ?>
                                <?php
                                // Match user based on firstname and lastname
                                $key = strtolower(trim(($item['firstName'] ?? '') . ' ' . ($item['lastName'] ?? '')));
                                $tags = $userTags[$key] ?? 'N/A';
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['firstName'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['lastName'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['middlename'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['dateofbirth'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['gender'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['civilstatus'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['nationality'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['mobilenumber'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars(($item['address'] ?? '') . ' ' . ($item['streetname'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($item['province'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($tags); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
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
        $(document).ready(function() {
            // Initialize DataTables with pagination of 20 records per page
            $('#profilingTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "pageLength": 20
            });

            // Add search functionality
            $("#searchInput").on("keyup", function() {
                var searchValue = $(this).val().toLowerCase();
                $("#profilingTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
                });
            });
        });
    </script>
</body>
</html>
