<?php
// Enable CORS
date_default_timezone_set('Asia/Kolkata');

header("Access-Control-Allow-Origin: http://projectverifier.helloindiasolutions.com/");
header("Access-Control-Allow-Methods: POST"); // Allow only POST method
header("Access-Control-Allow-Headers: Content-Type");

// MySQL database configuration
$host = "localhost";
$user = "u602093072_projectver";
$password = "Infintech123!@#";
$database = "u602093072_projectver";


// Connect to MySQL database
$connection = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle POST request to save links
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['links'])) {
        $linksData = $data['links'];
        $insertionErrors = [];

        // Begin transaction
        mysqli_autocommit($connection, false);
        $doe = date('Y-m-d H:i:s'); // Change to correct date format for MySQL DATETIME

        foreach ($linksData as $linkData) {
            $link = mysqli_real_escape_string($connection, $linkData['link']);
            $matchingPercentage = floatval($linkData['matchingPercentage']);
            $remarks = mysqli_real_escape_string($connection, $linkData['remarks']);
            $title = mysqli_real_escape_string($connection, $linkData['title']);
            $desc = mysqli_real_escape_string($connection, $linkData['description']);

            // Insert link data into the database
            $sql = "INSERT INTO project (link, percent, remarks, title, decrip, doe) VALUES ('$link', $matchingPercentage, '$remarks', '$title', '$desc', '$doe')";

            if (!mysqli_query($connection, $sql)) {
                $insertionErrors[] = mysqli_error($connection);
            }
        }

        if (empty($insertionErrors)) {
            mysqli_commit($connection);
            http_response_code(200);
            echo json_encode(["message" => "Links saved successfully"]);
        } else {
            mysqli_rollback($connection);
            http_response_code(500);
            echo json_encode([
                "message" => "Some links could not be saved",
                "errors" => $insertionErrors
            ]);
        }

        mysqli_autocommit($connection, true);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "No links provided"]);
    }
}

// Handle GET request to fetch all projects
elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $perPage = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
    $date = isset($_GET['date']) ? $_GET['date'] : '';

    $offset = ($page - 1) * $perPage;
    $dateCondition = $date ? "DATE(doe) = '$date'" : '1';

    // Query to get total count of projects with date filter
    $countResult = mysqli_query($connection, "SELECT COUNT(*) as total FROM project WHERE $dateCondition");
    if ($countResult) {
        $totalRows = mysqli_fetch_assoc($countResult)['total'];
    } else {
        http_response_code(500);
        die(json_encode(["message" => "Error fetching total rows: " . mysqli_error($connection)]));
    }

    // Query to get duplicate count
    $duplicateCountResult = mysqli_query($connection, "SELECT COUNT(*) as duplicates FROM (SELECT link FROM project GROUP BY link HAVING COUNT(link) > 1) AS dup_links");
    if ($duplicateCountResult) {
        $duplicateRows = mysqli_fetch_assoc($duplicateCountResult)['duplicates'];
    } else {
        http_response_code(500);
        die(json_encode(["message" => "Error fetching duplicate rows: " . mysqli_error($connection)]));
    }

    // Query to select data from the project table with date filter
    $sql = "SELECT * FROM project WHERE $dateCondition LIMIT $offset, $perPage";
    $result = mysqli_query($connection, $sql);

    if ($result) {
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        http_response_code(200);
        header('Content-Type: application/json'); // Set the content type to JSON
        echo json_encode([
            "data" => $data,
            "total" => $totalRows,
            "duplicates" => $duplicateRows,
        ]); // Return data in JSON format
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error fetching data: " . mysqli_error($connection)]);
    }
}

// Close MySQL connection
mysqli_close($connection);
?>

