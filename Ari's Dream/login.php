<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ari_dream";

// Connects to the mysql db using the credentials above^
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loginUser']) && isset($_POST['loginPass'])) {
    $username = $_POST['loginUser'];
    $stmt = $conn->prepare("SELECT userID, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($_POST['loginPass'], $row['password'])) {
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['username'] = $username;
            $_SESSION['userID'] = $row['userID'];

            session_regenerate_id();
            header("Location: index.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Username not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <script src="validation.js"></script>
</head>
<body>
<a href="landingpage.php" class="back-button">Back to Home</a>
<div>
    <h2>Login</h2>
    <form action="" method="POST" name="loginForm" onsubmit="return validateLoginForm()">
        <div class="form-group">
            <input type="text" name="loginUser" class="form-control" placeholder="Username" required><br>
        </div>
        <div class="form-group">
            <input type="password" name="loginPass" class="form-control" placeholder="Password" required><br>
        </div>
        <div class="form-group">
            <input type="submit" class="form-button" value="Login">
        </div>
    </form>
    <?php
    if (!empty($error)) {
        echo '<div class="error-message">' . $error . '</div>';
    }
    ?>
    <a href="register.php" class="register-link">Don't have an account? Register here</a>
</div>
</body>
</html>
