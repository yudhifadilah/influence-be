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
} else {
  error_log("Connected successfully to the database");
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
  $data = json_decode(file_get_contents("php://input"), true);

  if (isset($data['action']) && $data['action'] === 'register') {
    $email = isset($data['email']) ? $conn->real_escape_string($data['email']) : null;
    $password = isset($data['password']) ? password_hash($conn->real_escape_string($data['password']), PASSWORD_BCRYPT) : null;
    $brandName = isset($data['brandName']) ? $conn->real_escape_string($data['brandName']) : null;
    $picName = isset($data['picName']) ? $conn->real_escape_string($data['picName']) : null;
    $picPhone = isset($data['picPhone']) ? $conn->real_escape_string($data['picPhone']) : null;
    $province = isset($data['province']) ? $conn->real_escape_string($data['province']) : null;
    $city = isset($data['city']) ? $conn->real_escape_string($data['city']) : null;
    $referralCode = isset($data['referralCode']) ? $conn->real_escape_string($data['referralCode']) : null;
    $name = isset($data['name']) ? $conn->real_escape_string($data['name']) : null;
    $address = isset($data['address']) ? $conn->real_escape_string($data['address']) : null;

    $sql = "INSERT INTO brands (email, password, brand_name, pic_name, pic_phone, province, city, referral_code, name, address) VALUES ('$email', '$password', '$brandName', '$picName', '$picPhone', '$province', '$city', '$referralCode', '$name', '$address')";

    if ($conn->query($sql) === TRUE) {
      echo json_encode(["success" => "Brand was created."]);
    } else {
      error_log("Error: " . $sql . "<br>" . $conn->error);
      echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
    }
  } elseif (isset($data['action']) && $data['action'] === 'login') {
    $email = $conn->real_escape_string($data['email']);
    $password = $conn->real_escape_string($data['password']);

    $sql = "SELECT * FROM brands WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      if (password_verify($password, $row['password'])) {
        echo json_encode(["success" => "Login successful.", "brand_id" => $row['id']]);
      } else {
        echo json_encode(["error" => "Invalid password."]);
      }
    } else {
      echo json_encode(["error" => "No user found with this email."]);
    }
  }
}

$conn->close();
?>