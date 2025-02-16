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
  if (isset($_GET['action'])) {
    if ($_GET['action'] == 'services') {
      $sql = "SELECT * FROM services";
      $result = $conn->query($sql);
      $services = [];
      while ($row = $result->fetch_assoc()) {
        $services[] = $row;
      }
      echo json_encode($services);
    } elseif ($_GET['action'] == 'ongoing_campaigns') {
      $sql = "SELECT * FROM campaigns WHERE status = 'ongoing'";
      $result = $conn->query($sql);
      $campaigns = [];
      while ($row = $result->fetch_assoc()) {
        $campaigns[] = $row;
      }
      echo json_encode($campaigns);
    } elseif ($_GET['action'] == 'campaign_history') {
      if (isset($_GET['brand_id'])) {
          $brand_id = $conn->real_escape_string($_GET['brand_id']);
          $sql = "SELECT * FROM campaigns WHERE status = 'Completed' AND brand_id = '$brand_id'";
      } else {
          $sql = "SELECT * FROM campaigns WHERE status = 'Completed'";
      }
  
      $result = $conn->query($sql);
      $campaigns = [];
      while ($row = $result->fetch_assoc()) {
          $campaigns[] = $row;
      }
      echo json_encode($campaigns);
    
    } elseif ($_GET['action'] == 'campaigns') {
      $sql = "SELECT * FROM campaigns WHERE status = 'pending'";
      $result = $conn->query($sql);
      $campaigns = [];
      while ($row = $result->fetch_assoc()) {
        $campaigns[] = $row;
      }
      echo json_encode($campaigns);
    }
  }
}

if ($method == 'POST') {
  $data = json_decode(file_get_contents("php://input"), true);

  if (isset($data['action'])) {
    if ($data['action'] == 'approve') {
      $campaign_id = $conn->real_escape_string($data['campaign_id']);
      $sql = "UPDATE campaigns SET status = 'approved' WHERE id = '$campaign_id'";
      if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => "Campaign approved."]);
      } else {
        echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
      }
    } elseif ($data['action'] == 'reject') {
      $campaign_id = $conn->real_escape_string($data['campaign_id']);
      $sql = "UPDATE campaigns SET status = 'rejected' WHERE id = '$campaign_id'";
      if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => "Campaign rejected."]);
      } else {
        echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
      }
    }
  } else {
    $name = $conn->real_escape_string($data['name']);
    $service_id = $conn->real_escape_string($data['service_id']);
    $influencers = implode(',', array_map('intval', $data['influencers'])); // Format influencers as a comma-separated string of integers
    $start_date = $conn->real_escape_string($data['start_date']);
    $end_date = $conn->real_escape_string($data['end_date']);
    $proposal_deadline = $conn->real_escape_string($data['proposal_deadline']);
    $brief = $conn->real_escape_string($data['brief']);
    $brand_id = $conn->real_escape_string($data['brand_id']);

    $sql = "INSERT INTO campaigns (name, service_id, influencers, start_date, end_date, proposal_deadline, brief, brand_id, status) VALUES ('$name', '$service_id', '$influencers', '$start_date', '$end_date', '$proposal_deadline', '$brief', '$brand_id', 'pending')";

    if ($conn->query($sql) === TRUE) {
      echo json_encode(["success" => "Campaign created."]);
    } else {
      echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
    }
  }
}

$conn->close();
?>