<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="assets/images/unified-lgu-logo.png">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <title>Data Report</title>

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

<body class="vertical  light">
  <div class="wrapper">
    <?php include 'sections/navbar.php'; ?>
    <?php include 'sections/sidebar.php'; ?>

    <main role="main" class="main-content">
      <div class="content">
        <h2>Recent Events Data Report</h2>
        <button class="btn btn-primary" data-toggle="modal" data-target="#createEventModal">Create Event</button>
        
        <?php
        // Include database connection
        include '../conn.php';

        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          if (isset($_POST['create'])) {
            $event_name = $conn->real_escape_string($_POST['event_name']);
            $participants = $conn->real_escape_string($_POST['participants']);
            $event_date = $conn->real_escape_string($_POST['event_date']);
            $location = $conn->real_escape_string($_POST['location']);
            $sql = "INSERT INTO programs (event_name, participants, event_date, location) VALUES ('$event_name', '$participants', '$event_date', '$location')";
            if ($conn->query($sql) === TRUE) {
              echo "<p>New record created successfully</p>";
            } else {
              echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
            }
          } elseif (isset($_POST['update'])) {
            $id = $conn->real_escape_string($_POST['id']);
            $event_name = $conn->real_escape_string($_POST['event_name']);
            $participants = $conn->real_escape_string($_POST['participants']);
            $event_date = $conn->real_escape_string($_POST['event_date']);
            $location = $conn->real_escape_string($_POST['location']);
            $sql = "UPDATE programs SET event_name='$event_name', participants='$participants', event_date='$event_date', location='$location' WHERE id='$id'";
            if ($conn->query($sql) === TRUE) {
              echo "<p>Record updated successfully</p>";
            } else {
              echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
            }
          } elseif (isset($_POST['delete'])) {
            $id = $conn->real_escape_string($_POST['id']);
            $sql = "DELETE FROM programs WHERE id='$id'";
            if ($conn->query($sql) === TRUE) {
              echo "<p>Record deleted successfully</p>";
            } else {
              echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
            }
          }
        }

        // Fetch data from 'programs' table
        $query = "SELECT id, event_name, participants, event_date, location FROM programs ORDER BY event_date DESC";
        $result = $conn->query($query);

        if ($result) {
          if ($result->num_rows > 0) {
            echo '<table id="data-table" class="table">';
            echo '<thead><tr><th>Event Name</th><th>Participant</th><th>Event Date</th><th>Location</th><th>Actions</th></tr></thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
              echo '<tr>';
              echo '<td>' . $row['event_name'] . '</td>';
              echo '<td>' . $row['participants'] . '</td>';
              echo '<td>' . $row['event_date'] . '</td>';
              echo '<td>' . $row['location'] . '</td>';
              echo '<td>
                      <button class="btn btn-warning edit-btn" data-id="' . $row['id'] . '" data-name="' . $row['event_name'] . '" data-participants="' . $row['participants'] . '" data-date="' . $row['event_date'] . '" data-location="' . $row['location'] . '" data-toggle="modal" data-target="#editEventModal">Edit</button>
                      <button class="btn btn-danger delete-btn" data-id="' . $row['id'] . '" data-toggle="modal" data-target="#deleteEventModal">Delete</button>
                    </td>';
              echo '</tr>';
            }
            echo '</tbody></table>';
          } else {
            echo '<p>No recent events found.</p>';
          }
        } else {
          echo '<p>Error fetching data: ' . $conn->error . '</p>';
        }

        $conn->close();
        ?>
      </div>
    </main>
  </div>

  <!-- Create Event Modal -->
  <div class="modal fade" id="createEventModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create Event</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <form method="POST">
            <input type="hidden" name="create" value="create">
            <div class="form-group">
              <label for="event_name">Event Name</label>
              <input type="text" name="event_name" class="form-control" placeholder="Event Name" required>
            </div>
            <div class="form-group">
              <label for="participants">Participants</label>
              <input type="number" name="participants" class="form-control" placeholder="Participants" required>
            </div>
            <div class="form-group">
              <label for="event_date">Event Date</label>
              <input type="date" name="event_date" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="location">Location</label>
              <input type="text" name="location" class="form-control" placeholder="Location" required>
            </div>
            <button type="submit" class="btn btn-success">Create</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Event Modal -->
  <div class="modal fade" id="editEventModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Event</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <form method="POST">
            <input type="hidden" name="update" value="update">
            <input type="hidden" name="id" id="edit-id">
            <input type="text" name="event_name" id="edit-event-name" class="form-control" placeholder="Event Name" required><br>
            <input type="number" name="participants" id="edit-participants" class="form-control" placeholder="Participants" required><br>
            <input type="date" name="event_date" id="edit-event-date" class="form-control" required><br>
            <input type="text" name="location" id="edit-location" class="form-control" placeholder="Location" required><br>
            <button type="submit" class="btn btn-warning">Edit</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Event Modal -->
  <div class="modal fade" id="deleteEventModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Delete Event</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this event?</p>
          <form method="POST">
            <input type="hidden" name="delete" value="delete">
            <input type="hidden" name="id" id="delete-id">
            <button type="submit" class="btn btn-danger">Delete</button>
          </form>
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
    $('.edit-btn').click(function() {
      $('#edit-id').val($(this).data('id'));
      $('#edit-event-name').val($(this).data('name'));
      $('#edit-participants').val($(this).data('participants'));
      $('#edit-event-date').val($(this).data('date'));
      $('#edit-location').val($(this).data('location'));
    });

    $('.delete-btn').click(function() {
      $('#delete-id').val($(this).data('id'));
    });
  </script>
</body>

</html>
