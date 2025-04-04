<?php
session_start();
include '../conn.php'; // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die('User not logged in');
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// Initialize variables
$points = 0;
$show_modal = false;

// Fetch the user's current points
$query = "SELECT points FROM user_points WHERE user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die('Database error: ' . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$points = $row['points'] ?? 0;
$stmt->close();

// Ensure the user has an entry in `user_points`, if not, create one
if ($result->num_rows == 0) {
    $insert_query = "INSERT INTO user_points (user_id, points) VALUES (?, 0)";
    $insert_stmt = $conn->prepare($insert_query);
    if (!$insert_stmt) {
        die('Database error: ' . $conn->error);
    }
    $insert_stmt->bind_param("i", $user_id);
    $insert_stmt->execute();
    $insert_stmt->close();
}

// Check if the user has already received points today
$check_query = "SELECT * FROM user_points_log WHERE user_id = ? AND date = ?";
$check_stmt = $conn->prepare($check_query);
if (!$check_stmt) {
    die('Database error: ' . $conn->error);
}
$check_stmt->bind_param("is", $user_id, $today);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$already_received = $check_result->num_rows > 0;
$check_stmt->close();

if (!$already_received) {
    // User has not received points today, grant 5 points
    $points_change = 5;
    $description = 'Daily login bonus';

    $update_query = "UPDATE user_points SET points = points + ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_query);
    if (!$update_stmt) {
        die('Database error: ' . $conn->error);
    }
    $update_stmt->bind_param("ii", $points_change, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    // Log the points received
    $log_query = "INSERT INTO user_points_log (user_id, date, points_change, description) VALUES (?, ?, ?, ?)";
    $log_stmt = $conn->prepare($log_query);
    if (!$log_stmt) {
        die('Database error: ' . $conn->error);
    }
    $log_stmt->bind_param("isis", $user_id, $today, $points_change, $description);
    $log_stmt->execute();
    $log_stmt->close();

    // Set flag to show modal
    $show_modal = true;
}

// Fetch the user's points history
$history_query = "SELECT date, points_change, description FROM user_points_log WHERE user_id = ? ORDER BY date DESC";
$history_stmt = $conn->prepare($history_query);
if (!$history_stmt) {
    die('Database error: ' . $conn->error);
}
$history_stmt->bind_param("i", $user_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
$history = [];
while ($history_row = $history_result->fetch_assoc()) {
    $history[] = $history_row;
}
$history_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Points</title>
    <link rel="icon" href="assets/images/unified-lgu-logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="css/simplebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/feather.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 40px 20px;
        }
        .points-section {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
            text-align: center;
        }
        .points-section h2 {
            font-weight: 700;
            color: #007bff;
            margin-bottom: 15px;
        }
        .points-section .points {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }
        .history-section {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .history-section h2 {
            font-weight: 700;
            color: #007bff;
            margin-bottom: 15px;
        }
        .history-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .history-section table th, .history-section table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .history-section table td {
            position: relative;
        }
        .history-section table td:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: #fff;
            padding: 5px;
            border-radius: 5px;
            white-space: nowrap;
            font-size: 12px;
            z-index: 10;
        }
        .chart-container {
            margin-top: 20px;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
        }
        .refresh-button {
            margin-top: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .refresh-button:hover {
            background-color: #0056b3;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="vertical light">
    <div class="wrapper">
        <?php include 'sections/navbar.php'; ?>
        <?php include 'sections/sidebar.php'; ?>

        <main role="main" class="main-content">
            <section id="dashboard" class="section-content">
                <div class="container">
                    <div class="points-section">
                        <h2>Your Points</h2>
                        <div class="points"><?php echo $points; ?></div>
                    </div>
                    <div class="chart-container">
                        <canvas id="pointsChart"></canvas>
                        <button class="refresh-button">Refresh Points History</button>
                    </div>
                    <div class="history-section">
                        <h2>Points History</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Change</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $entry): ?>
                                    <tr>
                                        <td><?php echo $entry['date']; ?></td>
                                        <td data-tooltip="Points change"><?php echo $entry['points_change']; ?></td>
                                        <td data-tooltip="Reason for points"><?php echo $entry['description']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <?php if ($show_modal): ?>
    <div id="pointsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="pointsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pointsModalLabel">Congratulations!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>You have received 5 points for logging in today.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

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
            // Check if the modal should be shown
            <?php if ($show_modal): ?>
                $('#pointsModal').modal('show');
            <?php endif; ?>

            // Initialize chart
            const ctx = document.getElementById('pointsChart').getContext('2d');
            const pointsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_column($history, 'date')); ?>,
                    datasets: [{
                        label: 'Points Change',
                        data: <?php echo json_encode(array_column($history, 'points_change')); ?>,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Points'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });

            // Refresh points history dynamically
            $('.refresh-button').click(function() {
                $.ajax({
                    url: 'fetch_points_history.php', // Correct endpoint
                    method: 'GET',
                    success: function(data) {
                        try {
                            const parsedData = JSON.parse(data);
                            pointsChart.data.labels = parsedData.labels;
                            pointsChart.data.datasets[0].data = parsedData.data;
                            pointsChart.update();
                        } catch (e) {
                            alert('Error parsing points history data.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Failed to refresh points history: ' + error);
                    }
                });
            });
        });
    </script>
</body>
</html>
