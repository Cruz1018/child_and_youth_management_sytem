<?php
session_start();
if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    echo "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background-color: #f4f4f9;
            }
            .confirmation-box {
                background: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                text-align: center;
            }
            .confirmation-box h2 {
                margin-bottom: 20px;
                color: #333;
            }
            .confirmation-box button {
                padding: 10px 20px;
                margin: 5px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
            }
            .confirm-btn {
                background-color: #28a745;
                color: white;
            }
            .cancel-btn {
                background-color: #dc3545;
                color: white;
            }
        </style>
    </head>
    <body>
        <div class='confirmation-box'>
            <h2>Are you sure you want to log out?</h2>
            <button class='confirm-btn' onclick=\"window.location.href='logout.php?confirm=yes';\">Yes</button>
            <button class='cancel-btn' onclick=\"window.location.href='landing_page.php';\">No</button>
        </div>
    </body>
    </html>";
    exit();
}
session_destroy();
header("Location: ../login.php");
exit();
?>
