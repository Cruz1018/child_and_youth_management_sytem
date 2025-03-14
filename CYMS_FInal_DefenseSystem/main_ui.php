<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="assets/images/unified-lgu-logo.png">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <title>System UI Template</title>

  <!-- Simple bar CSS (for scvrollbar)-->
  <link rel="stylesheet" href="css/simplebar.css">
  <!-- Fonts CSS -->
  <link
    href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
    rel="stylesheet">
  <!-- Icons CSS -->
  <link rel="stylesheet" href="css/feather.css">
  <!-- App CSS -->
  <link rel="stylesheet" href="css/main.css">
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

  <style>
    .avatar-initials {
      width: 165px;
      height: 165px;
      border-radius: 50%;
      display: flex;
      margin-left: 8px;
      justify-content: center;
      align-items: center;
      font-size: 50px;
      font-weight: bold;
      color: #fff;
    }

    .avatar-initials-min {
      width: 40px;
      height: 40px;
      background: #75e6da;
      border-radius: 50%;
      display: flex;
      margin-left: 8px;
      justify-content: center;
      align-items: center;
      font-size: 14px;
      font-weight: bold;
      color: #fff;
    }

    .upload-icon {
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      cursor: pointer;
      font-size: 24px;
      color: #fff;
      opacity: 0;
      transition: opacity 0.3s ease-in-out;
      background-color: #333;
      padding: 10px;
      border-radius: 50%;
      z-index: 1;
    }

    .avatar-img:hover .upload-icon {
      opacity: 1;
    }

    .avatar-img {
      position: relative;
      transition: background-color 0.3s ease-in-out;
    }

    .avatar-img:hover {
      background-color: #a0f0e6;
    }

    .content {
      width: 100%;
      max-width: 1500px;
      margin: 10px auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 40px;
    }

    table th,
    table td {
      padding: 15px;
      text-align: center;
    }

    table th {
      background-color: rgb(90, 183, 230);
      color: #1b4965;
      padding: 40px;
      border-radius: 30px;
      box-shadow: 0 15px 50px rgba(0, 0, 0, 0.01);
      text-align: center
    }
  </style>
</head>

<body class="vertical  light">
  <div class="wrapper">
    <?php include 'navbar.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main role="main" class="main-content">
      <!--For Notification header naman ito-->
      <div class="modal fade modal-notif modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="defaultModalLabel">Notifications</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="list-group list-group-flush my-n3">
                <div class="col-12 mb-4">
                  <div class="alert alert-success alert-dismissible fade show" role="alert" id="notification">
                    <img class="fade show" src="../../assets/images/unified-lgu-logo.png" width="35" height="35">
                    <strong
                      style="font-size:12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                      onclick="removeNotification()">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                </div> <!-- /. col -->
                <div id="no-notifications" style="display: none; text-align:center; margin-top:10px;">No notifications
                </div>
              </div> <!-- / .list-group -->
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-block" onclick="clearAllNotifications()">Clear
                All</button>
            </div>
          </div>
        </div>
      </div>

      <!--YOUR CONTENTHERE-->
      <div class="content">
        <h2>Admin Account</h2>
      </div>

      <!--TABLE-->
      <div class="table">
        <table>
          <thead>
            <tr>
              <th>Registered Profiles</th>
              <th>Event Participants</th>
              <th>Community Programs</th>
              <th>Active Volunteers</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>200</td>
              <td>100</td>
              <td>30</td>
              <td>70</td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>

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
</body>

</html>