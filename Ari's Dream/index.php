<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ari_dream";

// Connect to the database
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch user posts
$posts = [];
$stmt = $conn->prepare("SELECT * FROM content WHERE userID= ? ORDER BY timestamp DESC");
$stmt->bind_param("i", $_SESSION['userID']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        array_push($posts, $row);
    }
}
$stmt->close();
$conn->close();

// Handle logout
if (isset($_POST['logout'])) {
    // Unset all session values
    $_SESSION = array();
    session_destroy();
    header('Location: landingpage.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
    <form action="" class="logout"method="POST">
        <input type="submit" name="logout" value="Logout">
    </form>
    <a href="createpost.php" class="fixed-button">Create a New Post</a>
</div>
<?php if(empty($posts)): ?>
    <p class="new-user">No posts yet. Create your first post!</p>
<?php else: ?>
    <?php foreach($posts as $post): ?>
        <div class="post-container">
            <p><?= htmlspecialchars($post['caption']) ?></p>
            <?php
            // Explode the string into an array of image paths
            $imagePaths = explode(',', $post['imagePath']);
            foreach ($imagePaths as $path) {
                // Display each image
                echo '<img src="' . htmlspecialchars($path) . '" class="post-image">';
            }
            ?>
            <p><?= htmlspecialchars($post['timestamp']) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
