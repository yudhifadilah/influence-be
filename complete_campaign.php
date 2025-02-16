<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "starpowers";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read input data
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($data['campaign_id'])) {
        $campaign_id = $conn->real_escape_string($data['campaign_id']);
        
        // Update campaign status to 'Completed'
        $sql = "UPDATE campaigns SET status = 'Completed' WHERE id = '$campaign_id'";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Kampanye berhasil diselesaikan."]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "ID kampanye tidak ditemukan."]);
    }
}

$conn->close();
?>
