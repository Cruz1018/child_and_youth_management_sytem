<?php
session_start();
include '../conn.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    die("Access denied. Please log in.");
}

$username = $_SESSION['username'];

// Fetch user data for the logged-in user
$sql = "SELECT user.id, user.firstname, user.lastname 
        FROM user 
        WHERE user.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if (!$userData) {
    die("No data found for the logged-in user.");
}

// Fetch the most recent tags for the logged-in user
$tagsSql = "SELECT tags FROM user_tags WHERE user_id = ? ORDER BY id DESC LIMIT 1";
$tagsStmt = $conn->prepare($tagsSql);
$tagsStmt->bind_param("i", $userData['id']);
$tagsStmt->execute();
$tagsResult = $tagsStmt->get_result();
$userTags = [];
if ($tagsRow = $tagsResult->fetch_assoc()) {
    $tagsArray = explode(',', $tagsRow['tags']); // Split tags by comma
    foreach ($tagsArray as $tag) {
        $userTags[] = trim($tag); // Trim whitespace
    }
}
$userData['tags'] = implode(', ', $userTags); // Combine tags into a comma-separated string

// Handle tag update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tags'])) {
    $tags = trim($_POST['tags']);
    $userId = $userData['id'];

    // Normalize and deduplicate tags
    $tagsArray = explode(',', $tags);
    $uniqueTags = array_unique(array_map('trim', $tagsArray));
    $normalizedTags = implode(', ', $uniqueTags);

    // Update or insert tags as a single entry
    $updateSql = "INSERT INTO user_tags (user_id, tags) VALUES (?, ?) 
                  ON DUPLICATE KEY UPDATE tags = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("iss", $userId, $normalizedTags, $normalizedTags);
    if ($updateStmt->execute()) {
        $userData['tags'] = $normalizedTags; // Update the displayed tags
        $message = "Tags updated successfully.";
    } else {
        $message = "Failed to update tags.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profiling</title>
    <link rel="icon" href="https://smartbarangayconnect.com/assets/img/logo.jpg">
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
    </style>
</head>
<body class="vertical light">
    <div class="wrapper">
        <?php include 'sections/navbar.php'; ?>
        <?php include 'sections/sidebar.php'; ?>
        <main role="main" class="main-content">
            <div class="content">
                <h1 class="mt-4">User Profiling</h1>
                <p>Welcome, <?php echo htmlspecialchars($userData['firstname'] . ' ' . $userData['lastname']); ?>!</p>
                <?php if (isset($message)): ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <div class="filter-container">
                    <button class="btn btn-primary" onclick="location.reload();">Refresh Data</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Tags</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo htmlspecialchars($userData['firstname']); ?></td>
                                <td><?php echo htmlspecialchars($userData['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($userData['tags'] ?? ''); ?></td>
                                <td>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editTagsModal">Edit Tags</button>
                                </td>
                            </tr>
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
                            <form method="POST">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="tagsInput" class="form-label">Tags</label>
                                        <input type="text" name="tags" id="tagsInput" class="form-control" value="<?php echo htmlspecialchars($userData['tags'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
