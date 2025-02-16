<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
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
  $email = $conn->real_escape_string($_POST['email']);
  $password = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_DEFAULT);
  $full_name = $conn->real_escape_string($_POST['fullName']);
  $birth_date = $conn->real_escape_string($_POST['birthDate']);
  $gender = $conn->real_escape_string($_POST['gender']);
  $influencer_category = $conn->real_escape_string($_POST['influencerCategory']);
  $phone_number = $conn->real_escape_string($_POST['phoneNumber']);
  $referral_code = $conn->real_escape_string($_POST['referralCode']);
  $ktp_number = $conn->real_escape_string($_POST['ktpNumber']);
  $npwp_number = $conn->real_escape_string($_POST['npwpNumber']);
  $instagram_link = $conn->real_escape_string($_POST['instagramLink']);
  $followers_count = $conn->real_escape_string($_POST['followersCount']);
  $bank_account = $conn->real_escape_string($_POST['bankAccount']);
  $account_number = $conn->real_escape_string($_POST['accountNumber']);
  $province = $conn->real_escape_string($_POST['province']);
  $city = $conn->real_escape_string($_POST['city']);
  
  // Handle file upload
  $profile_picture = '';
  if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profilePicture"]["name"]);
    if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $target_file)) {
      $profile_picture = $target_file;
    } else {
      echo json_encode(["error" => "Error uploading file"]);
      exit();
    }
  }

  $sql = "INSERT INTO influencers (email, password, full_name, birth_date, gender, influencer_category, phone_number, referral_code, ktp_number, npwp_number, instagram_link, followers_count, profile_picture, bank_account, account_number, province, city) VALUES ('$email', '$password', '$full_name', '$birth_date', '$gender', '$influencer_category', '$phone_number', '$referral_code', '$ktp_number', '$npwp_number', '$instagram_link', '$followers_count', '$profile_picture', '$bank_account', '$account_number', '$province', '$city')";

  if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => "New influencer registered successfully"]);
  } else {
    echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
  }
}

if ($method == 'GET') {
  $sql = "SELECT * FROM influencers";
  $result = $conn->query($sql);
  $influencers = [];

  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $influencers[] = $row;
    }
  }

  echo json_encode($influencers);
}


if ($method == 'PUT') {
  $data = json_decode(file_get_contents("php://input"), true);
  $id = $conn->real_escape_string($data['id']);
  $email = $conn->real_escape_string($data['email']);
  $full_name = $conn->real_escape_string($data['fullName']);
  $birth_date = $conn->real_escape_string($data['birthDate']);
  $gender = $conn->real_escape_string($data['gender']);
  $influencer_category = $conn->real_escape_string($data['influencerCategory']);
  $phone_number = $conn->real_escape_string($data['phoneNumber']);
  $referral_code = $conn->real_escape_string($data['referralCode']);
  $ktp_number = $conn->real_escape_string($data['ktpNumber']);
  $npwp_number = $conn->real_escape_string($data['npwpNumber']);
  $instagram_link = $conn->real_escape_string($data['instagramLink']);
  $followers_count = $conn->real_escape_string($data['followersCount']);
  $bank_account = $conn->real_escape_string($data['bankAccount']);
  $account_number = $conn->real_escape_string($data['accountNumber']);
  $province = $conn->real_escape_string($data['province']);
  $city = $conn->real_escape_string($data['city']);

  $sql = "UPDATE influencers SET email='$email', full_name='$full_name', birth_date='$birth_date', gender='$gender', influencer_category='$influencer_category', phone_number='$phone_number', referral_code='$referral_code', ktp_number='$ktp_number', npwp_number='$npwp_number', instagram_link='$instagram_link', followers_count='$followers_count', bank_account='$bank_account', account_number='$account_number', province='$province', city='$city' WHERE id=$id";

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