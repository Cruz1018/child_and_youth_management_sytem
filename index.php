<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conn.php';

// ✅ Check if email & session_token are provided
if (!isset($_GET['email']) || !isset($_GET['session_token'])) {
    header("Location: https://smartbarangayconnect.com");
    exit();
}

$email = $_GET['email'];
$session_token = $_GET['session_token'];

// ✅ Fetch registerlanding data from Main Domain API
$api_url = "https://smartbarangayconnect.com/api_get_registerlanding.php";
$response = file_get_contents($api_url);
$data = json_decode($response, true);

if (!$data || !is_array($data)) {
    die("❌ Failed to fetch data from Main Domain.");
}

// ✅ Find user data by email (para hindi pumasok lahat)
$userData = null;
foreach ($data as $row) {
    if ($row['email'] === $email) {
        $userData = $row;
        break; // Stop loop after finding the correct user
    }
}

//  If no matching user, deny access
if (!$userData) {
    die("❌ No matching user found!");
}

// ✅ Clear old data for this email (not all data)
$stmt = $conn->prepare("DELETE FROM registerlanding WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->close();

// ✅ Insert only the matched user
$stmt = $conn->prepare("INSERT INTO registerlanding 
    (id, email, first_name, last_name, session_token, birth_date, sex, mobile, working, occupation, house, street, barangay, city) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("❌ Query Preparation Failed: " . $conn->error);
}

$stmt->bind_param("isssssssssssss", 
    $userData['id'], $userData['email'], $userData['first_name'], $userData['last_name'], $userData['session_token'],
    $userData['birth_date'], $userData['sex'], $userData['mobile'], $userData['working'], $userData['occupation'],
    $userData['house'], $userData['street'], $userData['barangay'], $userData['city']
);
$stmt->execute();
$stmt->close();

// ✅ Verify session token in subdomain database
$sql = "SELECT id, email, first_name, last_name, birth_date, sex, mobile, working, occupation, house, street, barangay, city 
        FROM registerlanding WHERE email = ? AND session_token = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("❌ Query Preparation Failed: " . $conn->error);
}

$stmt->bind_param("ss", $email, $session_token);
if (!$stmt->execute()) {
    die("❌ Query Execution Failed: " . $stmt->error);
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Invalid session token or email!");
}

$row = $result->fetch_assoc();

// ✅ Store all data in session
$_SESSION['id'] = $row['id'];
$_SESSION['email'] = $email;
$_SESSION['first_name'] = $row['first_name'];
$_SESSION['last_name'] = $row['last_name'];
$_SESSION['session_token'] = $session_token;

// ✅ Additional session data
$_SESSION['birth_date'] = $row['birth_date'];
$_SESSION['sex'] = $row['sex'];
$_SESSION['mobile'] = $row['mobile'];
$_SESSION['working'] = $row['working'];
$_SESSION['occupation'] = $row['occupation'];
$_SESSION['house'] = $row['house'];
$_SESSION['street'] = $row['street'];
$_SESSION['barangay'] = $row['barangay'];
$_SESSION['city'] = $row['city'];

// ✅ Redirect to dashboard
header("Location: user/landing_page.php");
exit();
?>