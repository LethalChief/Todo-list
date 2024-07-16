<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM items WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
    } else {
        echo 'Item not found.';
        exit;
    }
} else {
    echo 'Invalid item ID.';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit To-Do Item</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Add your styles here -->
    <style>
        body {
            font-family: Arial, sans-serif;
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
        <h1>Edit To-Do Item</h1>
        <form action="update_item.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($item['description']); ?>">
            </div>
            <div class="form-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="high" <?php echo $item['priority'] == 'high' ? 'selected' : ''; ?>>High</option>
                    <option value="medium" <?php echo $item['priority'] == 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="low" <?php echo $item['priority'] == 'low' ? 'selected' : ''; ?>>Low</option>
                </select>
            </div>
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="datetime-local" id="due_date" name="due_date" value="<?php echo htmlspecialchars($item['due_date']); ?>">
            </div>
            <div class="form-group">
                <button type="submit">Update Item</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
