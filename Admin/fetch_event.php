<?php
// Function to fetch data from the API
function fetchEventData($url) {
    // Initialize cURL session
    $ch = curl_init();

    // Set the URL and options for the request
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);

    // Execute the request and fetch the response
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        curl_close($ch);
        return null; // Return null if an error occurs
    }

    // Close the cURL session
    curl_close($ch);

    return $response;
}

// Fetch data from the API
$apiUrl = "https://barangayevents.smartbarangayconnect.com/pages/admin/events_api.php";
$response = fetchEventData($apiUrl);
$events = json_decode($response, true);

// Ensure $events is an array
if (!is_array($events)) {
    $events = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="assets/images/unified-lgu-logo.png">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <title>Landing Page</title>

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
            <div class="content container">
                <h1 class="my-4">Events</h1>
                <div class="table-responsive">
                    <table id="eventsTable" class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Schedule</th>
                                <th>Poster</th>
                                <th>Location</th>
                                <th>Organizer Name</th>
                                <th>Organizer Contact</th>
                                <th>Event Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($events['data'])): ?>
                                <?php foreach ($events['data'] as $event): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($event['id'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($event['title'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($event['description'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($event['schedule'] ?? ''); ?></td>
                                        <td>
                                            <?php if (!empty($event['poster_base64'])): ?>
                                                <img src="data:image/jpeg;base64,<?php echo $event['poster_base64']; ?>" alt="Poster" style="width: 100px; height: auto; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-image="data:image/jpeg;base64,<?php echo $event['poster_base64']; ?>">
                                            <?php else: ?>
                                                No Poster
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($event['location'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($event['organizer_name'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($event['organizer_contact'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($event['event_type'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No events found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal for Image Zoom -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Poster</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Poster" style="width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/moment.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/simplebar.min.js"></script>
    <script src="js/daterangepicker.js"></script>
    <script src="js/jquery.stickOnScroll.js"></script>
    <script src="js/tinycolor-min.js"></script>
    <script src="js/d3.min.js"></script>
    <script src="js/topojson.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/gauge.min.js"></script>
    <script src="js/jquery.sparkline.min.js"></script>
    <script src="js/apexcharts.min.js"></script>
    <script src="js/apexcharts.custom.js"></script>
    <script src="js/jquery.mask.min.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/jquery.steps.min.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/jquery.timepicker.js"></script>
    <script src="js/dropzone.min.js"></script>
    <script src="js/uppy.min.js"></script>
    <script src="js/quill.min.js"></script>
    <script src="js/apps.js"></script>
    <script src="js/preloader.js"></script>
    <script src="js/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#eventsTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthChange": true,
                "pageLength": 10
            });
        });

        // JavaScript to update the modal image source dynamically
        document.addEventListener('DOMContentLoaded', function () {
            const imageModal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');

            imageModal.addEventListener('show.bs.modal', function (event) {
                const triggerElement = event.relatedTarget; // Element that triggered the modal
                const imageSrc = triggerElement.getAttribute('data-bs-image');
                modalImage.setAttribute('src', imageSrc);
            });
        });
    </script>
</body>
</html>
