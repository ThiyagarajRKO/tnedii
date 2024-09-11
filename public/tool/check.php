<?php
// Enable CORS
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

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data) && isset($data['title']) && isset($data['description'])) {
        $title = mysqli_real_escape_string($connection, $data['title']);
        $description = mysqli_real_escape_string($connection, $data['description']);
        $checkSql = "SELECT * FROM project WHERE title='$title' AND decrip='$description'";
        $checkResult = mysqli_query($connection, $checkSql);

        if (mysqli_num_rows($checkResult) > 0) {
            // If project with same title and description already exists, return an error message
            http_response_code(400);
            echo json_encode(["message" => "This project title and description are already saved"]);
            exit(); // Stop further execution
        } else {
            // If project doesn't exist, return success message
            http_response_code(200);
            echo json_encode(["message" => "Project title and description are available"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Title and description not provided"]);
    }
}

// Close MySQL connection
mysqli_close($connection);
?>
