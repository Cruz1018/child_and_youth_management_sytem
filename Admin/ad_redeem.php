<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="https://smartbarangayconnect.com/assets/img/logo.jpg">
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <title>Item Mangement</title>

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
              <th>Image</th>
              <th>Max Claims</th>
              <th>Cooldown Hours</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            include '../conn.php';
            $result = $conn->query("SELECT id, item_name, points_required, description, image_path, max_claims, cooldown_hours FROM redeemable_items");
            if ($result) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['item_name']}</td>
                        <td>{$row['points_required']}</td>
                        <td>{$row['description']}</td>
                        <td>
                          <img src='../uploads/{$row['image_path']}' alt='Item Image' class='zoomable-image' style='width: 50px; height: 50px; cursor: pointer;'>
                        </td>
                        <td>{$row['max_claims']}</td>
                        <td>{$row['cooldown_hours']}</td>
                        <td>
                          <button class='btn btn-warning btn-sm edit-item' data-id='{$row['id']}'>Edit</button>
                          <button class='btn btn-danger btn-sm delete-item' data-id='{$row['id']}'>Delete</button>
                        </td>
                      </tr>";
              }
            } else {
              echo "<tr><td colspan='7'>Error fetching items: " . $conn->error . "</td></tr>";
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
        <form action="process_redeem.php" method="post" enctype="multipart/form-data">
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
            <div class="form-group">
              <label for="item_image">Item Image:</label>
              <input type="file" id="item_image" name="item_image" class="form-control-file" accept="image/*" required>
            </div>
            <div class="form-group">
              <label for="max_claims">Max Claims:</label>
              <input type="number" id="max_claims" name="max_claims" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="cooldown_hours">Cooldown Hours:</label>
              <input type="number" id="cooldown_hours" name="cooldown_hours" class="form-control" required>
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
        <form id="editItemForm" method="post" action="update_item.php" enctype="multipart/form-data">
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
            <div class="form-group">
              <label for="edit_item_image">Item Image:</label>
              <input type="file" id="edit_item_image" name="item_image" class="form-control-file" accept="image/*">
              <img id="current_item_image" src="" alt="Current Image" style="width: 100px; height: 100px; margin-top: 10px;">
            </div>
            <div class="form-group">
              <label for="edit_max_claims">Max Claims:</label>
              <input type="number" id="edit_max_claims" name="max_claims" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="edit_cooldown_hours">Cooldown Hours:</label>
              <input type="number" id="edit_cooldown_hours" name="cooldown_hours" class="form-control" required>
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

  <!-- Add a modal for zooming the image -->
  <div class="modal fade" id="imageZoomModal" tabindex="-1" role="dialog" aria-labelledby="imageZoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="imageZoomModalLabel">Image Preview</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-center">
          <img id="zoomedImage" src="" alt="Zoomed Image" style="max-width: 100%; max-height: 80vh;">
        </div>
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

      // Handle image click to zoom
      $('#items-table').on('click', '.zoomable-image', function () {
        const imageUrl = $(this).attr('src');
        $('#zoomedImage').attr('src', imageUrl);
        $('#imageZoomModal').modal('show');
      });

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
            $('#current_item_image').attr('src', '../uploads/' + item.image_path);
            $('#edit_max_claims').val(item.max_claims);
            $('#edit_cooldown_hours').val(item.cooldown_hours);
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

        const formData = new FormData(this); // Use FormData to handle file uploads

        $.ajax({
          url: 'update_item.php',
          type: 'POST',
          data: formData,
          processData: false, // Prevent jQuery from processing the data
          contentType: false, // Prevent jQuery from setting the content type
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
