<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to your To-do List!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px 50px; /* Reduced top padding to move content up */
            margin: 0;
            height: 100vh;
            background-color: #f4f4f4; /* Light gray background */
            color: #333; /* Darker text color for better readability */
            display: flex;
            flex-direction: column;
            justify-content: flex-start; /* Align items to the start (top) */
            align-items: center;
        }
        h1 {
            color: #FFFFFF;
            background-color: #007883;
            padding: 20px;
            border-radius: 8px;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Shadow effect */
            margin-bottom: 20px; /* Space below the heading */
        }
        .btn {
            display: inline-block;
            margin: 10px;
            padding: 12px 20px;
            background-color: #007883;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s; /* Transition for hover effects */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Shadow effect */
        }
        .btn:hover {
            background-color: #005f66;
            transform: translateY(-2px); /* Slight lift on hover */
        }
    </style>
</head>
<body>
    <h1>Welcome to Your To-do List!</h1>
    <p>Please log in or sign up to access your to-do list.</p>
    <a href="login.php" class="btn">Login</a>
    <a href="signup.php" class="btn">Sign Up</a>
</body>
</html>
