const mysql = require("mysql");

const db = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "",
  database: "todo_list",
});

db.connect((err) => {
  if (err) {
    throw err;
  }
  console.log("Connected to database");
});

const sql = `
  CREATE TABLE IF NOT EXISTS user_customizations (
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
  )
`;

db.query(sql, (err, result) => {
  if (err) {
    console.error("Error creating table:", err.message);
    return;
  }
  console.log("Connection established");
});

module.exports = db;
