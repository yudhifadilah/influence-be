<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
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

if ($method == 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);
  $email = $conn->real_escape_string($data['email']);
  $password = $conn->real_escape_string($data['password']);

  $sql = "SELECT id, password FROM influencers WHERE email = '$email'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
      echo json_encode(["success" => true, "influencer_id" => $row['id']]);
    } else {
      echo json_encode(["success" => false, "error" => "Invalid password"]);
    }
  } else {
    echo json_encode(["success" => false, "error" => "No user found with this email"]);
  }
}

$conn->close();
?>