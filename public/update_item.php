<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $priority = $conn->real_escape_string($_POST['priority']);
    $due_date = $conn->real_escape_string($_POST['due_date']);

    $sql = "UPDATE items SET title='$title', description='$description', priority='$priority', due_date='$due_date' WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: main_processes.php"); // Redirect to the main page after successful update
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
