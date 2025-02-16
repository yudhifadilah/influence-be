<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");


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

$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'] ?? null;
$transaction_status = $data['transaction_status'] ?? null;

if (!$order_id) {
    echo json_encode(["error" => "Invalid order_id"]);
    exit;
}

$status_map = [
    'settlement' => 'success',
    'capture' => 'success',
    'pending' => 'pending',
    'expire' => 'failed',
    'cancel' => 'failed',
    'failure' => 'failed',
];

$new_status = $status_map[$transaction_status] ?? 'pending';

// Update status pembayaran di database
$stmt = $conn->prepare("UPDATE payments SET status = ? WHERE transaction_id = ?");
$stmt->bind_param("ss", $new_status, $order_id);
$stmt->execute();
$stmt->close();

echo json_encode(["message" => "Webhook received successfully"]);
$conn->close();
?>
