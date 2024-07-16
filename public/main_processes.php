<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Ensure the nickname is used if it's set and not empty, otherwise use the username
$username = isset($_SESSION['nickname']) ? htmlspecialchars($_SESSION['nickname']) : htmlspecialchars($_SESSION['username']);

include 'db.php';

$user_id = $_SESSION['id']; // Correct session variable
$sql = "SELECT * FROM items WHERE user_id = '$user_id'"; // Use $user_id instead of $id
$result = $conn->query($sql);
if ($result === false) {
    die("Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
            text-align: center;
        }

        .nickname {
            font-size: 24px;
            font-weight: bold;
            color: #007883;
            margin-bottom: 20px;
        }

        .table-container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative; /* Added position relative */
        }

        .customize-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #023162;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .customize-button:hover {
            background-color: #011c3a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007883;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .styled-button, .red-button {
            display: inline-block;
            margin: 10px;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .styled-button {
            background-color: #4CAF50;
        }

        .styled-button:hover {
            background-color: #337937;
        }

        .red-button {
            background-color: red;
        }

        .red-button:hover {
            background-color: darkred;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const savedCustomizations = JSON.parse(localStorage.getItem('customizations'));
            if (savedCustomizations) {
                document.querySelector('h1').style.backgroundColor = savedCustomizations.pageHeaderColor;
                document.querySelector('h1').style.color = savedCustomizations.pageHeaderFontColor; // Apply font color

                document.querySelectorAll('th').forEach(th => {
                    th.style.backgroundColor = savedCustomizations.tableHeaderColor;
                    th.style.color = savedCustomizations.tableHeaderFontColor; // Apply font color
                });

                document.querySelectorAll('tr:nth-child(odd)').forEach(row => {
                    row.style.backgroundColor = savedCustomizations.tableRowOddColor;
                    row.style.color = savedCustomizations.tableRowOddFontColor; // Apply font color
                });

                document.querySelectorAll('tr:nth-child(even)').forEach(row => {
                    row.style.backgroundColor = savedCustomizations.tableRowEvenColor;
                    row.style.color = savedCustomizations.tableRowEvenFontColor; // Apply font color
                });
            }
        });
    </script>
</head>
<body>
    <div class="table-container">
        <button onclick="window.location.href='customize.php'" class="customize-button">Customize</button>

         <h1>To-Do List</h1>

        <div class="nickname">Welcome, <?php echo htmlspecialchars($_SESSION['nickname']) ?>!</div>

        <div style="text-align: center; margin-top: 20px;">
            <button class="styled-button" onclick="window.location.href='add_item_page.php'">Add New Item</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Time Added</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                    $priority_class = '';
                    switch ($row['priority']) {
                        case 'high':
                            $priority_class = 'high-priority';
                            break;
                        case 'medium':
                            $priority_class = 'medium-priority';
                            break;
                        case 'low':
                            $priority_class = 'low-priority';
                            break;
                        case 'none':
                            $priority_class = 'no-priority';
                            break;
                        default:
                            $priority_class = 'no-priority';
                            break;
                    }

                        $formatted_timestamp = date('F j, Y, g:i a', strtotime($row['time_added']));
                        $formatted_due_date = !empty($row['due_date']) && $row['due_date'] !== '0000-00-00 00:00:00' ? date('F j, Y, g:i a', strtotime($row['due_date'])) : 'None';

                        echo "<tr class='$priority_class'>";
                        echo "<td>{$formatted_timestamp}</td>";
                        echo "<td>{$row['title']}</td>";
                        echo "<td>{$row['description']}</td>";
                        echo "<td>{$row['priority']}</td>";
                        echo "<td>{$formatted_due_date}</td>";
                        echo "<td><a href='edit_item.php?id={$row['id']}'>Edit</a></td>";
                        echo "<td><a href='delete_item.php?id={$row['id']}'>Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No to-do items found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <form action="logout.php" method="post" style="margin-top: 20px;">
            <button type="submit" class="red-button">Logout</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
