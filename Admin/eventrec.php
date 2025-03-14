<?php
// Include necessary libraries for AI integration
require '../lib/Gemini/Client.php'; // Manually include the Gemini client library
require '../conn.php'; // Include the database connection

use Gemini\Client;

// Initialize Gemini client
$client = new Client('AIzaSyDPb8HY9Ww2WxI5Qypg2LRx76RRJmWHJ9U');

// Fetch average age from the database
$averageAgeQuery = "SELECT AVG(age) as average_age FROM cy WHERE age IS NOT NULL";
$averageAgeResult = $conn->query($averageAgeQuery);
$averageAge = $averageAgeResult->fetch_assoc()['average_age'] ?? 0;

// Fetch all ages and tags
$dataQuery = "SELECT age, tags FROM cy WHERE age IS NOT NULL AND tags IS NOT NULL";
$dataResult = $conn->query($dataQuery);
$data = [];
$tagsFrequency = [];

while ($row = $dataResult->fetch_assoc()) {
    $age = (int) $row['age'];
    $tags = explode(',', $row['tags']); // Assuming tags are comma-separated

    // Build structured data
    $data[] = [
        'age' => $age,
        'tags' => $tags
    ];

    // Count tag occurrences
    foreach ($tags as $tag) {
        $tag = trim($tag);
        if (!empty($tag)) {
            $tagsFrequency[$tag] = ($tagsFrequency[$tag] ?? 0) + 1;
        }
    }
}

// Function to get event recommendations
function getEventRecommendations($conversationHistory, $profilingData, $tagsFrequency, $averageAge, $data)
{
    global $client;
    try {
        $prompt = "You are an event planner AI. Recommend events based on the given user conversation and profiling data:\n";
        
        foreach ($conversationHistory as $entry) {
            $prompt .= "User: " . $entry['user'] . "\nAI: " . $entry['ai'] . "\n";
        }

        // Append profiling data
        $prompt .= "User: " . end($conversationHistory)['user'] . "\n";
        if (!empty($profilingData['age']) && !empty($profilingData['tags'])) {
            $prompt .= "User's Age: " . $profilingData['age'] . "\n";
            $prompt .= "User's Interests: " . implode(", ", $profilingData['tags']) . "\n";
        } else {
            $prompt .= "User profiling data unavailable.\n";
        }

        // Include database insights
        $prompt .= "Community Stats:\n";
        $prompt .= "- Average Age: " . $averageAge . "\n";
        $prompt .= "- Most Common Interests: " . json_encode($tagsFrequency) . "\n";
        $prompt .= "- Sample Data: " . json_encode(array_slice($data, 0, 5)) . "\n"; // Send only a sample to avoid too much text
        
        $prompt .= "AI Recommendation:";

        // Send request to AI model
        $response = $client->generateContent($prompt);

        error_log("Full API Response: " . json_encode($response));

        if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            return $response['candidates'][0]['content']['parts'][0]['text'];
        } else {
            error_log("Unexpected response structure: " . json_encode($response));
            return "Sorry, I couldn't fetch recommendations at the moment.";
        }
    } catch (Exception $e) {
        error_log("Error fetching recommendations: " . $e->getMessage());
        return "Sorry, I couldn't fetch recommendations at the moment.";
    }
}

// Handle chatbot interaction
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputData = json_decode(file_get_contents('php://input'), true);
    $userInput = $inputData['userInput'] ?? '';
    $conversationHistory = $inputData['conversationHistory'] ?? [];
    
    // Assign actual fetched data
    $profilingData = [
        'age' => $averageAge, // Defaulting to average if no specific user data is available
        'tags' => array_keys($tagsFrequency) // Most common tags in the database
    ];
    
    if (!empty($userInput)) {
        $conversationHistory[] = ['user' => $userInput, 'ai' => ''];
        $recommendations = getEventRecommendations($conversationHistory, $profilingData, $tagsFrequency, $averageAge, $data);
        $conversationHistory[count($conversationHistory) - 1]['ai'] = $recommendations;
        echo json_encode(['recommendations' => $recommendations, 'conversationHistory' => $conversationHistory]);
    } else {
        echo json_encode(['recommendations' => 'Please provide some input.']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="assets/images/unified-lgu-logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>Event Recommendations</title>

    <!-- Simple bar CSS (for scrollbar)-->
    <link rel="stylesheet" href="css/simplebar.css">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="css/feather.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="css/main.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        /* Add some basic styling for the chatbot interface */
        #chatbox {
            width: 100%;
            height: 500px;
            border: 1px solid #ccc;
            padding: 10px;
            overflow-y: scroll;
            background-color: #f9f9f9;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .chat-message {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            white-space: pre-wrap; /* Preserve spaces and line breaks */
        }

        .chat-message.user {
            background-color: #e1f5fe;
            text-align: right;
        }

        .chat-message.ai {
            background-color: #fff9c4;
        }

        #userInput {
            width: calc(100% - 22px);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        #sendButton {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        #sendButton:hover {
            background-color: #0056b3;
        }

        #loadingIndicator {
            display: none;
            margin-top: 10px;
        }

        #downloadButton {
            padding: 10px 20px;
            border: none;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }

        #downloadButton:hover {
            background-color: #218838;
        }

        #quickChatButton {
            padding: 10px 20px;
            border: none;
            background-color: #ffc107;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }

        #quickChatButton:hover {
            background-color: #e0a800;
        }
    </style>
</head>

<body class="vertical light">
    <div class="wrapper">
        <?php include '/CYMS/Admin/sections/navbar.php'; ?>
        <?php include '/CYMS/Admin/sections/sidebar.php'; ?>

        <main role="main" class="main-content">
            <div class="content">
                <h2>Event Recommendations</h2>
                <div id="chatbox"></div>
                <input type="text" id="userInput" placeholder="Ask for event recommendations...">
                <button id="sendButton" onclick="sendMessage()">Send</button>
                <button id="quickChatButton" onclick="quickChat()">Get data and recommendation</button>
                <button id="downloadButton" onclick="downloadResponse()">Download Response</button>
                <div id="loadingIndicator">
                    <img src="assets/images/loading.gif" alt="Loading..." width="30">
                    <span id="loadingMessage">Loading...</span>
                </div>
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
    <script>
        let conversationHistory = [];
        let profilingData = {
            age: '', // Add logic to fetch age
            tags: [] // Add logic to fetch tags
        };

        let tagsFrequency = {}; // Add logic to fetch tags frequency
        let averageAge = 0; // Add logic to fetch average age
        let data = []; // Add logic to fetch all data

        function sendMessage() {
            const userInput = document.getElementById('userInput').value;
            if (userInput.trim() === '') {
                alert('Please enter a message.');
                return;
            }

            document.getElementById('loadingIndicator').style.display = 'block';
            document.getElementById('loadingMessage').innerText = 'Fetching recommendations, please wait...';
            fetch('eventrec.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        userInput,
                        conversationHistory,
                        profilingData,
                        tagsFrequency,
                        averageAge,
                        data
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const chatbox = document.getElementById('chatbox');
                    const aiResponse = data.recommendations.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                    chatbox.innerHTML += `<div class="chat-message user"><strong>You:</strong> ${userInput}</div>`;
                    chatbox.innerHTML += `<div class="chat-message ai"><strong>AI:</strong> ${aiResponse}</div>`;
                    document.getElementById('userInput').value = '';
                    chatbox.scrollTop = chatbox.scrollHeight;
                    conversationHistory = data.conversationHistory;
                    document.getElementById('loadingIndicator').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching recommendations.');
                    document.getElementById('loadingIndicator').style.display = 'none';
                });
        }

        function quickChat() {
            const userInput = "Recommend events and show data for recommendations";
            document.getElementById('loadingIndicator').style.display = 'block';
            document.getElementById('loadingMessage').innerText = 'Fetching recommendations, please wait...';
            fetch('eventrec.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        userInput,
                        conversationHistory,
                        profilingData,
                        tagsFrequency,
                        averageAge,
                        data
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const chatbox = document.getElementById('chatbox');
                    const aiResponse = data.recommendations.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                    chatbox.innerHTML += `<div class="chat-message user"><strong>You:</strong> ${userInput}</div>`;
                    chatbox.innerHTML += `<div class="chat-message ai"><strong>AI:</strong> ${aiResponse}</div>`;
                    chatbox.scrollTop = chatbox.scrollHeight;
                    conversationHistory = data.conversationHistory;
                    document.getElementById('loadingIndicator').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching recommendations.');
                    document.getElementById('loadingIndicator').style.display = 'none';
                });
        }

        function downloadResponse() {
            const chatbox = document.getElementById('chatbox').innerText;
            const blob = new Blob([chatbox], { type: 'text/plain' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'AI_Response.txt';
            link.click();
        }

        // Initialize Bootstrap dropdowns
        $(document).ready(function() {
            $('.dropdown-toggle').dropdown();
        });
    </script>
</body>

</html>