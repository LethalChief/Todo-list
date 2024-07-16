<?php
include 'db.php';

$message = ''; // Initialize message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $nickname = $_POST['nickname'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt the password

    $sql = "INSERT INTO users (username, nickname, password) VALUES ('$username', '$nickname', '$password')";

    if ($conn->query($sql) === TRUE) {
        $message = "User created successfully! Redirecting to login page...";
        header("refresh:3;url=login.php"); // Redirect to login page after 3 seconds
        exit; // Exit to prevent further HTML from being processed
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
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
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        h1 {
            color: #ffffff;
            background-color: #007883;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .input-field {
            width: calc(100% - 22px); /* Adjust width to fit within the container */
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .input-field-small {
            width: calc(100% - 22px); /* Adjust width to fit within the container */
            padding: 8px;
        }
        .message {
            margin: 15px 0;
            color: #ff0000;
        }
        .btn {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #337937;
        }
        .form-footer {
            margin-top: 10px;
        }
        .form-footer a {
            color: #007883;
            text-decoration: none;
        }
        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sign Up</h1>
        <?php if (!empty($message)) { echo "<p class='message'>$message</p>"; } ?>
        <form action="signup.php" method="post">
            <input type="text" name="username" class="input-field" placeholder="Username" required>
            <input type="password" name="password" class="input-field" placeholder="Password" required>
            <input type="text" name="nickname" class="input-field-small" placeholder="Nickname">
            <button class="btn" type="submit">Sign Up</button>
        </form>
        <div class="form-footer">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>