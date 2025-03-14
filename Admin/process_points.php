<?php
include '../conn.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Process Points</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f0f0f0;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      text-align: center;
    }
    .message {
      margin-bottom: 20px;
      font-size: 16px;
      color: #333;
    }
    .countdown {
      font-size: 18px;
      color: #555;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="message">
      <?php
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $users = $_POST['users'];
        $points = $_POST['points'];

        foreach ($users as $user_id) {
          // Update user points
          $stmt = $conn->prepare("UPDATE user_points SET points = points + ? WHERE user_id = ?");
          if ($stmt) {
            $stmt->bind_param("ii", $points, $user_id);
            if ($stmt->execute()) {
              echo "Points added successfully for user ID: $user_id<br>";
            } else {
              echo "Error executing statement for user ID: $user_id - " . $stmt->error . "<br>";
            }
            $stmt->close();
          } else {
            echo "Error preparing statement: " . $conn->error . "<br>";
          }

          // Log the points update
          $stmt = $conn->prepare("INSERT INTO user_points_log (user_id, date, points_change, description) VALUES (?, NOW(), ?, 'Points added')");
          if ($stmt) {
            $stmt->bind_param("ii", $user_id, $points);
            if ($stmt->execute()) {
              echo "Points log added successfully for user ID: $user_id<br>";
            } else {
              echo "Error executing log statement for user ID: $user_id - " . $stmt->error . "<br>";
            }
            $stmt->close();
          } else {
            echo "Error preparing log statement: " . $conn->error . "<br>";
          }
        }

        $conn->close();
      }
      ?>
    </div>
    <div class="countdown">Redirecting in <span id="countdown">3</span> seconds...</div>
  </div>
  <script>
    let countdownElement = document.getElementById('countdown');
    let countdown = 3;
    setInterval(() => {
      countdown--;
      countdownElement.textContent = countdown;
      if (countdown === 0) {
        window.location.href = 'apoints.php';
      }
    }, 1000);
  </script>
</body>
</html>
