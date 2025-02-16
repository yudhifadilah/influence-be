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
  $data = json_decode(file_get_contents("php://input"), true);

  $bankType = $conn->real_escape_string($data['bankType']);
  $accountNumber = $conn->real_escape_string($data['accountNumber']);
  $accountHolder = $conn->real_escape_string($data['accountHolder']);

  $sql = "INSERT INTO bank_accounts (bank_type, account_number, account_holder) VALUES ('$bankType', '$accountNumber', '$accountHolder')";

  if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => "Detail rekening bank berhasil disubmit"]);
  } else {
    echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
  }
}

if ($method == 'GET') {
  $sql = "SELECT * FROM bank_accounts";
  $result = $conn->query($sql);
  $bankAccounts = [];

  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $bankAccounts[] = $row;
    }
  }

  echo json_encode($bankAccounts);
}

if ($method == 'PUT') {
  $data = json_decode(file_get_contents("php://input"), true);

  $id = $conn->real_escape_string($data['id']);
  $bankType = $conn->real_escape_string($data['bankType']);
  $accountNumber = $conn->real_escape_string($data['accountNumber']);
  $accountHolder = $conn->real_escape_string($data['accountHolder']);

  $sql = "UPDATE bank_accounts SET bank_type='$bankType', account_number='$accountNumber', account_holder='$accountHolder' WHERE id=$id";

  if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => "Detail rekening bank berhasil diperbarui"]);
  } else {
    echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
  }
}

if ($method == 'DELETE') {
  $id = $conn->real_escape_string($_GET['id']);

  $sql = "DELETE FROM bank_accounts WHERE id=$id";

  if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => "Detail rekening bank berhasil dihapus"]);
  } else {
    echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
  }
}

$conn->close();
?>