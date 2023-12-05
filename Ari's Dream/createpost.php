<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ari_dream";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';
$success = '';
$imagePaths = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = isset($_POST['caption']) ? trim($_POST['caption']) : '';

    // Check if either caption or image is provided
    if (empty($caption) && !isset($_FILES['image']['name'])) {
        $error = "Please provide a caption or an image.";
    } else {
        // Handle file upload if image is provided
        if (isset($_FILES['image']['name'][0]) && $_FILES['image']['error'][0] == 0) {
            // Loop through each file
            foreach ($_FILES['image']['name'] as $index => $name) {
                if ($_FILES['image']['error'][$index] == 0) {
                    $allowedTypes = ['image/jpeg'];
                    $fileType = $_FILES['image']['type'][$index];

                    if (in_array($fileType, $allowedTypes)) {
                        $imagePath = 'uploads/' . basename($_FILES['image']['name'][$index]);
                        if (move_uploaded_file($_FILES['image']['tmp_name'][$index], $imagePath)) {
                            $imagePaths[] = $imagePath;
                        } else {
                            $error = "Failed to upload image.";
                            break;
                        }
                    }
                }
            }
        }

        // Insert post into the database
        if (empty($error)) {
            $imagePathsString = implode(',', $imagePaths);
            $timestamp = date('Y-m-d H:i:s');
            $stmt = $conn->prepare("INSERT INTO content (userID, caption, imagePath, timestamp) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $_SESSION['userID'], $caption, $imagePathsString, $timestamp);
            if ($stmt->execute()) {
                $success = "Post created successfully.";
            } else {
                $error = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Post</title>
    <link rel="stylesheet" href="style.css">
    <script src="validation.js"></script>
</head>
<body>
<a href="index.php" class="go-back-link">Go Back</a>
<form action="" method="POST" enctype="multipart/form-data" class="create-post-form" name="createPostForm" onsubmit="return validateCreatePostForm()">
    <div>
        <textarea name="caption" placeholder="Enter your caption here" class="form-control"></textarea>
    </div>
    <div>
        <input type="file" name="image[]" class="form-control" multiple>
    </div>
    <div>
        <input type="submit" value="Create Post" class="form-button">
    </div>
</form>
<?php
if (!empty($error)) {
    echo '<div class="error-message">' . $error . '</div>';
}
if (!empty($success)) {
    echo '<div class="success-message">' . $success . '</div>';
}
?>
</body>
</html>
