const express = require("express");
const session = require("express-session");
const bodyParser = require("body-parser");
const bcrypt = require("bcrypt");
const path = require("path");
const db = require("./db");

require("dotenv").config();

const app = express();
const port = process.env.PORT || 3000;

// Use environment variables from .env
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
app.use(bodyParser.json()); // To parse JSON bodies

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

app.get("/main_processes", checkAuth, (req, res) => {
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

app.get("/add_item_page", checkAuth, (req, res) => {
  res.render("add_item_page");
});

app.post("/add_item", checkAuth, (req, res) => {
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

app.get("/edit_item/:id", checkAuth, (req, res) => {
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

app.post("/update_item", checkAuth, (req, res) => {
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

app.get("/customize", checkAuth, (req, res) => {
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

app.post("/customize", checkAuth, (req, res) => {
  const userId = req.session.userId;
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
});

const moment = require("moment-timezone");

app.get("/api/events", checkAuth, (req, res) => {
  const userId = req.session.userId;
  const month = parseInt(req.query.month, 10);
  const year = parseInt(req.query.year, 10);
  const timeZone = req.query.timezone || "UTC";

  const startDate = moment
    .tz([year, month, 1], timeZone)
    .startOf("day")
    .toDate();
  const endDate = moment.tz([year, month, 1], timeZone).endOf("month").toDate();

  /*console.log(
    `Fetching events for user_id: ${userId}, startDate: ${startDate}, endDate: ${endDate}`
  );*/

  const sql = `
    SELECT title, due_date 
    FROM items 
    WHERE user_id = ? AND due_date BETWEEN ? AND ?
  `;
  db.query(sql, [userId, startDate, endDate], (err, results) => {
    if (err) {
      console.error(err);
      return res.status(500).send("Database error");
    }

    const events = results.map((event) => ({
      title: event.title,
      due_date: moment(event.due_date).tz(timeZone).format("YYYY-MM-DD"),
    }));

    //console.log("Database results:", results);
    res.json(events);
  });
});

app.get("/events", checkAuth, (req, res) => {
  const userId = req.session.userId;
  const date = req.query.date;
  const timeZone = req.query.timezone || "UTC";

  const sql = `
    SELECT title, due_date 
    FROM items 
    WHERE user_id = ? AND DATE(due_date) = ?
  `;
  db.query(sql, [userId, date], (err, results) => {
    if (err) {
      console.error(err);
      return res.status(500).send("Database error");
    }

    const events = results.map((event) => ({
      title: event.title,
      due_date: moment(event.due_date).tz(timeZone).toISOString(),
    }));

    res.json(events);
  });
});

app.get("/calendar", checkAuth, (req, res) => {
  const userId = req.session.userId;
  const month = parseInt(req.query.month, 10) || new Date().getMonth();
  const year = parseInt(req.query.year, 10) || new Date().getFullYear();

  const startDate = moment
    .tz([year, month, 1], "America/New_York")
    .startOf("day")
    .toDate();
  const endDate = moment
    .tz([year, month + 1, 0], "America/New_York")
    .endOf("day")
    .toDate();

  const sql = `
    SELECT title, due_date 
    FROM items 
    WHERE user_id = ? AND due_date BETWEEN ? AND ?
  `;
  db.query(sql, [userId, startDate, endDate], (err, results) => {
    if (err) {
      console.error(err);
      return res.send("Database error");
    }

    // Convert results to a simpler format
    const events = results.map((event) => ({
      title: event.title,
      due_date: moment(event.due_date)
        .tz("America/New_York")
        .format("YYYY-MM-DD"),
    }));

    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const currentDate = new Date();
    const days = Array.from({ length: daysInMonth }, (_, i) => ({
      day: i + 1,
      date: `${year}-${String(month + 1).padStart(2, "0")}-${String(
        i + 1
      ).padStart(2, "0")}`,
      event: events.some(
        (event) =>
          moment(event.due_date).tz("America/New_York").date() === i + 1
      ),
      today:
        currentDate.getFullYear() === year &&
        currentDate.getMonth() === month &&
        currentDate.getDate() === i + 1,
    }));

    res.render("calendar", { events, days, month, year });
  });
});

app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});

module.exports = app; // Export the app for testing
