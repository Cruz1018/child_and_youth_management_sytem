<?php
include '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = $_POST['users'];
    $action = $_POST['action'];
    $points = intval($_POST['points']);
    $reason = $_POST['reason'];

    $usernames = [];
    foreach ($users as $user_id) {
        // Fetch username for display
        $user_query = $conn->prepare("SELECT username FROM user WHERE id = ?");
        $user_query->bind_param("i", $user_id);
        $user_query->execute();
        $user_query->bind_result($username);
        $user_query->fetch();
        $usernames[] = $username;
        $user_query->close();

        // Insert into points_log
        $stmt = $conn->prepare("INSERT INTO points_log (user_id, action, points, reason) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("isis", $user_id, $action, $points, $reason);
            $stmt->execute();
            $stmt->close();
        }
    }

    $conn->close();
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
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
      max-width: 500px;
      width: 100%;
    }
    .icon {
      font-size: 50px;
      color: #4caf50;
      margin-bottom: 20px;
    }
    .message {
      font-size: 22px;
      color: #333;
      margin-bottom: 10px;
    }
    .details {
      font-size: 16px;
      color: #555;
      margin-bottom: 20px;
    }
    .loading {
      font-size: 14px;
      color: #777;
    }
    .user-list {
      margin: 10px 0;
      padding: 0;
      list-style: none;
      text-align: left;
    }
    .user-list li {
      font-size: 14px;
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
    <div class="loading">Redirecting to Manage Points page in 3 seconds...</div>
  </div>
  <script>
    setTimeout(() => {
      window.location.href = 'apoints.php';
    }, 3000);
  </script>
</body>
</html>
