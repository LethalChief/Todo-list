<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customize Interface</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    .form-container {
        max-width: 800px;
        margin: auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
    }

    .form-group {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 15px;
    }

    .form-group label {
        grid-column: span 2;
        margin-bottom: 5px;
    }

    .preview {
        margin: 20px;
        padding: 20px;
        border: 1px solid #ccc;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group input[type="color"] {
        width: 100%;
        height: 40px;
        padding: 0;
        border: none;
        cursor: pointer;
    }

    .form-group button {
        padding: 15px 20px; /* Increased padding */
        font-size: 16px; /* Increased font size */
        background-color: #007883;
        color: white;
        border: none;
        cursor: pointer;
    }

    .form-group button:hover {
        background-color: #005f66;
    }

    .save-button {
        background-color: #4CAF50 !important;
        color: white;
        padding: 15px 20px; /* Increased padding */
        font-size: 16px; /* Increased font size */
        border: none;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .save-button:hover {
        background-color: #337937 !important;
    }

    .button-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .button-container .form-group {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .button-container .form-group button {
        width: 50%; /* Adjust the width as needed */
        margin-bottom: 10px;
    }
</style>
</head>
<body>

    <?php
    session_start();
    if (!isset($_SESSION['id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['id'];

    include 'db.php';

    //default colors
    $defaultColors = [
        'page_header_color' => '#007883',
        'page_header_font_color' => '#FFFFFF',// White
        'table_header_color' => '#007883', 
        'table_header_font_color' => '#FFFFFF', 
        'table_row_odd_color' => '#7fedf3', 
        'table_row_odd_font_color' => '#000000',// Black
        'table_row_even_color' => '#FFFFFF', 
        'table_row_even_font_color' => '#000000'
    ];

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['reset'])) {
            $sql = "DELETE FROM user_customizations WHERE user_id = '$user_id'";
            $conn->query($sql);
            header("Location: customize.php");
            exit();
        } else {
            $pageHeaderColor = $_POST['page-header-color'];
            $pageHeaderFontColor = $_POST['page-header-font-color'];
            $tableHeaderColor = $_POST['table-header-color'];
            $tableHeaderFontColor = $_POST['table-header-font-color'];
            $tableRowOddColor = $_POST['table-row-odd-color'];
            $tableRowOddFontColor = $_POST['table-row-odd-font-color'];
            $tableRowEvenColor = $_POST['table-row-even-color'];
            $tableRowEvenFontColor = $_POST['table-row-even-font-color'];

            $sql = "REPLACE INTO user_customizations (user_id, page_header_color, page_header_font_color, table_header_color, table_header_font_color, table_row_odd_color, table_row_odd_font_color, table_row_even_color, table_row_even_font_color)
                    VALUES ('$user_id', '$pageHeaderColor', '$pageHeaderFontColor', '$tableHeaderColor', '$tableHeaderFontColor', '$tableRowOddColor', '$tableRowOddFontColor', '$tableRowEvenColor', '$tableRowEvenFontColor')";
            $conn->query($sql);

            header("Location: main_processes.php");
            exit();
        }
    }

    $sql = "SELECT * FROM user_customizations WHERE user_id = '$user_id'";
    $result = $conn->query($sql);
    $customizations = $result->fetch_assoc();
    ?>

   <h1>Customize Interface</h1>
<div class="form-container">
    <form id="customize-form" method="post">
        <div class="form-group">
            <div>
                <label for="page-header-color">Page Header Color</label>
                <input type="color" id="page-header-color" name="page-header-color" value="<?php echo $customizations['page_header_color'] ?? '#0000FF'; ?>">
            </div>
            <div>
                <label for="table-header-color">Table Header Color</label>
                <input type="color" id="table-header-color" name="table-header-color" value="<?php echo $customizations['table_header_color'] ?? '#0000FF'; ?>">
            </div>
            <div>
                <label for="table-row-odd-color">Table Row Odd Numbers Color</label>
                <input type="color" id="table-row-odd-color" name="table-row-odd-color" value="<?php echo $customizations['table_row_odd_color'] ?? '#FFFFFF'; ?>">
            </div>
            <div>
                <label for="table-row-even-color">Table Row Even Numbers Color</label>
                <input type="color" id="table-row-even-color" name="table-row-even-color" value="<?php echo $customizations['table_row_even_color'] ?? '#0000FF'; ?>">
            </div>
            <div>
                <label for="page-header-font-color">Page Header Font Color</label>
                <input type="color" id="page-header-font-color" name="page-header-font-color" value="<?php echo $customizations['page_header_font_color'] ?? '#FFFFFF'; ?>">
            </div>
            <div>
                <label for="table-header-font-color">Table Header Font Color</label>
                <input type="color" id="table-header-font-color" name="table-header-font-color" value="<?php echo $customizations['table_header_font_color'] ?? '#FFFFFF'; ?>">
            </div>
            <div>
                <label for="table-row-odd-font-color">Table Row Odd Numbers Font Color</label>
                <input type="color" id="table-row-odd-font-color" name="table-row-odd-font-color" value="<?php echo $customizations['table_row_odd_font_color'] ?? '#000000'; ?>">
            </div>
            <div>
                <label for="table-row-even-font-color">Table Row Even Numbers Font Color</label>
                <input type="color" id="table-row-even-font-color" name="table-row-even-font-color" value="<?php echo $customizations['table_row_even_font_color'] ?? '#FFFFFF'; ?>">
            </div>
        </div>
    </form>
</div>
<div class="preview" id="preview">
    <h1>To-Do List Preview</h1>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Priority</th>
                <th>Time Added</th>
                <th>Due Date</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sample Title 1</td>
                <td>Sample Description 1</td>
                <td>High</td>
                <td>Sample Time 1</td>
                <td>Sample Due Date 1</td>
                <td>Edit</td>
                <td>Delete</td>
            </tr>
            <tr>
                <td>Sample Title 2</td>
                <td>Sample Description 2</td>
                <td>Medium</td>
                <td>Sample Time 2</td>
                <td>Sample Due Date 2</td>
                <td>Edit</td>
                <td>Delete</td>
            </tr>
        </tbody>
    </table>
</div>
    <div class="button-container">
        <div class="form-group">
            <button type="submit" form="customize-form" class="save-button">Save Customizations</button>
        </div>
        <div class="form-group">
            <button type="button" onclick="resetToDefault()">Reset to Default</button>
        </div>
        <div class="form-group">
            <button type="button" onclick="returnToTodoList()">Return to Todo-List</button>
        </div>
    </div>


    <script>

    document.addEventListener('DOMContentLoaded', function() {
        const saveButton = document.querySelector('.save-button');

        saveButton.addEventListener('mouseover', function() {
            saveButton.style.backgroundColor = '#337937';
        });

        saveButton.addEventListener('mouseout', function() {
            saveButton.style.backgroundColor = '#4CAF50';
        });
    });

        function discardChanges() {
            window.location.href = 'main_processes.php';
        }

        function returnToTodoList() {
            if (confirm('Would you like to return to your todo-list? Your changes will not be saved.')) {
                window.location.href = 'main_processes.php';
            }
        }

        function resetToDefault() {
            if (confirm('Are you sure you want to reset to default settings?')) {
                const defaultColors = <?php echo json_encode($defaultColors); ?>;

                // Set form values to default colors
                document.getElementById('page-header-color').value = defaultColors.page_header_color;
                document.getElementById('page-header-font-color').value = defaultColors.page_header_font_color;
                document.getElementById('table-header-color').value = defaultColors.table_header_color;
                document.getElementById('table-header-font-color').value = defaultColors.table_header_font_color;
                document.getElementById('table-row-odd-color').value = '<?php echo $defaultColors['table_row_odd_color']; ?>';
                document.getElementById('table-row-odd-font-color').value = defaultColors.table_row_odd_color;
                document.getElementById('table-row-odd-font-color').value = defaultColors.table_row_odd_font_color;
                document.getElementById('table-row-even-color').value = defaultColors.table_row_even_color;
                document.getElementById('table-row-even-font-color').value = defaultColors.table_row_even_font_color;
        // Update preview immediately
                document.getElementById('customize-form').dispatchEvent(new Event('input'));
    }
}

        document.getElementById('customize-form').addEventListener('input', function() {
            const pageHeaderColor = document.getElementById('page-header-color').value;
            const pageHeaderFontColor = document.getElementById('page-header-font-color').value;
            const tableHeaderColor = document.getElementById('table-header-color').value;
            const tableHeaderFontColor = document.getElementById('table-header-font-color').value;
            const tableRowOddColor = document.getElementById('table-row-odd-color').value;
            const tableRowOddFontColor = document.getElementById('table-row-odd-font-color').value;
            const tableRowEvenColor = document.getElementById('table-row-even-color').value;
            const tableRowEvenFontColor = document.getElementById('table-row-even-font-color').value;

            const previewHeader = document.querySelector('#preview h1');
            const previewTableHeaders = document.querySelectorAll('#preview th');
            const previewOddRows = document.querySelectorAll('#preview tr:nth-child(odd)');
            const previewEvenRows = document.querySelectorAll('#preview tr:nth-child(even)');

            previewHeader.style.backgroundColor = pageHeaderColor;
            previewHeader.style.color = pageHeaderFontColor;

            previewTableHeaders.forEach(th => {
                th.style.backgroundColor = tableHeaderColor;
                th.style.color = tableHeaderFontColor;
            });

            previewOddRows.forEach(row => {
                row.style.backgroundColor = tableRowOddColor;
                row.style.color = tableRowOddFontColor;
            });

            previewEvenRows.forEach(row => {
                row.style.backgroundColor = tableRowEvenColor;
                row.style.color = tableRowEvenFontColor;
            });
        });

        document.getElementById('customize-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const customizations = {
                pageHeaderColor: document.getElementById('page-header-color').value,
                pageHeaderFontColor: document.getElementById('page-header-font-color').value,
                tableHeaderColor: document.getElementById('table-header-color').value,
                tableHeaderFontColor: document.getElementById('table-header-font-color').value,
                tableRowOddColor: document.getElementById('table-row-odd-color').value,
                tableRowOddFontColor: document.getElementById('table-row-odd-font-color').value,
                tableRowEvenColor: document.getElementById('table-row-even-color').value,
                tableRowEvenFontColor: document.getElementById('table-row-even-font-color').value,
            };

            localStorage.setItem('customizations', JSON.stringify(customizations));
            alert('Customizations saved!');

            window.location.href = 'main_processes.php'; // Redirect to the main page
        });

        document.addEventListener('DOMContentLoaded', function() {
            const savedCustomizations = JSON.parse(localStorage.getItem('customizations'));
            if (savedCustomizations) {
                document.getElementById('page-header-color').value = savedCustomizations.pageHeaderColor;
                document.getElementById('page-header-font-color').value = savedCustomizations.pageHeaderFontColor;
                document.getElementById('table-header-color').value = savedCustomizations.tableHeaderColor;
                document.getElementById('table-header-font-color').value = savedCustomizations.tableHeaderFontColor;
                document.getElementById('table-row-odd-color').value = savedCustomizations.tableRowOddColor;
                document.getElementById('table-row-odd-font-color').value = savedCustomizations.tableRowOddFontColor;
                document.getElementById('table-row-even-color').value = savedCustomizations.tableRowEvenColor;
                document.getElementById('table-row-even-font-color').value = savedCustomizations.tableRowEvenFontColor;

                document.getElementById('customize-form').dispatchEvent(new Event('input'));
            }
        });
    </script>
</body>
</html>
