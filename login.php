<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nickname'] = $row['nickname']; // Store nickname in session
            
            //debug process
            echo "<pre>";
            print_r($_SESSION);
            echo "</pre>";
            
            header("Location: main_processes.php"); // Redirect to main to-do list section
            exit();
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "No user found with that username.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #ffffff;
            margin-bottom: 20px;
        }
        .input-field {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn {
            width: 100%;
            padding: 10px;
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
        .message {
            color: red;
            margin-bottom: 20px;
        }
        .alt-action {
            margin-top: 10px;
        }
        .alt-action a {
            color: #007883;
            text-decoration: none;
        }
        .alt-action a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Login</h1>
        <?php if (!empty($message)) { echo "<p class='message'>$message</p>"; } ?>
        <form action="login.php" method="post">
            <input class="input-field" type="text" name="username" placeholder="Username" required>
            <input class="input-field" type="password" name="password" placeholder="Password" required>
            <button class="btn" type="submit">Login</button>
        </form>
        <div class="alt-action">
            <p>Don't have an account? <a href="signup.php">Sign up!</a></p>
        </div>
    </div>
</body>
</html>
