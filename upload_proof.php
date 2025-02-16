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
    die(json_encode(["success" => false, "error" => "Koneksi database gagal: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['campaign_id']) && isset($_FILES['proof'])) {
        $campaign_id = $conn->real_escape_string($_POST['campaign_id']);

        $target_dir = "uploads/proof/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = basename($_FILES["proof"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ["jpg", "jpeg", "png", "gif", "pdf"];
        if (!in_array($file_type, $allowed_types)) {
            echo json_encode(["success" => false, "error" => "Format file tidak didukung."]);
            exit();
        }

        if (!move_uploaded_file($_FILES["proof"]["tmp_name"], $target_file)) {
            echo json_encode(["success" => false, "error" => "Gagal memindahkan file."]);
            exit();
        }

        // Insert ke database
        $sql = "INSERT INTO campaign_proof (campaign_id, proof_path) VALUES ('$campaign_id', '$target_file')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Bukti berhasil diunggah.", "fileUrl" => $target_file]);
        } else {
            echo json_encode(["success" => false, "error" => "Gagal menyimpan data ke database: " . $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Data tidak lengkap."]);
    }
}

$conn->close();
?>
