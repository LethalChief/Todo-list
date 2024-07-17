<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date']; // Get due date from the form

    // Insert the new item along with the user_id and due date
    $sql = "INSERT INTO items (user_id, title, description, priority, due_date) VALUES ('$user_id', '$title', '$description', '$priority', '$due_date')";

    if ($conn->query($sql) === TRUE) {
        header("Location: main_processes.php"); // Redirect to the main page after successful insertion
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add To-Do Item</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            /*background-color: #f2f2f2; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;*/
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .form-group button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #337937;
        }
        .form-container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Add To-Do Item</h1>
        <form action="add_item.php" method="post">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" placeholder="Enter title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" placeholder="Enter description">
            </div>
            <div class="form-group">
                <label for="priority">Enter Priority</label>
                <select id="priority" name="priority">
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
            <div class="form-group">
                <label for="due_date">Select Due Date</label>
                <input type="datetime-local" id="due_date" name="due_date">
            </div>
            <div class="form-group">
                <button type="submit">Add Item</button>
            </div>
        <button onclick="window.location.href='main_processes.php'" class="customize-button">Return Home</button>
        </form>
    </div>
</body>
</html>