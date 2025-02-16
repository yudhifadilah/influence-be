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

if ($method == 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);

  $action = isset($data['action']) ? $data['action'] : '';

  if ($action == 'create') {
    $influencer_id = isset($data['influencer_id']) ? $conn->real_escape_string($data['influencer_id']) : '';
    $service_name = isset($data['serviceName']) ? $conn->real_escape_string($data['serviceName']) : '';
    $price_per_post = isset($data['pricePerPost']) ? $conn->real_escape_string($data['pricePerPost']) : '';
    $description = isset($data['description']) ? $conn->real_escape_string($data['description']) : '';
    $duration = isset($data['duration']) ? $conn->real_escape_string($data['duration']) : '';

    if ($influencer_id && $service_name && $price_per_post && $description && $duration) {
      $sql = "INSERT INTO services (influencer_id, service_name, price_per_post, description, duration) VALUES ('$influencer_id', '$service_name', '$price_per_post', '$description', '$duration')";

      if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => "Service added successfully"]);
      } else {
        echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
      }
    } else {
      echo json_encode(["error" => "All fields are required"]);
    }
  } elseif ($action == 'update') {
    $id = isset($data['id']) ? $conn->real_escape_string($data['id']) : '';
    $service_name = isset($data['serviceName']) ? $conn->real_escape_string($data['serviceName']) : '';
    $price_per_post = isset($data['pricePerPost']) ? $conn->real_escape_string($data['pricePerPost']) : '';
    $description = isset($data['description']) ? $conn->real_escape_string($data['description']) : '';
    $duration = isset($data['duration']) ? $conn->real_escape_string($data['duration']) : '';

    if ($id && $service_name && $price_per_post && $description && $duration) {
      $sql = "UPDATE services SET service_name='$service_name', price_per_post='$price_per_post', description='$description', duration='$duration' WHERE id='$id'";

      if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => "Service updated successfully"]);
      } else {
        echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
      }
    } else {
      echo json_encode(["error" => "All fields are required"]);
    }
  } elseif ($action == 'delete') {
    $id = isset($data['id']) ? $conn->real_escape_string($data['id']) : '';

    if ($id) {
      $sql = "DELETE FROM services WHERE id='$id'";

      if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => "Service deleted successfully"]);
      } else {
        echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
      }
    } else {
      echo json_encode(["error" => "ID is required"]);
    }
  }
} elseif ($method == 'GET') {
  $sql = "SELECT * FROM services";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $services = [];
    while($row = $result->fetch_assoc()) {
      $services[] = $row;
    }
    echo json_encode(["services" => $services]);
  } else {
    echo json_encode(["services" => []]);
  }
}

$conn->close();
?>