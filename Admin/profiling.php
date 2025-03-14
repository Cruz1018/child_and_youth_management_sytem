<?php
include '../conn.php';
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
                <div class="filter-container mb-4">
                    <div class="col-md-4">
                        <label for="searchInput" class="form-label">Search:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="searchIcon"><i class="fa fa-search"></i></span>
                            </div>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by Name, Age, or Tags..." aria-describedby="searchIcon">
                        </div>
                    </div>
                </div>
                <?php
                $tagsFrequency = [];
                $totalAge = 0;
                $count = 0;

                $sql = "SELECT age, tags FROM cy";
                $result = $conn->query($sql);

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

                $sql = "SELECT name, age, location, guardian, contacts, tags FROM cy";
                $result = $conn->query($sql);

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
                              </tr>";
                    }
                    echo "</tbody></table></div>";
                } else {
                    echo "<p class='mt-4'>0 results</p>";
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
        $(document).ready(function(){
            $("#searchInput").on("keyup", function() {
                var searchValue = $("#searchInput").val().toLowerCase();
                $("#profilingTable tbody tr").filter(function() {
                    var name = $(this).find("td:eq(0)").text().toLowerCase();
                    var age = $(this).find("td:eq(1)").text().toLowerCase();
                    var tags = $(this).find("td:eq(5)").text().toLowerCase();
                    $(this).toggle(name.indexOf(searchValue) > -1 || age.indexOf(searchValue) > -1 || tags.indexOf(searchValue) > -1);
                });
            });

            // Initialize DataTables with pagination
            $('#profilingTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "pageLength": 5
            });
        });

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
    </script>
</body>
</html>
