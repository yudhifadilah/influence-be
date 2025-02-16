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

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);

  $influencer_id = isset($data['influencer_id']) ? $conn->real_escape_string($data['influencer_id']) : '';
  $oldPassword = isset($data['oldPassword']) ? $conn->real_escape_string($data['oldPassword']) : '';
  $newPassword = isset($data['newPassword']) ? $conn->real_escape_string($data['newPassword']) : '';

  if ($influencer_id && $oldPassword && $newPassword) {
    $sql = "SELECT password FROM influencers WHERE id='$influencer_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      if (password_verify($oldPassword, $row['password'])) {
        $newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE influencers SET password='$newPasswordHashed' WHERE id='$influencer_id'";

        if ($conn->query($sql) === TRUE) {
          echo json_encode(["success" => "Password changed successfully"]);
        } else {
          echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
        }
      } else {
        echo json_encode(["error" => "Password lama salah"]);
      }
    } else {
      echo json_encode(["error" => "User tidak ditemukan"]);
    }
  } else {
    echo json_encode(["error" => "Semua field harus diisi"]);
  }
}

$conn->close();
?>