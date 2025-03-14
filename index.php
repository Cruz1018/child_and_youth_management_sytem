<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CYMS - Empowering the Future</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #1a237e, #0d47a1);
            text-align: center;
            color: white;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
        }
        h1 {
            font-size: 3.5rem;
            margin-bottom: 10px;
        }
        p {
            font-size: 1.2rem;
            max-width: 800px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin-top: 20px;
            font-size: 1.2rem;
            color: white;
            background-color: #3949ab;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn:hover {
            background-color: #303f9f;
        }
        .features {
            margin-top: 50px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }
        .feature-box {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
        }
        .footer {
            margin-top: 50px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Children and Youth Management System</h1>
        <p>Empowering the youth through innovative programs, event recommendations, and community engagement.</p>
        <a href="login.php" class="btn"><i class="fas fa-sign-in-alt"></i> Login</a>
        
        <div class="features">
            <div class="feature-box">
                <h3>AI-Driven Event Recommendations</h3>
                <p>Personalized events based on children and youth profiles, ensuring meaningful participation.</p>
            </div>
            <div class="feature-box">
                <h3>Community Engagement</h3>
                <p>Interactive forums, group activities, and local event collaboration to boost involvement.</p>
            </div>
            <div class="feature-box">
                <h3>Volunteer Opportunities</h3>
                <p>Encouraging community members to take part in youth-focused programs and activities.</p>
            </div>
            <div class="feature-box">
                <h3>Performance Tracking</h3>
                <p>Monitor youth engagement, skills development, and participation history.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; 2025 CYMS. All rights reserved. | Contact Us: support@cyms.org</p>
        </div>
    </div>
</body>
</html>
