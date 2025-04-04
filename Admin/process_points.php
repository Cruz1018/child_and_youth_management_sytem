<?php
include '../conn.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $userIds = $_POST['users'] ?? [];
    $action = $_POST['action'] ?? '';
    $points = intval($_POST['points'] ?? 0);
    $reason = $_POST['reason'] ?? '';

    if (empty($userIds) || empty($action) || $points <= 0 || empty($reason)) {
        header("Location: apoints.php?status=error");
        exit;
    }

    $success = true;

    // Loop through each user and update their points in the user_points table
    foreach ($userIds as $userId) {
        // Check if the user already has an entry in the user_points table
        $checkQuery = "SELECT points FROM user_points WHERE user_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param('i', $userId);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // Update points if the user already exists
            $updateQuery = $action === 'add' 
                ? "UPDATE user_points SET points = points + ? WHERE user_id = ?" 
                : "UPDATE user_points SET points = points - ? WHERE user_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('ii', $points, $userId);
            if (!$updateStmt->execute()) {
                $success = false;
                break;
            }
            $updateStmt->close();
        } else {
            // Insert new entry if the user does not exist
            if ($action === 'add') {
                $insertQuery = "INSERT INTO user_points (user_id, points) VALUES (?, ?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param('ii', $userId, $points);
                if (!$insertStmt->execute()) {
                    $success = false;
                    break;
                }
                $insertStmt->close();
            } else {
                $success = false;
                break;
            }
        }

        // Log the points change in user_points_log
        $logQuery = "INSERT INTO user_points_log (user_id, date, points_change, description) VALUES (?, ?, ?, ?)";
        $logStmt = $conn->prepare($logQuery);
        if (!$logStmt) {
            $success = false;
            break;
        }
        $date = date('Y-m-d');
        $pointsChange = $action === 'add' ? $points : -$points;
        $logStmt->bind_param('isis', $userId, $date, $pointsChange, $reason);
        if (!$logStmt->execute()) {
            $success = false;
            break;
        }
        $logStmt->close();

        $checkStmt->close();
    }

    $conn->close();

    // Redirect back with success message
    header("Location: apoints.php?status=" . ($success ? "success" : "error"));
    exit;
} else {
    header("Location: apoints.php?status=error");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Processing...</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #e9f5ff;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background-color: #ffffff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
      text-align: center;
      max-width: 600px;
      width: 100%;
    }
    .icon {
      font-size: 60px;
      color: #4caf50;
      margin-bottom: 25px;
    }
    .message {
      font-size: 24px;
      color: #333;
      margin-bottom: 15px;
      font-weight: 500;
    }
    .details {
      font-size: 18px;
      color: #555;
      margin-bottom: 25px;
    }
    .loading {
      font-size: 16px;
      color: #777;
    }
    .user-list {
      margin: 15px 0;
      padding: 0;
      list-style: none;
      text-align: left;
    }
    .user-list li {
      font-size: 16px;
      color: #444;
    }
    .highlight {
      font-weight: bold;
      color: #4caf50;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="icon">✔</div>
    <div class="message">
      <?php echo ucfirst($action); ?> Successful!
    </div>
    <div class="details">
      <p><strong>Action:</strong> <?php echo ucfirst($action); ?></p>
      <p><strong>Points:</strong> <span class="highlight"><?php echo $points; ?></span></p>
      <p><strong>Reason:</strong> <?php echo htmlspecialchars($reason); ?></p>
      <p><strong>Total Users Affected:</strong> <span class="highlight"><?php echo count($usernames); ?></span></p>
      <p><strong>Users:</strong></p>
      <ul class="user-list">
        <?php foreach ($usernames as $username): ?>
          <li><?php echo htmlspecialchars($username); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="loading">Redirecting to Redeem Points page in 3 seconds...</div>
  </div>
  <script>
    setTimeout(() => {
      window.location.href = 'ad_redeem.php';
    }, 3000);
  </script>
</body>
</html>
