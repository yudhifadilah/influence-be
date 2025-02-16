<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
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

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
  $sql = "SELECT * FROM campaigns WHERE status = 'approved'";
  $result = $conn->query($sql);
  $campaigns = [];
  while ($row = $result->fetch_assoc()) {
    $campaigns[] = $row;
  }
  echo json_encode($campaigns);
}

$conn->close();
?>