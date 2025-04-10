<?php
require '../conn.php'; // Include database connection

// Fetch AI response logs from the database
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'desc';
$page = $_GET['page'] ?? 1;
$limit = 10; // Number of records per page
$offset = ($page - 1) * $limit;

// Build query with search and sorting
$query = "SELECT * FROM ai_responses WHERE user_input LIKE ? OR ai_response LIKE ? ORDER BY created_at $sort LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($query);
$searchTerm = "%$search%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Get total records for pagination
$countQuery = "SELECT COUNT(*) as total FROM ai_responses WHERE user_input LIKE ? OR ai_response LIKE ?";
$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param("ss", $searchTerm, $searchTerm);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="https://smartbarangayconnect.com/assets/img/logo.jpg">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <title>Response Logs</title>

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
                <h2 class="text-center mb-4">AI Response Log</h2>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form class="d-flex" method="GET" action="">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search logs..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                    <div>
                        <a href="?sort=asc&search=<?php echo htmlspecialchars($search); ?>" class="btn btn-primary btn-sm me-2">Sort by Oldest</a>
                        <a href="?sort=desc&search=<?php echo htmlspecialchars($search); ?>" class="btn btn-secondary btn-sm">Sort by Newest</a>
                    </div>
                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>AI Response</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(substr($row['ai_response'], 0, 50)) . '...'; ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#responseModal" data-response="<?php echo htmlspecialchars($row['ai_response']); ?>">
                                            More Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No logs found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo htmlspecialchars($sort); ?>&search=<?php echo htmlspecialchars($search); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="responseModalLabel">AI Response Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modalResponseText" style="line-height: 1.8; font-size: 1rem;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/moment.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/simplebar.min.js"></script>
    <script src="js/apps.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Populate modal with AI response
        const responseModal = document.getElementById('responseModal');
        responseModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const response = button.getAttribute('data-response');
            const modalResponseText = document.getElementById('modalResponseText');
            const formattedResponse = response
                .replace(/\n/g, '<br>') // Replace newlines with line breaks
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>'); // Bold text between ** **
            modalResponseText.innerHTML = formattedResponse;
        });
    </script>
</body>

</html>
