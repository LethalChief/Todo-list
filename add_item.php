<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $user_id = $_SESSION['id'];

    // Insert the new item along with the user_id
     $sql = "INSERT INTO items (title, description, priority, due_date, user_id) VALUES ('$title', '$description', '$priority', '$due_date', '$user_id')";

    if ($conn->query($sql) === TRUE) {
        header("Location: main_processes.php"); // Redirect to the main page after successful insertion
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
