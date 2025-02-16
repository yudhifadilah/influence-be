<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require __DIR__ . '/../vendor/autoload.php';

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

use Midtrans\Config;
use Midtrans\Snap;

Config::$serverKey = 'SB-Mid-server-47L_viV9aBGcFzEmAkrVDKwO'; // Ganti dengan Server Key Anda
Config::$isProduction = false;
Config::$isSanitized = true;
Config::$is3ds = true;

function generateOrderID() {
    return "ORDER-" . rand(100000, 999999);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $brand_id = $data['brand_id'] ?? '';
    $service_id = $data['service_id'] ?? '';

    if (empty($brand_id) || empty($service_id)) {
        echo json_encode(["error" => "BrandID dan ServiceID diperlukan"]);
        exit;
    }

    // Ambil harga layanan
    $stmt = $conn->prepare("SELECT price_per_post FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $service = $result->fetch_assoc();
    $stmt->close();

    if (!$service) {
        echo json_encode(["error" => "Service tidak ditemukan"]);
        exit;
    }

    // Ambil data pembeli
    $stmt = $conn->prepare("SELECT pic_name, email, pic_phone FROM brands WHERE id = ?");
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $brand = $result->fetch_assoc();
    $stmt->close();

    if (!$brand) {
        echo json_encode(["error" => "Brand tidak ditemukan"]);
        exit;
    }

    $order_id = generateOrderID();
    $amount = round($service['price_per_post'] * 1.1); // Harga termasuk pajak
    $status = "pending";

    // Simpan transaksi ke database
    $stmt = $conn->prepare("INSERT INTO payments (transaction_id, brand_id, service_id, amount, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("siids", $order_id, $brand_id, $service_id, $amount, $status);
    $stmt->execute();
    $stmt->close();

    // Buat transaksi Midtrans
    $transaction_details = [
        'order_id' => $order_id,
        'gross_amount' => $amount,
    ];

    $customer_details = [
        'first_name' => $brand['pic_name'],
        'email' => $brand['email'],
        'phone' => $brand['pic_phone'],
    ];

    $snap_payload = [
        'transaction_details' => $transaction_details,
        'customer_details' => $customer_details,
    ];

    try {
        $snapToken = Snap::getSnapToken($snap_payload);
        echo json_encode([
            'message' => 'Payment initiated',
            'order_id' => $order_id,
            'transaction_id' => $snapToken,
            'payment_url' => "https://app.sandbox.midtrans.com/snap/v2/vtweb/" . $snapToken,
        ]);
    } catch (Exception $e) {
        echo json_encode(["error" => "Gagal membuat transaksi: " . $e->getMessage()]);
    }
}

$conn->close();
?>
