<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "starpowers";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Koneksi database gagal: " . $conn->connect_error]));
}

// Query untuk mengambil kampanye yang statusnya "Completed"
$sql = "SELECT id, name, start_date, end_date, proposal_deadline, influencers, brief FROM campaigns WHERE status = 'Completed'";

// Periksa apakah query berhasil dieksekusi
if (!$result = $conn->query($sql)) {
    echo json_encode(["success" => false, "error" => "Query gagal: " . $conn->error]);
    $conn->close();
    exit();
}

$completedCampaigns = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $completedCampaigns[] = $row;
    }
}

// Kembalikan data dalam format JSON
echo json_encode(["success" => true, "data" => $completedCampaigns]);

$conn->close();
?>
