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

$residentData = fetchResidentData('https://backend-api-5m5k.onrender.com/api/cencus');

// Fetch tags for all users from the database
$userTags = [];
$sql = "SELECT user.firstname, user.lastname, user_tags.tags 
        FROM user 
        LEFT JOIN user_tags ON user.id = user_tags.user_id";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $key = strtolower(trim($row['firstname'] . ' ' . $row['lastname']));
        $userTags[$key] = $row['tags'] ?? 'N/A'; // Ensure 'N/A' is set if tags are null
    }
}

// Limit data to 10 records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10;
$offset = ($page - 1) * $itemsPerPage;
$totalRecords = count($residentData);
$totalPages = ceil($totalRecords / $itemsPerPage);
$limitedData = array_slice($residentData, $offset, $itemsPerPage);
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
    <link rel="stylesheet" href="css/feather.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .content {
            padding: 20px;
        }
        .table thead {
            background-color: #007bff;
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
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .filter-container .search-bar {
            width: 300px;
        }
        .pagination {
            justify-content: center;
        }
    </style>
</head>
<body class="vertical light">
    <div class="wrapper">
        <?php include 'sections/navbar.php'; ?>
        <?php include 'sections/sidebar.php'; ?>
        <main role="main" class="main-content">
            <div class="content">
                <h1 class="mt-4">Resident Profiling</h1>
                <div class="filter-container">
                    <input type="text" id="searchInput" class="form-control search-bar" placeholder="Search residents...">
                    <button class="btn btn-primary" onclick="location.reload();">Refresh Data</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="profilingTable">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Middle Name</th>
                                <th>Date of Birth</th>
                                <th>Age</th>
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
                                $key = strtolower(trim(($item['firstname'] ?? '') . ' ' . ($item['lastname'] ?? ''))); // Ensure consistent casing
                                $tags = $userTags[$key] ?? 'N/A'; // Fetch tags or default to 'N/A'
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['firstname'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['lastname'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['middlename'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['birthday'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['age'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['gender'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['civilstatus'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['occupation'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['mobilenumber'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars(($item['housenumber'] ?? '') . ' ' . ($item['streetname'] ?? '') . ', ' . ($item['barangay'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($item['province'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($tags); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination controls -->
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#profilingTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "pageLength": 10
            });

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
