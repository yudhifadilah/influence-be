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

switch ($method) {
  case 'GET':
    $category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
    if ($category) {
      $sql = "SELECT * FROM faqs WHERE category='$category'";
    } else {
      $sql = "SELECT * FROM faqs";
    }
    $result = $conn->query($sql);
    $faqs = [];
    while ($row = $result->fetch_assoc()) {
      $faqs[] = $row;
    }
    echo json_encode($faqs);
    break;

  case 'POST':
    $data = json_decode(file_get_contents("php://input"), true);
    $category = $conn->real_escape_string($data['category']);
    $question = $conn->real_escape_string($data['question']);
    $answer = $conn->real_escape_string($data['answer']);

    $sql = "INSERT INTO faqs (category, question, answer) VALUES ('$category', '$question', '$answer')";
    if ($conn->query($sql) === TRUE) {
      echo json_encode(["success" => "New FAQ created successfully"]);
    } else {
      echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
    }
    break;

  case 'PUT':
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $conn->real_escape_string($data['id']);
    $category = $conn->real_escape_string($data['category']);
    $question = $conn->real_escape_string($data['question']);
    $answer = $conn->real_escape_string($data['answer']);

    $sql = "UPDATE faqs SET category='$category', question='$question', answer='$answer' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
      echo json_encode(["success" => "FAQ updated successfully"]);
    } else {
      echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
    }
    break;

  case 'DELETE':
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $conn->real_escape_string($data['id']);

    $sql = "DELETE FROM faqs WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
      echo json_encode(["success" => "FAQ deleted successfully"]);
    } else {
      echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
    }
    break;
}

$conn->close();
?>