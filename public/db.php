<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "todo_app";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " .$conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS user_customizations (
    user_id INT(11) NOT NULL,
    page_header_color VARCHAR(7) DEFAULT '#FFFFFF',
    page_header_font_color VARCHAR(7) DEFAULT '#000000',
    table_header_color VARCHAR(7) DEFAULT '#FFFFFF',
    table_header_font_color VARCHAR(7) DEFAULT '#000000',
    table_row_odd_color VARCHAR(7) DEFAULT '#FFFFFF',
    table_row_odd_font_color VARCHAR(7) DEFAULT '#000000',
    table_row_even_color VARCHAR(7) DEFAULT '#FFFFFF',
    table_row_even_font_color VARCHAR(7) DEFAULT '#000000',
    PRIMARY KEY (user_id)
)";

if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}
?>