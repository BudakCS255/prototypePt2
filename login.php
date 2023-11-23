<?php
session_start();

// If the user is already logged in, redirect to the index page
if (isset($_SESSION['user_type'])) {
    header("Location: index.php");
    exit();
}

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Database configuration
    $dbHost = 'localhost';
    $dbUser = 'afnan';
    $dbPass = 'john_wick_77';
    $dbName = 'mywebsite_images';

    // Create a database connection
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT type FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($type);
        $stmt->fetch();
        $_SESSION['username'] = $username;
        $_SESSION['user_type'] = $type;
        header("Location: index.php");
        exit();
    } else {
        $errorMessage = 'Invalid username or password.';
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if ($errorMessage): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
