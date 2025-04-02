<?php
// Include necessary libraries for AI integration
require '../lib/Gemini/Client.php'; // Manually include the Gemini client library
require '../conn.php'; // Include the database connection

use Gemini\Client;

// Initialize Gemini client
$client = new Client('AIzaSyDPb8HY9Ww2WxI5Qypg2LRx76RRJmWHJ9U');

// Fetch tags from the 'user_tags' table
function fetchTagsFromDatabase($conn) {
    $tagsFrequency = [];
    $query = "SELECT tags FROM user_tags"; 
    $result = $conn->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $tags = explode(',', $row['tags']); // Assuming tags are stored as comma-separated values
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    // Increment tag count
                    $tagsFrequency[$tag] = ($tagsFrequency[$tag] ?? 0) + 1;
                }
            }
        }
    } else {
        error_log("Error fetching tags from database: " . $conn->error);
    }

    return $tagsFrequency;
}

// Fetch tags from the database
$tagsFrequency = fetchTagsFromDatabase($conn);

if (empty($tagsFrequency)) {
    error_log("No tags found in the 'user_tags' table.");
} else {
    error_log("Tags Frequency: " . json_encode($tagsFrequency)); // Debugging log
}

// Sort tags by frequency and get the top 5 most frequent tags
arsort($tagsFrequency);
$topTags = array_slice(array_keys($tagsFrequency), 0, 5); // Get top 5 most common tags

// Fetch API data for profiling
function fetchAPIData($url) {
    $response = file_get_contents($url);
    if ($response === false) {
        error_log("Error fetching API data from $url");
        return [];
    }
    $data = json_decode($response, true);
    return $data['data'] ?? []; // Adjusted to return the 'data' array
}

$apiData = fetchAPIData('https://backend-api-5m5k.onrender.com/api/resident');
$data = [];
$ages = [];

foreach ($apiData as $item) {
    $age = (int) ($item['age'] ?? 0); // Ensure age is fetched correctly
    $tags = explode(',', $item['tags'] ?? ''); // Assuming tags are comma-separated

    if ($age > 0) {
        $ages[] = $age;
    }

    if (!empty($tags)) {
        $data[] = [
            'age' => $age,
            'tags' => $tags
        ];
    }
}

// Calculate average age from API data
$averageAge = !empty($ages) ? array_sum($ages) / count($ages) : 0;

// Log fetched data for debugging
error_log("Average Age: $averageAge");
error_log("Sample Data: " . json_encode(array_slice($data, 0, 5)));

// Age group distribution data
$ageGroupDistribution = [
    ['group' => 'Under 1', 'population' => 25, 'percentage' => '2.10%'],
    ['group' => '1 to 4', 'population' => 86, 'percentage' => '7.21%'],
    ['group' => '5 to 9', 'population' => 91, 'percentage' => '7.63%'],
    ['group' => '10 to 14', 'population' => 79, 'percentage' => '6.63%'],
    ['group' => '15 to 19', 'population' => 85, 'percentage' => '7.13%'],
    ['group' => '20 to 24', 'population' => 106, 'percentage' => '8.89%'],
    ['group' => '25 to 29', 'population' => 98, 'percentage' => '8.22%']
];

// Function to get event recommendations
function getEventRecommendations($conversationHistory, $profilingData, $tagsFrequency, $averageAge, $data)
{
    global $client, $conn, $ageGroupDistribution; // Ensure $ageGroupDistribution is accessible
    try {
        $prompt = "You are an event planner AI. Recommend events based on the given user conversation and profiling data:\n";
        
        foreach ($conversationHistory as $entry) {
            $prompt .= "User: " . $entry['user'] . "\nAI: " . $entry['ai'] . "\n";
        }

        // Append profiling data
        $prompt .= "User: " . end($conversationHistory)['user'] . "\n";
        $prompt .= "User's Age: " . ($profilingData['age'] ?? "Unknown") . "\n";
        $prompt .= "User's Interests: " . (!empty($profilingData['tags']) ? implode(", ", $profilingData['tags']) : "No interests detected") . "\n";

        // Include database insights
        $prompt .= "Community Stats:\n";
        $prompt .= "- Average Age: " . $averageAge . "\n";
        $prompt .= "- Most Common Interests: " . json_encode($tagsFrequency) . "\n";
        $prompt .= "- Sample Data: " . json_encode(array_slice($data, 0, 5)) . "\n"; // Send only a sample to avoid too much text

        // Include age group distribution
        $prompt .= "Age Group Distribution:\n";
        foreach ($ageGroupDistribution as $group) {
            $prompt .= "- " . $group['group'] . ": " . $group['population'] . " (" . $group['percentage'] . ")\n";
        }

        $prompt .= "AI Recommendation:";

        // Send request to AI model
        $response = $client->generateContent($prompt);

        error_log("Full API Response: " . json_encode($response));

        if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            $aiResponse = $response['candidates'][0]['content']['parts'][0]['text'];

            // Save user input and AI response to the database
            $stmt = $conn->prepare("INSERT INTO ai_responses (user_input, ai_response) VALUES (?, ?)");
            $stmt->bind_param("ss", end($conversationHistory)['user'], $aiResponse);
            $stmt->execute();
            $stmt->close();

            return $aiResponse;
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
        'age' => $averageAge, // Use average age from API data
        'tags' => $topTags // Send only top 5 most common tags
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
</head>

<body class="vertical light">
    <div class="wrapper">
        <?php include 'sections/navbar.php'; ?>
        <?php include 'sections/sidebar.php'; ?>

        <main role="main" class="main-content">
            <div class="content">
                <h2 class="text-center mb-4">Event Recommendations</h2>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">AI Response</h5>
                    </div>
                    <div class="card-body" id="responseBox" style="min-height: 200px; transition: all 0.3s ease;">
                        <p id="aiResponse" class="text-muted text-center">Click a button below to get started.</p>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <button id="recommendButton" class="btn btn-warning mx-2 dynamic-btn" onclick="recommendData()">
                        <i class="fas fa-lightbulb"></i> Recommend Data
                    </button>
                    <button id="showDataButton" class="btn btn-info mx-2 dynamic-btn" onclick="showData()">
                        <i class="fas fa-database"></i> Show Data
                    </button>
                    <button id="createPlanButton" class="btn btn-secondary mx-2 dynamic-btn" onclick="createPlan()">
                        <i class="fas fa-calendar-alt"></i> Create a Plan
                    </button>
                </div>
                <div id="loadingIndicator" class="text-center mt-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p id="loadingMessage" class="text-muted mt-2">Processing your request...</p>
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

        function displayResponse(response) {
            const responseBox = document.getElementById('responseBox');
            const aiResponse = response.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            responseBox.style.opacity = 0; // Fade out
            setTimeout(() => {
                document.getElementById('aiResponse').innerHTML = aiResponse;
                responseBox.style.opacity = 1; // Fade in
            }, 300);
        }

        function toggleLoading(show) {
            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.style.display = show ? 'block' : 'none';
        }

        function recommendData() {
            const userInput = "Recommend events based on available data";
            toggleLoading(true);
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
                displayResponse(data.recommendations);
                toggleLoading(false);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching recommendations.');
                toggleLoading(false);
            });
        }

        function showData() {
            const userInput = "Show me the data you have";
            toggleLoading(true);
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
                displayResponse(data.recommendations);
                toggleLoading(false);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching data.');
                toggleLoading(false);
            });
        }

        function createPlan() {
            const userInput = "Create a plan based on the data";
            toggleLoading(true);
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
                displayResponse(data.recommendations);
                toggleLoading(false);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating a plan.');
                toggleLoading(false);
            });
        }

        // Add hover effects and animations for buttons
        document.querySelectorAll('.dynamic-btn').forEach(button => {
            button.addEventListener('mouseover', () => {
                button.style.transform = 'scale(1.1)';
                button.style.transition = 'transform 0.2s ease';
            });
            button.addEventListener('mouseout', () => {
                button.style.transform = 'scale(1)';
            });
        });

        // Initialize Bootstrap dropdowns
        $(document).ready(function() {
            $('.dropdown-toggle').dropdown();
        });
    </script>
</body>

</html>