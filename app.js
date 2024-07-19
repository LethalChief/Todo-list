const express = require("express");
const session = require("express-session");
const bodyParser = require("body-parser");
const bcrypt = require("bcrypt");
const path = require("path");
const db = require("./db"); // Import the database configuration

require("dotenv").config();

const app = express();
const port = process.env.PORT || 3000;

// Use environment variables from .env
/*const db = mysql.createConnection({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASS,
  database: process.env.DB_NAME,
});*/

app.use(
  session({
    secret: process.env.SESSION_SECRET,
    resave: false,
    saveUninitialized: true,
    cookie: { maxAge: 1000000 },
  })
);

app.set("view engine", "ejs");
app.set("views", path.join(__dirname, "views"));

app.use(express.static(path.join(__dirname, "public")));
app.use(bodyParser.urlencoded({ extended: true }));
app.use(
  session({
    secret: "your_secret_key",
    resave: false,
    saveUninitialized: true,
    cookie: { maxAge: 1000000 },
  })
);

function checkAuth(req, res, next) {
  if (!req.session.userId) {
    return res.redirect("/login");
  }
  next();
}

// Routes

app.get("/", (req, res) => {
  res.render("index");
});

app.get("/login", (req, res) => {
  res.render("login", { message: "" });
});

app.post("/login", (req, res) => {
  const { username, password } = req.body;

  db.query(
    "SELECT * FROM users WHERE username = ?",
    [username],
    (err, results) => {
      if (err) {
        console.error(err);
        res.render("login", { message: "Database error" });
      } else if (results.length > 0) {
        const user = results[0];
        if (bcrypt.compareSync(password, user.password)) {
          req.session.userId = user.id;
          req.session.username = user.username;
          req.session.nickname = user.nickname;
          res.redirect("/main_processes");
        } else {
          res.render("login", { message: "Invalid username or password" });
        }
      } else {
        res.render("login", { message: "No user found with that username" });
      }
    }
  );
});

app.get("/signup", (req, res) => {
  res.render("signup", { message: "" });
});

app.post("/signup", (req, res) => {
  const { username, nickname, password } = req.body;
  const hashedPassword = bcrypt.hashSync(password, 10);

  db.query(
    "INSERT INTO users (username, nickname, password) VALUES (?, ?, ?)",
    [username, nickname, hashedPassword],
    (err, result) => {
      if (err) {
        console.error(err);
        res.render("signup", { message: "Database error" });
      } else {
        res.render("signup", {
          message: "User created successfully! Redirecting to login page...",
        });
      }
    }
  );
});

app.post("/logout", (req, res) => {
  req.session.destroy((err) => {
    if (err) {
      console.error(err);
      res.send("Logout error");
    } else {
      res.redirect("/");
    }
  });
});

app.get("/delete_item/:id", (req, res) => {
  const id = req.params.id;

  const getNameQuery = "SELECT title FROM items WHERE id = ?";
  db.query(getNameQuery, [id], (err, result) => {
    if (err) {
      console.error(err);
      res.send("Database error");
    } else if (result.length > 0) {
      const itemName = result[0].title;

      const deleteQuery = "DELETE FROM items WHERE id = ?";
      db.query(deleteQuery, [id], (err, result) => {
        if (err) {
          console.error(err);
          res.send("Error deleting item");
        } else {
          res.render("delete_item", {
            message: `${itemName} has been deleted.`,
          });
        }
      });
    } else {
      res.render("delete_item", { message: "Item not found." });
    }
  });
});

app.get("/main_processes", (req, res) => {
  if (!req.session.userId) {
    return res.redirect("/login");
  }

  const userId = req.session.userId;
  const username = req.session.nickname || req.session.username;

  const sql = "SELECT * FROM items WHERE user_id = ?";
  db.query(sql, [userId], (err, results) => {
    if (err) {
      console.error(err);
      return res.send("Database error");
    }

    res.render("main_processes", { username, items: results });
  });
});

app.get("/add_item_page", (req, res) => {
  if (!req.session.userId) {
    return res.redirect("/login");
  }
  res.render("add_item_page");
});

app.post("/add_item", (req, res) => {
  if (!req.session.userId) {
    return res.redirect("/login");
  }

  const { title, description, priority, due_date } = req.body;
  const userId = req.session.userId;

  const sql =
    "INSERT INTO items (title, description, priority, due_date, user_id) VALUES (?, ?, ?, ?, ?)";
  db.query(
    sql,
    [title, description, priority, due_date, userId],
    (err, result) => {
      if (err) {
        console.error(err);
        res.send("Database error");
      } else {
        res.redirect("/main_processes");
      }
    }
  );
});

app.get("/edit_item/:id", (req, res) => {
  const id = req.params.id;

  const sql = "SELECT * FROM items WHERE id = ?";
  db.query(sql, [id], (err, result) => {
    if (err) {
      console.error(err);
      res.send("Database error");
    } else if (result.length > 0) {
      res.render("edit_item", { item: result[0] });
    } else {
      res.send("Item not found");
    }
  });
});

app.post("/update_item", (req, res) => {
  const { id, title, description, priority, due_date } = req.body;

  const sql =
    "UPDATE items SET title = ?, description = ?, priority = ?, due_date = ? WHERE id = ?";
  db.query(sql, [title, description, priority, due_date, id], (err, result) => {
    if (err) {
      console.error(err);
      res.send("Database error");
    } else {
      res.redirect("/main_processes");
    }
  });
});

app.get("/customize", (req, res) => {
  if (!req.session.userId) {
    return res.redirect("/login");
  }

  const userId = req.session.userId;
  const defaultColors = {
    page_header_color: "#007883",
    page_header_font_color: "#FFFFFF",
    table_header_color: "#007883",
    table_header_font_color: "#FFFFFF",
    table_row_odd_color: "#7fedf3",
    table_row_odd_font_color: "#000000",
    table_row_even_color: "#FFFFFF",
    table_row_even_font_color: "#000000",
  };

  const sql = "SELECT * FROM user_customizations WHERE user_id = ?";
  db.query(sql, [userId], (err, result) => {
    if (err) {
      console.error(err);
      res.send("Database error");
    } else {
      const customizations = result.length > 0 ? result[0] : {};
      res.render("customize", { customizations, defaultColors });
    }
  });
});

app.post("/customize", (req, res) => {
  if (!req.session.userId) {
    return res.redirect("/login");
  }

  const userId = req.session.userId;
  if (req.body.reset) {
    const sql = "DELETE FROM user_customizations WHERE user_id = ?";
    db.query(sql, [userId], (err) => {
      if (err) {
        console.error(err);
        res.send("Database error");
      } else {
        res.redirect("/customize");
      }
    });
  } else {
    const {
      pageHeaderColor,
      pageHeaderFontColor,
      tableHeaderColor,
      tableHeaderFontColor,
      tableRowOddColor,
      tableRowOddFontColor,
      tableRowEvenColor,
      tableRowEvenFontColor,
    } = req.body;

    const sql = `REPLACE INTO user_customizations (user_id, page_header_color, page_header_font_color, table_header_color, table_header_font_color, table_row_odd_color, table_row_odd_font_color, table_row_even_color, table_row_even_font_color)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`;

    db.query(
      sql,
      [
        userId,
        pageHeaderColor,
        pageHeaderFontColor,
        tableHeaderColor,
        tableHeaderFontColor,
        tableRowOddColor,
        tableRowOddFontColor,
        tableRowEvenColor,
        tableRowEvenFontColor,
      ],
      (err) => {
        if (err) {
          console.error(err);
          res.send("Database error");
        } else {
          res.redirect("/main_processes");
        }
      }
    );
  }
});

app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});

module.exports = app; // Export the app for testing
