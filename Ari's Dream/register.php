<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ari_dream";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die ("Connection failed: " . mysqli_connect_error());
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirmPassword'])
        && $_POST['password'] == $_POST['confirmPassword']) {

        $username = $_POST['username'];

        // Check if username already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $error = "Username is already taken.";
        } else {
            // Username is unique, proceed with registration
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password);

            if ($stmt->execute()) {
                // Redirect to login page after successful registration
                header("Location: login.php");
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $error = "Passwords do not match or missing information.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <script src="validation.js"></script>
</head>
<body>
<a href="landingpage.php" class="back-button">Back to Home</a>
<div>
    <h2>Register</h2>
    <form action="" method="POST" name="registerForm" onsubmit="return validateRegisterForm()">
        <div class="form-group">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="form-group">
            <input type="password" name="confirmPassword" class="form-control" placeholder="Confirm Password" required>
        </div>
        <div class="form-group">
            <button type="submit" class="form-button">Register</button>
        </div>
    </form>
    <a href="login.php" class="register-link">Already have an account? Login here</a>
</div>

<div>
    <?php
    if (!empty($error)) {
        echo '<div class="error-message">' . $error . '</div>';
    }
    if (!empty($success)) {
        echo '<div class="success-message">' . $success . '</div>';
    }
    ?>
</div>
</body>
</html>
