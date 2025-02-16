<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
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

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    $sql = "SELECT c.id, c.name, c.status, REPLACE(c.influencers, '\"', '') AS influencer_id, i.full_name AS influencer_name
            FROM campaigns c
            JOIN influencers i ON REPLACE(c.influencers, '\"', '') = i.id";
    $result = $conn->query($sql);

    $notifications = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
    }

    echo json_encode($notifications);
    exit();
}

$conn->close();
?>