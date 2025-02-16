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
    if (isset($_GET['id'])) {
      $id = $_GET['id'];
      $sql = "SELECT * FROM articles WHERE id=$id";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        $article = $result->fetch_assoc();
        $article['image'] = 'http://localhost/star-1/backend/' . $article['image'];
        echo json_encode($article);
      } else {
        echo json_encode(null);
      }
    } else {
      $sql = "SELECT * FROM articles";
      $result = $conn->query($sql);
      $articles = [];
      while ($row = $result->fetch_assoc()) {
        $row['image'] = 'http://localhost/star-1/backend/' . $row['image'];
        $articles[] = $row;
      }
      echo json_encode($articles);
    }
    break;

  case 'POST':
    $title = $_POST['title'];
    $excerpt = $_POST['excerpt'];
    $content = $_POST['content'];
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
      $target_dir = "uploads/";
      $target_file = $target_dir . basename($_FILES["image"]["name"]);
      if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image = $target_file;
      } else {
        echo json_encode(["error" => "Error uploading file"]);
        exit;
      }
    }

    $sql = "INSERT INTO articles (title, excerpt, content, image) VALUES ('$title', '$excerpt', '$content', '$image')";
    if ($conn->query($sql) === TRUE) {
      echo json_encode(["success" => "New record created successfully"]);
    } else {
      echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
    }
    break;

  case 'PUT':
    parse_str(file_get_contents("php://input"), $_PUT);
    $id = $_PUT['id'];
    $title = $_PUT['title'];
    $excerpt = $_PUT['excerpt'];
    $content = $_PUT['content'];
    $image = $_PUT['image'];

    $sql = "UPDATE articles SET title='$title', excerpt='$excerpt', content='$content', image='$image' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
      echo json_encode(["success" => "Record updated successfully"]);
    } else {
      echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
    }
    break;

  case 'DELETE':
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['id'];

    $sql = "DELETE FROM articles WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
      echo json_encode(["success" => "Record deleted successfully"]);
    } else {
      echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
    }
    break;
}

$conn->close();
?>
