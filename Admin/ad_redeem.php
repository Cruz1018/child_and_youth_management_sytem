<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="assets/images/unified-lgu-logo.png">
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <title>Landing Page</title>

  <!-- Simple bar CSS (for scrollbar) -->
  <link rel="stylesheet" href="css/simplebar.css">
  <!-- Fonts CSS -->
  <link
    href="https://fonts.googleapis.com/css2?family=Overpass:wght@100;200;300;400;600;700;800;900&display=swap"
    rel="stylesheet">
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">
  <!-- Icons CSS -->
  <link rel="stylesheet" href="css/feather.css">
  <!-- App CSS -->
  <link rel="stylesheet" href="css/main.css">
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="vertical light">
  <div class="wrapper">
    <!-- Updated navbar -->
    <?php include 'sections/navbar.php'; ?>
    <!-- Updated sidebar -->
    <?php include 'sections/sidebar.php'; ?>

    <main role="main" class="main-content">
      <div class="content">
        <h2>Redeem Points Management</h2>

        <!-- Success Alert -->
        <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Item added successfully.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>

        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addItemModal">Add Item</button>

        <table id="items-table" class="table mt-3">
          <thead>
            <tr>
              <th>Item Name</th>
              <th>Points Required</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            include '../conn.php';
            $result = $conn->query("SELECT id, item_name, points_required, description FROM redeemable_items");
            if ($result) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['item_name']}</td>
                        <td>{$row['points_required']}</td>
                        <td>{$row['description']}</td>
                        <td>
                          <button class='btn btn-warning btn-sm edit-item' data-id='{$row['id']}'>Edit</button>
                          <button class='btn btn-danger btn-sm delete-item' data-id='{$row['id']}'>Delete</button>
                        </td>
                      </tr>";
              }
            } else {
              echo "<tr><td colspan='4'>Error fetching items: " . $conn->error . "</td></tr>";
            }
            $conn->close();
            ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Add Item Modal -->
  <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form action="process_redeem.php" method="post">
          <div class="modal-header">
            <h5 class="modal-title" id="addItemModalLabel">Add Redeemable Item</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="item_name">Item Name:</label>
              <input type="text" id="item_name" name="item_name" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="points_required">Points Required:</label>
              <input type="number" id="points_required" name="points_required" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="description">Description:</label>
              <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Item Modal -->
  <div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form id="editItemForm">
          <div class="modal-header">
            <h5 class="modal-title" id="editItemModalLabel">Edit Redeemable Item</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="edit_item_id" name="id">
            <div class="form-group">
              <label for="edit_item_name">Item Name:</label>
              <input type="text" id="edit_item_name" name="item_name" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="edit_points_required">Points Required:</label>
              <input type="number" id="edit_points_required" name="points_required" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="edit_description">Description:</label>
              <textarea id="edit_description" name="description" class="form-control" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- JavaScript Dependencies -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/moment.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/simplebar.min.js"></script>
  <script src="js/main.js"></script>
  <script src="js/apps.js"></script>

  <script>
    $(document).ready(function () {
      console.log('Document is ready');

      // Handle delete button click using event delegation
      $('#items-table').on('click', '.delete-item', function () {
        const itemId = $(this).data('id');
        console.log('Delete button clicked for item ID:', itemId);

        if (confirm('Are you sure you want to delete this item?')) {
          $.ajax({
            url: 'delete_item.php',
            type: 'POST',
            data: JSON.stringify({ id: itemId }),
            contentType: 'application/json',
            success: function (response) {
              console.log('Server response:', response);
              if (response.trim() === 'success') {
                alert('Item deleted successfully.');
                location.reload();
              } else {
                alert('Error deleting item: ' + response);
              }
            },
            error: function (xhr, status, error) {
              console.error('AJAX error:', status, error);
              alert('An error occurred while processing your request.');
            }
          });
        }
      });

      // Handle edit button click
      $('#items-table').on('click', '.edit-item', function () {
        const itemId = $(this).data('id');
        console.log('Edit button clicked for item ID:', itemId);

        // Fetch item details via AJAX
        $.ajax({
          url: 'get_item.php',
          type: 'GET',
          data: { id: itemId },
          success: function (response) {
            const item = JSON.parse(response);
            $('#edit_item_id').val(item.id);
            $('#edit_item_name').val(item.item_name);
            $('#edit_points_required').val(item.points_required);
            $('#edit_description').val(item.description);
            $('#editItemModal').modal('show');
          },
          error: function (xhr, status, error) {
            console.error('AJAX error:', status, error);
            alert('An error occurred while fetching item details.');
          }
        });
      });

      // Handle edit form submission
      $('#editItemForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
          url: 'update_item.php',
          type: 'POST',
          data: formData, // Send as application/x-www-form-urlencoded
          success: function (response) {
            console.log('Server response:', response);
            if (response.trim() === 'success') {
              alert('Item updated successfully.');
              location.reload();
            } else {
              alert('Error updating item: ' + response);
            }
          },
          error: function (xhr, status, error) {
            console.error('AJAX error:', status, error);
            alert('An error occurred while processing your request.');
          }
        });
      });
    });
  </script>
</body>

</html>
