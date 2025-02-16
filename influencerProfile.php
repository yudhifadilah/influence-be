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
  $email = $conn->real_escape_string($data['email']);
  $password = password_hash($conn->real_escape_string($data['password']), PASSWORD_DEFAULT);
  $full_name = $conn->real_escape_string($data['full_name']);
  $birth_date = $conn->real_escape_string($data['birth_date']);
  $gender = $conn->real_escape_string($data['gender']);
  $influencer_category = $conn->real_escape_string($data['influencer_category']);
  $phone_number = $conn->real_escape_string($data['phone_number']);
  $referral_code = $conn->real_escape_string($data['referral_code']);
  $ktp_number = $conn->real_escape_string($data['ktp_number']);
  $npwp_number = $conn->real_escape_string($data['npwp_number']);
  $instagram_link = $conn->real_escape_string($data['instagram_link']);
  $followers_count = $conn->real_escape_string($data['followers_count']);
  $profile_picture = $conn->real_escape_string($data['profile_picture']);
  $bank_account = $conn->real_escape_string($data['bank_account']);
  $account_number = $conn->real_escape_string($data['account_number']);
  $province = $conn->real_escape_string($data['province']);
  $city = $conn->real_escape_string($data['city']);

  $sql = "INSERT INTO influencers (email, password, full_name, birth_date, gender, influencer_category, phone_number, referral_code, ktp_number, npwp_number, instagram_link, followers_count, profile_picture, bank_account, account_number, province, city) VALUES ('$email', '$password', '$full_name', '$birth_date', '$gender', '$influencer_category', '$phone_number', '$referral_code', '$ktp_number', '$npwp_number', '$instagram_link', '$followers_count', '$profile_picture', '$bank_account', '$account_number', '$province', '$city')";

  if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => "New influencer registered successfully"]);
  } else {
    echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
  }
}

if ($method == 'GET') {
  if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM influencers WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $influencer = $result->fetch_assoc();
      if ($influencer['profile_picture']) {
        $influencer['profile_picture'] =  $influencer['profile_picture'];
      }
      echo json_encode($influencer);
    } else {
      echo json_encode(["error" => "Influencer not found"]);
    }
  } else {
    $sql = "SELECT * FROM influencers";
    $result = $conn->query($sql);
    $influencers = [];

    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        if ($row['profile_picture']) {
          $row['profile_picture'] = $row['profile_picture'];
        }
        $influencers[] = $row;
      }
    }

    echo json_encode($influencers);
  }
}

if ($method == 'PUT') {
  parse_str(file_get_contents("php://input"), $data);
  $id = $conn->real_escape_string($data['id']);
  $email = $conn->real_escape_string($data['email']);
  $full_name = $conn->real_escape_string($data['full_name']);
  $birth_date = $conn->real_escape_string($data['birth_date']);
  $gender = $conn->real_escape_string($data['gender']);
  $influencer_category = $conn->real_escape_string($data['influencer_category']);
  $phone_number = $conn->real_escape_string($data['phone_number']);
  $referral_code = $conn->real_escape_string($data['referral_code']);
  $ktp_number = $conn->real_escape_string($data['ktp_number']);
  $npwp_number = $conn->real_escape_string($data['npwp_number']);
  $instagram_link = $conn->real_escape_string($data['instagram_link']);
  $followers_count = $conn->real_escape_string($data['followers_count']);
  $bank_account = $conn->real_escape_string($data['bank_account']);
  $account_number = $conn->real_escape_string($data['account_number']);
  $province = $conn->real_escape_string($data['province']);
  $city = $conn->real_escape_string($data['city']);

  $profile_picture = $conn->real_escape_string($data['profile_picture']);
  if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
      $profile_picture = $target_file;
    } else {
      echo json_encode(["error" => "Error uploading file"]);
      exit();
    }
  }

  $sql = "UPDATE influencers SET email='$email', full_name='$full_name', birth_date='$birth_date', gender='$gender', influencer_category='$influencer_category', phone_number='$phone_number', referral_code='$referral_code', ktp_number='$ktp_number', npwp_number='$npwp_number', instagram_link='$instagram_link', followers_count='$followers_count', profile_picture='$profile_picture', bank_account='$bank_account', account_number='$account_number', province='$province', city='$city' WHERE id=$id";

  if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => "Influencer updated successfully"]);
  } else {
    echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
  }
}

if ($method == 'DELETE') {
  $id = $conn->real_escape_string($_GET['id']);

  $sql = "DELETE FROM influencers WHERE id=$id";

  if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => "Influencer deleted successfully"]);
  } else {
    echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
  }
}

$conn->close();
?>