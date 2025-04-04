<?php
session_start(); // Start the session
include '../conn.php';

// Fetch the logged-in user's username
$username = $_SESSION['username'] ?? 'Guest';

$content = '';
$imagePaths = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['content'])) {
        $content = $_POST['content'];
        $userId = $_SESSION['user_id'] ?? null; // Get the logged-in user's ID from the session

        if (!$userId) {
            die("Error: User not logged in."); // Handle the case where the user is not logged in
        }

        // Insert post content into the posts table with 'pending' status
        $stmt = $conn->prepare("INSERT INTO posts (content, user_id, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("si", $content, $userId);
        $stmt->execute();
        $postId = $stmt->insert_id;
        $stmt->close();

        // Handle image uploads if images are provided
        if (isset($_FILES['images']['tmp_name']) && is_array($_FILES['images']['tmp_name']) && !empty($_FILES['images']['tmp_name'][0])) {
            if (!is_dir('../Admin/uploads/')) {
                mkdir('../Admin/uploads/', 0777, true); // Create the directory with proper permissions
            }
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $imagePath = '../Admin/uploads/' . basename($_FILES['images']['name'][$key]);
                move_uploaded_file($tmp_name, $imagePath);

                // Insert image path into the post_images table
                $stmt = $conn->prepare("INSERT INTO post_images (post_id, image_path) VALUES (?, ?)");
                $stmt->bind_param("is", $postId, $imagePath);
                $stmt->execute();
                $stmt->close();
            }
        }

        echo "<script>alert('Your post has been submitted for approval.');</script>";
    }

    // Handle new comment submission
    if (isset($_POST['comment_content']) && isset($_POST['post_id'])) {
        $commentContent = $_POST['comment_content'];
        $postId = $_POST['post_id'];
        $userId = $_SESSION['user_id'] ?? null; // Get the logged-in user's ID
        $commentImagePath = null;

        if (!$userId) {
            die("Error: User not logged in."); // Handle the case where the user is not logged in
        }

        // Handle comment image upload
        if (!empty($_FILES['comment_image']['tmp_name'])) {
            if (!is_dir('../Admin/uploads/comments/')) {
                mkdir('../Admin/uploads/comments/', 0777, true); // Create the comments directory
            }
            $commentImagePath = '../Admin/uploads/comments/' . basename($_FILES['comment_image']['name']);
            move_uploaded_file($_FILES['comment_image']['tmp_name'], $commentImagePath);
        }

        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content, image_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $postId, $userId, $commentContent, $commentImagePath);
        $stmt->execute();
        $stmt->close();
    }

    // Handle comment deletion
    if (isset($_POST['delete_comment_id'])) {
        $commentId = $_POST['delete_comment_id'];
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            die("Error: User not logged in."); // Handle the case where the user is not logged in
        }

        // Verify the comment belongs to the logged-in user
        $stmt = $conn->prepare("SELECT id FROM comments WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $commentId, $userId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();
            $deleteStmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
            $deleteStmt->bind_param("i", $commentId);
            $deleteStmt->execute();
            $deleteStmt->close();
        } else {
            $stmt->close();
            die("Error: Unauthorized action.");
        }
    }
}

// Fetch only approved posts for display
$posts = $conn->query("
    SELECT p.id, p.content, pi.image_path, u.username 
    FROM posts p 
    LEFT JOIN post_images pi ON p.id = pi.post_id 
    LEFT JOIN user u ON p.user_id = u.id 
    WHERE p.status = 'approved'
    ORDER BY p.id DESC
");

// Fetch data for sidebar and navbar
$userResult = $conn->query("SELECT COUNT(*) as count FROM user");
$userCount = $userResult->fetch_assoc()['count'];

$volunteerResult = $conn->query("SELECT COUNT(*) as count FROM volunteer");
$volunteerCount = $volunteerResult->fetch_assoc()['count'];

$eventsResult = $conn->query("SELECT COUNT(*) as count FROM events");
$eventsCount = $eventsResult->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum - Posts</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/simplebar.css">
    <link rel="stylesheet" href="css/feather.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
        }
        .post-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .post-card h3 {
            margin-bottom: 10px;
            font-size: 18px;
        }
        .post-image {
            max-width: 250px;
            height: auto;
            border-radius: 5px;
            display: block;
            margin-top: 10px;
            cursor: pointer;
        }
        .comments {
            margin-top: 15px;
            padding-left: 15px;
            border-left: 2px solid #ddd;
        }
        .comment {
            padding: 10px;
            margin-bottom: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .comment img {
            max-width: 200px;
            height: auto;
            border-radius: 5px;
            display: block;
            cursor: pointer;
        }
        .post-form, .comment-form {
            background: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            resize: none;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.9);
        }
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }
        .modal-content, #caption {
            animation-name: zoom;
            animation-duration: 0.6s;
        }
        @keyframes zoom {
            from {transform: scale(0)}
            to {transform: scale(1)}
        }
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }
        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
        .char-counter {
            font-size: 12px;
            color: #6c757d;
            text-align: right;
            margin-top: -10px;
            margin-bottom: 10px;
        }
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .image-preview img {
            max-width: 100px;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script>
        // Character counter for text areas
        function updateCharCounter(textarea, counterId, maxLength) {
            const counter = document.getElementById(counterId);
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${remaining} characters remaining`;
        }

        // Image preview for file inputs
        function previewImages(input, previewContainerId) {
            const previewContainer = document.getElementById(previewContainerId);
            previewContainer.innerHTML = ''; // Clear previous previews
            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        previewContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }
    </script>
</head>
<body class="vertical light">
    <div class="wrapper">
        <?php include 'sections/navbar.php'; ?>
        <?php include 'sections/sidebar.php'; ?>

        <main role="main" class="main-content">
            <div class="container">
                <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
                <h2>Forum - Create a Post</h2>
                <div class="post-form">
                    <form action="posting.php" method="post" enctype="multipart/form-data">
                        <textarea name="content" placeholder="What's on your mind?" required 
                                  oninput="updateCharCounter(this, 'postCharCounter', 500)" maxlength="500"></textarea>
                        <div id="postCharCounter" class="char-counter">500 characters remaining</div>
                        <input type="file" name="images[]" multiple 
                               onchange="previewImages(this, 'postImagePreview')">
                        <div id="postImagePreview" class="image-preview"></div>
                        <button type="submit">Post</button>
                    </form>
                </div>

                <h2>Recent Posts</h2>
                <div id="posts">
                    <?php while ($post = $posts->fetch_assoc()): ?>
                        <div class="post-card">
                            <h3><?php echo nl2br(htmlspecialchars($post['content'])); ?></h3>
                            <p><strong>Posted by:</strong> <?php echo htmlspecialchars($post['username']); ?></p>
                            <?php if (!empty($post['image_path'])): ?>
                                <img src="../Admin/<?php echo htmlspecialchars($post['image_path']); ?>" class="post-image" alt="Post Image" onclick="openModal(this)">
                            <?php endif; ?>

                            <div class="comments">
                                <h4>Comments</h4>
                                <?php
                                $postId = $post['id'];
                                $comments = $conn->query("
                                    SELECT c.id, c.content, c.image_path, c.user_id, u.username 
                                    FROM comments c 
                                    LEFT JOIN user u ON c.user_id = u.id 
                                    WHERE c.post_id = $postId 
                                    ORDER BY c.created_at ASC
                                ");
                                ?>
                                <?php while ($comment = $comments->fetch_assoc()): ?>
                                    <div class="comment">
                                        <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                        <p><strong>Commented by:</strong> <?php echo htmlspecialchars($comment['username'] ?? 'Unknown'); ?></p>
                                        <?php if ($comment['image_path']): ?>
                                            <img src="../Admin/<?php echo htmlspecialchars($comment['image_path']); ?>" alt="Comment Image" onclick="openModal(this)">
                                        <?php endif; ?>
                                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                                            <form action="posting.php" method="post" style="display:inline;">
                                                <input type="hidden" name="delete_comment_id" value="<?php echo $comment['id']; ?>">
                                                <button type="submit" onclick="return confirm('Are you sure you want to delete this comment?')">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                            <div class="comment-form">
                                <form action="posting.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                                    <textarea name="comment_content" placeholder="Add a comment..." required 
                                              oninput="updateCharCounter(this, 'commentCharCounter<?php echo $postId; ?>', 300)" maxlength="300"></textarea>
                                    <div id="commentCharCounter<?php echo $postId; ?>" class="char-counter">300 characters remaining</div>
                                    <input type="file" name="comment_image" 
                                           onchange="previewImages(this, 'commentImagePreview<?php echo $postId; ?>')">
                                    <div id="commentImagePreview<?php echo $postId; ?>" class="image-preview"></div>
                                    <button type="submit">Comment</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </main>
    </div>

    <div id="myModal" class="modal" onclick="closeModal()">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="img01">
    </div>

    <script>
        function openModal(img) {
            var modal = document.getElementById("myModal");
            var modalImg = document.getElementById("img01");
            modal.style.display = "block";
            modalImg.src = img.src;
        }

        function closeModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }

        // Close the modal when clicking outside the image
        window.onclick = function(event) {
            var modal = document.getElement.getElementById("myModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

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
</body>
</html>

<?php
$conn->close();
?>
