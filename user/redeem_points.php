<?php
session_start();
include '../conn.php'; // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die('User not logged in');
}

$user_id = $_SESSION['user_id'];

// Fetch redeemable items with max claims and cooldown
$items_query = "SELECT id, item_name, points_required, description, image_path, max_claims, cooldown_hours FROM redeemable_items";
$items_result = $conn->query($items_query);
$items = [];
if ($items_result) {
    while ($row = $items_result->fetch_assoc()) {
        $items[] = $row;
    }
}

// Check user's claim history for cooldown and max claims
$claim_check_query = "SELECT COUNT(*) AS claim_count, MAX(claimed_at) AS last_claimed 
                      FROM claimed_items 
                      WHERE user_id = ? AND item_id = ?";
$claim_check_stmt = $conn->prepare($claim_check_query);

// Fetch user's current points
$points_query = "SELECT points FROM user_points WHERE user_id = ?";
$stmt = $conn->prepare($points_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_points = $result->fetch_assoc()['points'] ?? 0;
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];

    // Fetch item details
    $item_query = "SELECT item_name, points_required, max_claims, cooldown_hours FROM redeemable_items WHERE id = ?";
    $stmt = $conn->prepare($item_query);
    if (!$stmt) {
        die('Database error: ' . $conn->error); // Add error handling
    }
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $item_result = $stmt->get_result();
    $item = $item_result->fetch_assoc();
    $stmt->close();

    if (!$item) {
        $error_message = "Item not found.";
    } else {
        // Check claim history
        $claim_check_stmt->bind_param("ii", $user_id, $item_id);
        $claim_check_stmt->execute();
        $claim_check_result = $claim_check_stmt->get_result();
        $claim_data = $claim_check_result->fetch_assoc();
        $claim_count = $claim_data['claim_count'];
        $last_claimed = $claim_data['last_claimed'];

        // Calculate cooldown expiration
        $cooldown_expiration = strtotime($last_claimed) + ($item['cooldown_hours'] * 3600);
        $current_time = time();

        if ($claim_count >= $item['max_claims']) {
            $error_message = "You have reached the maximum number of claims for this item.";
        } elseif ($last_claimed && $current_time < $cooldown_expiration) {
            $remaining_time = $cooldown_expiration - $current_time;
            $hours = floor($remaining_time / 3600);
            $minutes = floor(($remaining_time % 3600) / 60);
            $error_message = "You can claim this item again in $hours hours and $minutes minutes.";
        } elseif ($user_points < $item['points_required']) {
            $error_message = "Not enough points to redeem this item.";
        } else {
            // Deduct points and log the transaction
            $conn->begin_transaction();
            try {
                $points_required = $item['points_required'];
                $deduct_query = "UPDATE user_points SET points = points - ? WHERE user_id = ?";
                $deduct_stmt = $conn->prepare($deduct_query);
                if (!$deduct_stmt) {
                    throw new Exception('Database error: ' . $conn->error); // Add error handling
                }
                $deduct_stmt->bind_param("ii", $points_required, $user_id);
                $deduct_stmt->execute();
                $deduct_stmt->close();

                $log_query = "INSERT INTO user_points_log (user_id, date, points_change, description) VALUES (?, NOW(), ?, ?)";
                $log_stmt = $conn->prepare($log_query);
                if (!$log_stmt) {
                    throw new Exception('Database error: ' . $conn->error); // Add error handling
                }
                $description = "Redeemed item: " . $item['item_name'];
                $points_change = -$points_required;
                $log_stmt->bind_param("iis", $user_id, $points_change, $description);
                $log_stmt->execute();
                $log_stmt->close();

                $claim_query = "INSERT INTO claimed_items (user_id, item_id, stub_number) VALUES (?, ?, ?)";
                $claim_stmt = $conn->prepare($claim_query);
                if (!$claim_stmt) {
                    throw new Exception('Database error: ' . $conn->error); // Add error handling
                }
                $stub_number = uniqid('STUB-');
                $claim_stmt->bind_param("iis", $user_id, $item_id, $stub_number);
                $claim_stmt->execute();
                $claim_stmt->close();

                // Decrement max_claims for the item
                $update_claims_query = "UPDATE redeemable_items SET max_claims = max_claims - 1 WHERE id = ?";
                $update_claims_stmt = $conn->prepare($update_claims_query);
                if (!$update_claims_stmt) {
                    throw new Exception('Database error: ' . $conn->error); // Add error handling
                }
                $update_claims_stmt->bind_param("i", $item_id);
                $update_claims_stmt->execute();
                $update_claims_stmt->close();

                $conn->commit();
                $success_message = "Item redeemed successfully! Stub Number: $stub_number";
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = $e->getMessage(); // Display the specific error
            }
        }
    }
}

// Fetch user's name and last name
$user_query = "SELECT firstname, lastname FROM user WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();
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
  <title>Redeem Rewards</title>

  <!-- Simple bar CSS (for scrollbar)-->
  <link rel="stylesheet" href="css/simplebar.css">
  <!-- Fonts CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <!-- Icons CSS -->
  <link rel="stylesheet" href="css/feather.css">
  <!-- App CSS -->
  <link rel="stylesheet" href="css/main.css">
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
        .items {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .item {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .item:hover {
            transform: scale(1.05);
        }
        .item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .item button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .item button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .item button:hover:not(:disabled) {
            background-color: #0056b3;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2000;
        }
        .spinner div {
            width: 40px;
            height: 40px;
            margin: 5px;
            background-color: #007bff;
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out both;
        }
        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0);
            }
            40% {
                transform: scale(1);
            }
        }
        .confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .confirmation-modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .confirmation-modal-content h3 {
            margin-bottom: 20px;
        }
        .confirmation-modal-content button {
            margin: 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .confirm-btn {
            background-color: #28a745;
            color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .confirm-btn:hover {
            background-color: #218838;
            transform: scale(1.05);
        }
        .cancel-btn {
            background-color: #dc3545;
            color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .cancel-btn:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }
    </style>
</head>
<body class="vertical light">
    <div class="wrapper">
        <?php include 'sections/navbar.php'; ?>
        <?php include 'sections/sidebar.php'; ?>

        <div class="spinner">
            <div></div>
        </div>
        <main role="main" class="main-content">
            <div class="container">
                <h1>Redeemable Items</h1>
                <p>Your Points: <?php echo $user_points; ?></p>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <div class="items">
                    <?php foreach ($items as $item): ?>
                        <div class="item">
                            <img src="../uploads/<?php echo $item['image_path']; ?>" alt="<?php echo $item['item_name']; ?>">
                            <h3><?php echo $item['item_name']; ?></h3>
                            <p><?php echo $item['description']; ?></p>
                            <p>Points Required: <?php echo $item['points_required']; ?></p>
                            <p>Max Claims: <?php echo $item['max_claims']; ?></p>
                            <p>Cooldown: <?php echo $item['cooldown_hours']; ?> hours</p>
                            <form method="POST" onsubmit="event.preventDefault(); openConfirmationModal('<?php echo $item['item_name']; ?>', this);">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" <?php echo $user_points < $item['points_required'] ? 'disabled' : ''; ?>>Redeem</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div id="redeemModal" class="modal">
        <div class="modal-content">
            <h2>Redemption Details</h2>
            <p><strong>Name:</strong> <?php echo $user['firstname'] . ' ' . $user['lastname']; ?></p>
            <p><strong>Stub Number:</strong> <span id="stubNumber"><?php echo $stub_number ?? ''; ?></span></p>
            <p><strong>Date Claimed:</strong> <span id="dateClaimed"><?php echo date('Y-m-d H:i:s'); ?></span></p>
            <button id="downloadJPG">Download as JPG</button>
            <button id="printStub">Print</button>
            <button onclick="closeModal()">Close</button>
        </div>
    </div>

    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-modal-content">
            <h3 id="confirmationMessage">Are you sure you want to redeem this item?</h3>
            <button class="confirm-btn" id="confirmRedemptionBtn">Yes</button>
            <button class="cancel-btn" onclick="closeConfirmationModal()">No</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        let currentForm;

        function openModal() {
            document.getElementById('redeemModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('redeemModal').style.display = 'none';
        }

        function showSpinner() {
            document.querySelector('.spinner').style.display = 'block';
        }

        function openConfirmationModal(itemName, form) {
            currentForm = form;
            document.getElementById('confirmationMessage').textContent = `Are you sure you want to redeem the item: "${itemName}"?`;
            document.getElementById('confirmationModal').style.display = 'flex';
        }

        function closeConfirmationModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }

        document.getElementById('confirmRedemptionBtn').addEventListener('click', function () {
            closeConfirmationModal();
            showSpinner();
            currentForm.submit();
        });

        document.getElementById('downloadJPG').addEventListener('click', function () {
            const modalContent = document.querySelector('.modal-content');
            html2canvas(modalContent).then(canvas => {
                const link = document.createElement('a');
                link.download = 'redemption-details.jpg';
                link.href = canvas.toDataURL('image/jpeg');
                link.click();
            });
        });

        document.getElementById('printStub').addEventListener('click', function () {
            const modalContent = document.querySelector('.modal-content');
            const printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print Stub</title></head><body>');
            printWindow.document.write(modalContent.outerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        });

        <?php if (isset($success_message)): ?>
            openModal();
        <?php endif; ?>
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/moment.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/simplebar.min.js"></script>
    <script src="js/apps.js"></script>
</body>
</html>
