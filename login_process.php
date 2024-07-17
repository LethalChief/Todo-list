<?php
session_start();

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $nickname = $_POST['nickname']; // Retrieve nickname from form
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Insert the new user into the database
    $sql = "INSERT INTO users (username, nickname, password) VALUES ('$username', '$nickname', '$password')";
    if ($conn->query($sql) === TRUE) {
        echo "New user created successfully.";
        header("Location: login.php"); // Redirect to login page
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Sign Up</h1>
    <form action="login_process.php" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="nickname" placeholder="Nickname" required> <!-- Add nickname input field -->
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign Up</button>
    </form>
</body>
</html>
