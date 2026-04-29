# Oak & Wool — Furniture Inspiration Web App

**Project by:** Joshua Akeredolu
**Course:** COSC 459 — Dr. Mack

---

## Tech Stack

- **MAMP** — local Apache/MySQL server
- **MySQL** — database (queries written in SQL)
- **PHP** — server-side logic
- **HTML/CSS** — frontend structure and styling

---

## Database Setup

1. Open **MAMP** and start the server.
2. Click **Open WebStart Page**, then open **phpMyAdmin**.
3. In phpMyAdmin, click the **SQL** tab.
4. Open `akeredolu_joshua_milestone2.sql` from the project folder, copy the contents, paste into the SQL tab, and run it.
   - This creates the `furniture_inspiration_db` database and all tables.
   - It also seeds the database with sample users, designers, categories, and furniture items.

---

## Running the App

1. Make sure MAMP is running and the document root points to this project folder.
2. In your browser, navigate to:
   ```
   http://localhost:80/index.html
   ```
   *(Adjust the port if your MAMP is configured differently, e.g. `:8888`)*

---

## How to Use

1. **Sign Up** — Click "Sign Up" on the login page to create a new account with your first name, last name, email, and password.
2. **Log In** — Use your email and password on the login page to access the site.
3. **Browse the Catalog** — The homepage displays featured furniture pulled from the database. Click **Browse All** to see the full catalog with search and filter options.
4. **Save Favorites** — Click the **Save** button on any furniture card to add it to your personal Inspiration Board. The button turns orange when an item is saved.
5. **View Your Board** — Click **Favorites** in the nav bar (or the Favorites button on the homepage) to view all your saved items.
6. **Remove Items** — On the Favorites page, click **Remove** on any card to take it off your board.

---

## Project File Overview

| File | Purpose |
|------|---------|
| `index.html` | Login page |
| `signup.html` | Sign up page |
| `homepage.php` | Main landing page with featured catalog |
| `fullCatalog.php` | Full searchable/filterable furniture catalog |
| `favorites.php` | User's personal inspiration board |
| `login.php` | Handles login form submission |
| `signup.php` | Handles signup form submission |
| `toggle_favorite.php` | API endpoint — adds/removes items from board |
| `akeredolu_joshua_milestone2.sql` | Full database schema and seed data |
| `homestyle.css` | Main stylesheet |
| `favoritesStyle.css` | Styles for the favorites/board page |
