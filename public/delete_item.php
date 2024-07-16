<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve the item name for confirmation message
    $getNameQuery = "SELECT title FROM items WHERE id = ?";
    $stmt = $conn->prepare($getNameQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $itemName = $row['title'];

        // Delete the item from the database
        $deleteQuery = "DELETE FROM items WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $message = "{$itemName} has been deleted.";
        } else {
            $message = "Error deleting item: " . $stmt->error;
        }
    } else {
        $message = "Item not found.";
    }
}

$conn->close();

// Redirect back to index.php after 3 seconds
header("refresh:3;url=main_processes.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Item</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Deleting Item...</h1>
        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
        <p>Redirecting back to the to-do list...</p>
    </div>
</body>
</html>
