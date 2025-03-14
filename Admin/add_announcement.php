<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: ../login.php");
  exit();
}

include '../conn.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Ensure PHPMailer is installed

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $image_path = '';

  // Handle file upload
  if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $uploadDir = 'uploads/';
    $image_path = $uploadDir . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
  }

  // Insert into database
  $stmt = $conn->prepare("INSERT INTO announcements (title, description, image_path, created_at) VALUES (?, ?, ?, NOW())");
  $stmt->bind_param("sss", $title, $description, $image_path);

  if ($stmt->execute()) {
    echo "New announcement created successfully";

    // Fetch all user emails
    $result = $conn->query("SELECT email FROM user");
    $emails = [];
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $emails[] = $row['email'];
      }
    }

    // Send emails using PHPMailer
    if (!empty($emails)) {
      $mail = new PHPMailer(true);
      try {
        // 🔍 Enable Debugging
        $mail->SMTPDebug = 2; // Set to 0 after debugging

        // 📨 SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'cruzrovick@gmail.com'; // Your email
        $mail->Password = 'spih mlat oohx sjyy'; // Use App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // 📩 Email Details
        $mail->setFrom('no-reply@cyms.smartbarangayconnect.com', 'CYMS Notifications');
        $mail->isHTML(true);
        $mail->Subject = "New Announcement: $title";

        $emailBody = "<h1>$title</h1><p>$description</p><p>Visit <a href='http://cyms.smartbarangayconnect.com'>cyms.smartbarangayconnect.com</a> to check the new announcement now!.</p>";
        $mail->Body = $emailBody;

        // 📬 Send to multiple users
        foreach ($emails as $email) {
          $mail->addAddress($email);
        }

        // ✅ Send Email
        if ($mail->send()) {
          echo "Notification emails sent successfully.";
        } else {
          echo "Error sending emails: " . $mail->ErrorInfo;
        }
      } catch (Exception $e) {
        echo "Email sending failed: {$mail->ErrorInfo}";
      }
    }

    header("Location: announcement.php");
    exit();
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
