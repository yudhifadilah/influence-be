<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
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
    die(json_encode(["success" => false, "error" => "Koneksi database gagal: " . $conn->connect_error]));
}

$sql = "SELECT campaign_id, proof_path FROM campaign_proof";
$result = $conn->query($sql);

$proofs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $proofs[$row["campaign_id"]] = $row["proof_path"];
    }
}

echo json_encode($proofs);

$conn->close();
?>