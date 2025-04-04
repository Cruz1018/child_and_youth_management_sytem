<?php
include '../conn.php';
session_start();

// Assuming the user's ID is stored in a session variable
$userId = $_SESSION['user_id'];

// Fetch the user's first name and last name from the 'user' table
$sql = "SELECT firstname, lastname FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Error preparing the SQL statement: ' . $conn->error);
}

$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($userFirstName, $userLastName);
$stmt->fetch();
$stmt->close();

// Fetch tags for the logged-in user
$sql = "SELECT tags FROM user_tags WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($userTags);
$stmt->fetch();
$stmt->close();

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

$residentData = fetchResidentData('https://backend-api-5m5k.onrender.com/api/cencus'); // Updated API URL

// Filter data for the logged-in user
$loggedInUser = [
    'firstname' => $userFirstName,
    'lastname' => $userLastName
];

$userData = array_filter($residentData, function ($item) use ($loggedInUser) {
    return (isset($item['firstname']) && strtolower($item['firstname']) === strtolower($loggedInUser['firstname'])) && // Check if 'firstname' exists
           (isset($item['lastname']) && strtolower($item['lastname']) === strtolower($loggedInUser['lastname'])); // Check if 'lastname' exists
});

// Get the first matching record
$userData = reset($userData);
$userData['tags'] = $userTags ?? 'N/A';
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
        .loader {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }
        .modal-header {
            background-color: #343a40;
            color: #fff;
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
                <p class="mb-4">Welcome to your profiling page! Here, you can edit your tags to add your interests. Let us know what excites you!</p>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="profilingTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Location</th>
                                <th>Contact</th>
                                <th>Tags</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($userData): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($userData['firstname'] . ' ' . $userData['lastname']); ?></td>
                                    <td><?php echo htmlspecialchars($userData['age'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars(($userData['housenumber'] ?? '') . ' ' . ($userData['streetname'] ?? '') . ', ' . ($userData['barangay'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($userData['mobilenumber'] ?? 'N/A'); ?></td>
                                    <td id="userTags"><?php echo htmlspecialchars($userData['tags'] ?? 'N/A'); ?></td>
                                    <td>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editTagsModal">Edit Tags</button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">No data found for the logged-in user.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Modal for editing tags -->
                <div class="modal fade" id="editTagsModal" tabindex="-1" aria-labelledby="editTagsModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editTagsModalLabel">Edit Tags</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editTagsForm">
                                    <div class="mb-3">
                                        <label for="tagsInput" class="form-label">Tags</label>
                                        <input type="text" class="form-control" id="tagsInput" value="<?php echo htmlspecialchars($userData['tags'] ?? ''); ?>">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="saveTagsButton">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loader -->
                <div class="loader">
                    <img src="assets/images/loader.gif" alt="Loading...">
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
        function editTags(currentTags) {
            const newTags = prompt("Tell me what things interest you!", currentTags);
            if (newTags !== null) {
                $.ajax({
                    url: 'update_tags.php',
                    type: 'POST',
                    data: {
                        firstname: '<?php echo $userFirstName; ?>',
                        lastname: '<?php echo $userLastName; ?>',
                        tags: newTags
                    },
                    success: function(response) {
                        alert('Tags updated successfully!');
                        location.reload();
                    },
                    error: function(error) {
                        alert('Error updating tags.');
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const table = document.getElementById('profilingTable');
            searchInput.addEventListener('input', function () {
                const filter = searchInput.value.toLowerCase();
                const rows = table.getElementsByTagName('tr');
                for (let i = 1; i < rows.length; i++) {
                    const cells = rows[i].getElementsByTagName('td');
                    let match = false;
                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].textContent.toLowerCase().includes(filter)) {
                            match = true;
                            break;
                        }
                    }
                    rows[i].style.display = match ? '' : 'none';
                }
            });

            const saveTagsButton = document.getElementById('saveTagsButton');
            const tagsInput = document.getElementById('tagsInput');
            const userTags = document.getElementById('userTags');
            const loader = document.querySelector('.loader');

            saveTagsButton.addEventListener('click', function () {
                const newTags = tagsInput.value.trim();
                if (newTags) {
                    loader.style.display = 'block';
                    $.ajax({
                        url: 'update_tags.php',
                        type: 'POST',
                        data: {
                            firstname: '<?php echo $userFirstName; ?>',
                            lastname: '<?php echo $userLastName; ?>',
                            tags: newTags
                        },
                        success: function (response) {
                            loader.style.display = 'none';
                            userTags.textContent = newTags;
                            alert('Tags updated successfully!');
                            $('#editTagsModal').modal('hide');
                        },
                        error: function (error) {
                            loader.style.display = 'none';
                            alert('Error updating tags.');
                        }
                    });
                } else {
                    alert('Tags cannot be empty.');
                }
            });
        });
    </script>
</body>
</html>
