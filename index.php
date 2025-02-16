<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "starpowers";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
} else {
    echo json_encode(["message" => "Connected successfully to the database"]);
}

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'POST':
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'register':
                    registerUser();
                    break;
                case 'login':
                    loginUser();
                    break;
                case 'create_campaign':
                    createCampaign();
                    break;
                case 'create_article':
                    createArticle();
                    break;
                case 'upload_image':
                    uploadImage();
                    break;
            }
        }
        break;
    case 'GET':
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'influencers':
                    getInfluencers();
                    break;
                case 'notifications':
                    getNotifications();
                    break;
                case 'articles':
                    getArticles();
                    break;
            }
        }
        break;
    case 'PUT':
        if (isset($_GET['action']) && $_GET['action'] == 'update_article') {
            updateArticle();
        }
        break;
    case 'DELETE':
        if (isset($_GET['action']) && $_GET['action'] == 'delete_article') {
            deleteArticle();
        }
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function registerUser()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $full_name = $data['full_name'];
    $birth_date = $data['birth_date'];
    $gender = $data['gender'];
    $influencer_category = $data['influencer_category'];
    $phone_number = $data['phone_number'];
    $referral_code = $data['referral_code'];
    $ktp_number = $data['ktp_number'];
    $npwp_number = $data['npwp_number'];
    $instagram_link = $data['instagram_link'];
    $followers_count = $data['followers_count'];

    $sql = "INSERT INTO users (email, password, full_name, birth_date, gender, influencer_category, phone_number, referral_code, ktp_number, npwp_number, instagram_link, followers_count) VALUES ('$email', '$password', '$full_name', '$birth_date', '$gender', '$influencer_category', '$phone_number', '$referral_code', '$ktp_number', '$npwp_number', '$instagram_link', '$followers_count')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "User registered successfully!"]);
    } else {
        echo json_encode(["message" => "Error: " . $sql . "<br>" . $conn->error]);
    }
}

function loginUser()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'];
    $password = $data['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            echo json_encode(["message" => "Login successful", "user" => $user]);
        } else {
            echo json_encode(["message" => "Invalid Password"]);
        }
    } else {
        echo json_encode(["message" => "User not found"]);
    }
}

function createCampaign()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);

    // 🔍 Log untuk debugging
    error_log("Data diterima di backend: " . json_encode($data));

    $name = $data['name'] ?? null;
    $service_id = $data['service_id'] ?? null;
    $influencers = json_encode($data['influencers'] ?? []); // Simpan sebagai JSON
    $start_date = $data['start_date'] ?? null;
    $end_date = $data['end_date'] ?? null;
    $proposal_deadline = $data['proposal_deadline'] ?? null;
    $brief = $data['brief'] ?? null;
    $brand_id = $data['brand_id'] ?? null;

    if (!$brand_id) {
        echo json_encode(["success" => false, "error" => "Brand ID tidak ada"]);
        return;
    }

    // 🔍 Log untuk cek sebelum query dijalankan
    error_log("Query yang akan dijalankan: INSERT INTO campaigns (name, service_id, influencers, start_date, end_date, proposal_deadline, brief, brand_id) 
               VALUES ('$name', '$service_id', '$influencers', '$start_date', '$end_date', '$proposal_deadline', '$brief', '$brand_id')");

    $sql = "INSERT INTO campaigns (name, service_id, influencers, start_date, end_date, proposal_deadline, brief, brand_id) 
            VALUES ('$name', '$service_id', '$influencers', '$start_date', '$end_date', '$proposal_deadline', '$brief', '$brand_id')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Campaign created successfully!"]);
    } else {
        echo json_encode(["success" => false, "error" => "Error: " . $conn->error]);
    }
}

function getInfluencers()
{
    global $conn;
    $sql = "SELECT * FROM influencers";
    $result = $conn->query($sql);
    $influencers = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $influencers[] = $row;
        }
    }
    echo json_encode($influencers);
}

function getNotifications()
{
    global $conn;
    $sql = "SELECT * FROM notifications";
    $result = $conn->query($sql);
    $notifications = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
    }
    echo json_encode($notifications);
}

function createArticle()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    $title = $data['title'];
    $excerpt = $data['excerpt'];
    $content = $data['content'];
    $image = $data['image'];

    $sql = "INSERT INTO articles (title, excerpt, content, image) VALUES ('$title', '$excerpt', '$content', '$image')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Article created successfully!"]);
    } else {
        echo json_encode(["message" => "Error: " . $sql . "<br>" . $conn->error]);
    }
}


function getArticles()
{
    global $conn;
    $sql = "SELECT * FROM articles";
    $result = $conn->query($sql);
    $articles = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $articles[] = $row;
        }
    }
    echo json_encode($articles);
}

function updateArticle()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];
    $title = $data['title'];
    $excerpt = $data['excerpt'];
    $content = $data['content'];
    $image = $data['image'];

    $sql = "UPDATE articles SET title='$title', excerpt='$excerpt', content='$content', image='$image' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Article updated successfully!"]);
    } else {
        echo json_encode(["message" => "Error: " . $sql . "<br>" . $conn->error]);
    }
}

function deleteArticle()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    $sql = "DELETE FROM articles WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Article deleted successfully!"]);
    } else {
        echo json_encode(["message" => "Error: " . $sql . "<br>" . $conn->error]);
    }
}

function uploadImage()
{
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                echo json_encode(["message" => "File is successfully uploaded.", "filePath" => $dest_path]);
            } else {
                echo json_encode(["message" => "There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server."]);
            }
        } else {
            echo json_encode(["message" => "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions)]);
        }
    } else {
        echo json_encode(["message" => "There is no file uploaded or there was an upload error."]);
    }
}

$conn->close();
?>