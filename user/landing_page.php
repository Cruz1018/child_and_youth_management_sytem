<?php
ob_start(); // Start output buffering
session_start(); 
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
    * {
      text-decoration: none !important; /* Remove underline from all text elements */
    }
    .container {
      max-width: 1200px;
      margin: auto;
      padding: 40px 20px;
    }
    .banner-container {
      position: relative;
      border-radius: 10px;
      overflow: hidden;
    }
    .banner-img {
      width: 100%;
      display: none;
      border-radius: 10px;
    }
    .banner-img.active {
      display: block;
    }
    .prev, .next {
      cursor: pointer;
      position: absolute;
      top: 50%;
      width: auto;
      padding: 16px;
      margin-top: -22px;
      color: white;
      font-weight: bold;
      font-size: 18px;
      transition: 0.6s ease;
      border-radius: 0 3px 3px 0;
      user-select: none;
    }
    .next {
      right: 0;
      border-radius: 3px 0 0 3px;
    }
    .prev:hover, .next:hover {
      background-color: rgba(0,0,0,0.8);
    }
    .dots-container {
      text-align: center;
      padding: 20px;
      background: #ddd;
    }
    .dot {
      cursor: pointer;
      height: 15px;
      width: 15px;
      margin: 0 2px;
      background-color: #bbb;
      border-radius: 50%;
      display: inline-block;
      transition: background-color 0.6s ease;
    }
    .dot.active {
      background-color: #717171;
    }
    .content-section {
      background: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
      margin-top: 20px;
    }
    h2 {
      font-weight: 700;
      color: #007bff;
      margin-bottom: 15px;
    }
    .director-info {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 20px;
    }
    .director-img {
      width: 100%;
      max-width: 350px;
      border-radius: 10px;
    }
    .read-more {
      color: #007bff;
      text-decoration: none;
      font-weight: 600;
    }
    .read-more:hover {
      text-decoration: underline;
    }
    .points-section {
      background: #ffffff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
      margin-top: 20px;
      text-align: center;
    }
    .points-section h2 {
      font-weight: 700;
      color: #007bff;
      margin-bottom: 15px;
    }
    .points-section .points {
      font-size: 24px;
      font-weight: 600;
      color: #333;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgb(0,0,0);
      background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 500px;
      border-radius: 10px;
    }
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }
    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
  </style>
</head>

<body class="vertical light">
  <div class="wrapper">
    <?php include '/CYMS/user/sections/navbar.php'; ?>
    <?php include '/CYMS/user/sections/sidebar.php'; ?>
    <nav>
      <!-- Add link to manage tags page -->
      <ul>
        <li><a href="managetags.php">Manage Tags</a></li>
      </ul>
    </nav>

    <main role="main" class="main-content">
      <section id="dashboard" class="section-content">
        <div class="container">
          <div class="banner-container">
            <img class="banner-img active" src="https://gsqms.infoadvance.com.ph/wp-content/uploads/2022/08/Blue-and-Yellow-Modern-Artisan-Parties-and-Celebrations-X-Frame-Banner-2048x512.jpg" alt="Banner Image 1">
            <img class="banner-img" src="https://www.dilg.gov.ph/images/slider/dilg_slider_2024430_d734f3ce05.jpg" alt="Banner Image 2">
            <img class="banner-img" src="https://www.dilg.gov.ph/images/slider/dilg_slider_2024411_6ff24c04e1.jpg" alt="Banner Image 3">

            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>

            <div class="dots-container">
              <span class="dot active" onclick="currentSlide(1)"></span>
              <span class="dot" onclick="currentSlide(2)"></span>
              <span class="dot" onclick="currentSlide(3)"></span>
            </div>
          </div>

          <div class="content-section">
            <h2>DILG Secretary</h2>
            <div class="director-info">
              <img src="https://tnt.abante.com.ph/wp-content/uploads/2024/10/jonvicc-768x473.jpg" id="img" class="director-img">
              <div class="director-details">
                <h3>DILG Secretary Juanito Victor "Jonvic" Catibayan Remulla Jr.</h3>
                <p>Juanito Victor "Jonvic" Catibayan Remulla Jr. born October 23, 1967 is a Filipino politician serving as the secretary of the Interior and Local Government since 2024. He served as the governor of Cavite from 2019 to 2024 and from 2010 to 2016, and had previously served as vice governor and as a member of the Cavite Provincial Board. He is a son of former governor Juanito Remulla Sr. and sibling of fellow politicians Gilbert and Jesus Crispin Remulla.
                Remulla entered politics in 1995, when he won as board member of the second district of Cavite. Three years later, in 1998, he was elected as vice governor, a post he held for three terms and in 2010, he became governor. He became a governor again when he defeated former governor Ayong Maliksi in the 2019 elections.</p>
                <a href="#" class="read-more">Read more</a>
              </div>
            </div>
          </div>

          <div class="content-section">
            <h2>Quezon City Mayor</h2>
            <div class="director-info">
              <img src="https://talambuhay.net/wp-content/uploads/2023/03/ds.jpg" id="img" class="director-img">
              <div class="director-details">
                <h3>Quezon City Mayor Joy Belmonte</h3>
                <p>Joy Belmonte-Alimurung (born Maria Josefina Tanya Go Belmonte; March 15, 1970) is a Filipina politician who has served as the 11th mayor of Quezon City since 2019. A member of the local Serbisyo sa Bayan Party, Belmonte previously served as the vice mayor of Quezon City from 2010 to 2019 under her predecessor, Herbert Bautista.</p>
                <a href="#" class="read-more">Read more</a>
              </div>
            </div>
          </div>

          <div class="points-section">
            <h2>Your Points</h2>
            <div class="points" id="userPoints">Loading...</div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <!-- Modal for points notification -->
  <div id="pointsModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <p>You have gained 1 point for logging in today!</p>
    </div>
  </div>

  <?php
ob_end_flush(); // Send the output after everything is ready
?>


  <!-- Include jQuery -->
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
    var slideIndex = 1;
    showSlides(slideIndex);

    setInterval(function() {
      plusSlides(1);
    }, 2500);

    function plusSlides(n) {
      showSlides(slideIndex += n);
    }

    function currentSlide(n) {
      showSlides(slideIndex = n);
    }

    function showSlides(n) {
      var i;
      var slides = document.getElementsByClassName("banner-img");
      var dots = document.getElementsByClassName("dot");
      if (n > slides.length) {
        slideIndex = 1
      }
      if (n < 1) {
        slideIndex = slides.length
      }
      for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
      }
      for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
      }
      slides[slideIndex - 1].style.display = "block";
      dots[slideIndex - 1].className += " active";
    }

    $(document).ready(function() {
      $.ajax({
        url: 'upoints.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
          $('#userPoints').text(data.points);
          if (data.new_points) {
            $('#pointsModal').show();
          }
        },
        error: function() {
          $('#userPoints').text('Error loading points');
        }
      });

      // Modal close functionality
      $('.close').click(function() {
        $('#pointsModal').hide();
      });
    });
  </script>
</body>

</html>
