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
        .filter-container {
            display: flex;
            justify-content: flex-start; /* Change to flex-start to align items to the left */
            align-items: center;
        }
        .pagination {
            justify-content: flex-start; /* Align pagination to the left */
        }
        .table-responsive {
            overflow-x: hidden; /* Remove horizontal scroll */
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
                <?php
                $tagsFrequency = [];
                $totalAge = 0;
                $count = 0;

                // Fetch the user's profiling data
                $sql = "SELECT age, tags FROM cy WHERE name = ? AND lastname = ?";
                $stmt = $conn->prepare($sql);

                if ($stmt === false) {
                    die('Error preparing the SQL statement: ' . $conn->error);
                }

                $stmt->bind_param('ss', $userFirstName, $userLastName);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result === false) {
                    die('Error executing the SQL query: ' . $conn->error);
                }

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $totalAge += $row["age"];
                        $count++;
                        $tags = explode(", ", $row["tags"]);
                        foreach ($tags as $tag) {
                            if (isset($tagsFrequency[$tag])) {
                                $tagsFrequency[$tag]++;
                            } else {
                                $tagsFrequency[$tag] = 1;
                            }
                        }
                    }
                }

                $averageAge = $count > 0 ? $totalAge / $count : 0;

                // Fetch the user's profiling data for display
                $sql = "SELECT name, age, location, guardian, contacts, tags FROM cy WHERE name = ? AND lastname = ?";
                $stmt = $conn->prepare($sql);

                if ($stmt === false) {
                    die('Error preparing the SQL statement: ' . $conn->error);
                }

                $stmt->bind_param('ss', $userFirstName, $userLastName);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result === false) {
                    die('Error executing the SQL query: ' . $conn->error);
                }

                if ($result->num_rows > 0) {
                    echo "<div class='table-responsive'>
                            <table class='table table-striped table-hover mt-4' id='profilingTable'>
                                <thead class='thead-dark'>
                                    <tr>
                                        <th>Name</th>
                                        <th>Age</th>
                                        <th>Location</th>
                                        <th>Guardian</th>
                                        <th>Contacts</th>
                                        <th>Tags</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>";
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row["name"] . "</td>
                                <td>" . $row["age"] . "</td>
                                <td>" . $row["location"] . "</td>
                                <td>" . $row["guardian"] . "</td>
                                <td>" . $row["contacts"] . "</td>
                                <td>" . $row["tags"] . "</td>
                                <td><button class='btn btn-primary' onclick='editTags(\"" . $row["tags"] . "\")'>Edit Tags</button></td>
                              </tr>";
                    }
                    echo "</tbody></table></div>";
                } else {
                    echo "<p class='mt-4'>No results found. Start adding your interests now!</p>";
                }

                $conn->close();
                ?>
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
        let conversationHistory = [];
        let profilingData = {
            age: '', // Add logic to fetch age
            tags: [] // Add logic to fetch tags
        };

        let tagsFrequency = <?php echo json_encode($tagsFrequency); ?>;
        let averageAge = <?php echo $averageAge; ?>;

        function sendMessage() {
            const userInput = document.getElementById('userInput').value;
            if (userInput.trim() === '') {
                alert('Please enter a message.');
                return;
            }

            // Fetch age and tags from the profiling table
            const selectedRow = $("#profilingTable tbody tr:visible").first();
            profilingData.age = selectedRow.find("td:eq(1)").text();
            profilingData.tags = selectedRow.find("td:eq(5)").text().split(', ');

            fetch('eventrec.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        userInput,
                        conversationHistory,
                        profilingData,
                        tagsFrequency,
                        averageAge
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const chatbox = document.getElementById('chatbox');
                    chatbox.innerHTML += `<p><strong>You:</strong> ${userInput}</p>`;
                    chatbox.innerHTML += `<p><strong>AI:</strong> ${data.recommendations}</p>`;
                    document.getElementById('userInput').value = '';
                    chatbox.scrollTop = chatbox.scrollHeight;
                    conversationHistory = data.conversationHistory;
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching recommendations.');
                });
        }

        function insertProfilingSummary() {
            const summary = `Profiling Data: Age - ${profilingData.age}, Tags - ${profilingData.tags.join(', ')}, Average Age - ${averageAge}, Tags Frequency - ${JSON.stringify(tagsFrequency)}`;
            document.getElementById('userInput').value = summary;
        }

        // Initialize Bootstrap dropdowns
        $(document).ready(function() {
            $('.dropdown-toggle').dropdown();
        });

        function editTags(currentTags) {
            const newTags = prompt("tell me what things interest you!", currentTags);
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
    </script>
</body>
</html>
