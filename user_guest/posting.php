<?php
include '../conn.php';

$content = '';
$imagePaths = [];

// Fetch posts and comments from the database
$posts = $conn->query("SELECT p.id, p.content, pi.image_path FROM posts p LEFT JOIN post_images pi ON p.id = pi.post_id ORDER BY p.id DESC");

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
    </style>
</head>
<body class="vertical light">
    <div class="wrapper">
        <?php include 'sections/navbar.php'; ?>
        <?php include 'sections/sidebar.php'; ?>

        <main role="main" class="main-content">
            <div class="container">
                <h2>Recent Posts</h2>
                <div id="posts">
                    <?php while ($post = $posts->fetch_assoc()): ?>
                        <div class="post-card">
                            <h3><?php echo nl2br(htmlspecialchars($post['content'])); ?></h3>
                            <?php if ($post['image_path']): ?>
                                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" class="post-image" alt="Post Image" onclick="openModal(this)">
                            <?php endif; ?>

                            <div class="comments">
                                <h4>Comments</h4>
                                <?php
                                $postId = $post['id'];
                                $comments = $conn->query("SELECT content, image_path FROM comments WHERE post_id = $postId ORDER BY created_at ASC");
                                ?>
                                <?php while ($comment = $comments->fetch_assoc()): ?>
                                    <div class="comment">
                                        <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                        <?php if ($comment['image_path']): ?>
                                            <img src="<?php echo htmlspecialchars($comment['image_path']); ?>" alt="Comment Image" onclick="openModal(this)">
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                            <div class="comment-form">
                                <p>You need to <a href="login.php">log in</a> to comment.</p>
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
            var modal = document.getElementById("myModal");
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
