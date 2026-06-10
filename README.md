# FilmBase

A cinema database and film news web application built with PHP, MySQL, HTML5, and CSS3.
University assignment project.

---

## Requirements

- XAMPP (Apache + MySQL + PHP 8.0+)
- PHP extensions: `mysqli`, `curl`, `fileinfo` (all enabled by default in XAMPP)
- Free OMDB API key from [omdbapi.com/apikey.aspx](https://www.omdbapi.com/apikey.aspx)

---

## Setup Instructions

### 1. Copy files to XAMPP

Copy the entire `filmbase/` folder into your XAMPP `htdocs` directory:

```
C:\xampp\htdocs\filmbase\
```

### 2. Import the database

1. Start **Apache** and **MySQL** in the XAMPP Control Panel
2. Open your browser and go to: `http://localhost/phpmyadmin`
3. Click **New** in the left sidebar and create a database named `filmbase`
4. Select the `filmbase` database, click the **Import** tab
5. Click **Choose File** and select:
   ```
   filmbase/database/filmbase.sql
   ```
6. Click **Go** — all tables and sample data will be imported

**Alternative (command line):**
```bash
mysql -u root filmbase < C:\xampp\htdocs\filmbase\database\filmbase.sql
```

### 3. Configure the database connection

Open `config/db.php` and verify the settings match your XAMPP setup:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // Empty by default in XAMPP
define('DB_NAME', 'filmbase');
```

If you have set a MySQL root password in XAMPP, update `DB_PASS` accordingly.

### 4. Set your OMDB API key

1. Register for a free key at [omdbapi.com/apikey.aspx](https://www.omdbapi.com/apikey.aspx)
2. Open `api-omdb.php` and replace the placeholder:

```php
define('OMDB_API_KEY', 'YOUR_OMDB_API_KEY');
```

### 5. Set folder permissions (uploads)

Ensure the uploads folder is writable by Apache:

```
filmbase/assets/uploads/
```

On Windows with XAMPP this is writable by default. If you get upload errors,
right-click the folder → Properties → Security → grant write access to the XAMPP user.

### 6. Open the site

With Apache and MySQL running, visit:

```
http://localhost/filmbase/
```

---

## Demo Login Credentials

All sample accounts use the password: **`password`**

| Name       | Email                   | Role  |
|------------|-------------------------|-------|
| Admin One  | admin@filmbase.com      | Admin |
| Admin Two  | admin2@filmbase.com     | Admin |
| John Smith | john@example.com        | User  |
| Maria Jones| maria@example.com       | User  |

Admin panel: `http://localhost/filmbase/admin/`

---

## File Structure

```
filmbase/
├── index.php               # Homepage
├── news.php                # News listing
├── news-single.php         # Single news article
├── gallery.php             # Photo gallery
├── about.php               # About page
├── contact.php             # Contact form + Google Maps
├── api-omdb.php            # OMDB movie search (REST API / JSON)
├── api-hnb.php             # HNB exchange rates (API / JSON→XML)
├── register.php            # User registration
├── login.php               # Login
├── logout.php              # Logout
│
├── config/
│   └── db.php              # Database connection
│
├── includes/
│   ├── auth.php            # Session helpers, guards
│   ├── header.php          # Shared header + nav
│   └── footer.php          # Shared footer + social icons
│
├── admin/
│   ├── index.php           # Dashboard (stats + tabs)
│   ├── users.php           # User list, edit, delete
│   ├── news-add.php        # Add news article
│   ├── news-edit.php       # Edit news article
│   ├── news-delete.php     # Delete news article
│   └── gallery-add.php     # Upload gallery image + manage
│
├── assets/
│   ├── css/
│   │   └── style.css       # Main stylesheet (dark cinema theme)
│   ├── js/
│   │   └── main.js         # Hamburger menu, tabs, confirm, preview
│   ├── images/             # Static site images (favicon, hero, etc.)
│   └── uploads/            # User-uploaded images (news + gallery)
│
└── database/
    └── filmbase.sql        # Full DB dump with sample data
```

---

## Pages Overview

| Page | URL | Description |
|------|-----|-------------|
| Home | `/filmbase/` | Hero, about, features, latest 3 news |
| News | `/filmbase/news.php` | Paginated news list |
| Article | `/filmbase/news-single.php?id=X` | Full article + related sidebar |
| Gallery | `/filmbase/gallery.php` | Image grid with lightbox |
| About | `/filmbase/about.php` | Team, values, YouTube embed |
| Contact | `/filmbase/contact.php` | Google Maps + contact form |
| Movie Search | `/filmbase/api-omdb.php` | OMDB API search |
| Exchange Rates | `/filmbase/api-hnb.php` | HNB live rates table |
| Register | `/filmbase/register.php` | New user registration |
| Login | `/filmbase/login.php` | Session login |
| Admin | `/filmbase/admin/` | Admin dashboard (admin only) |

---

## Tech Stack

| Technology | Usage |
|------------|-------|
| PHP 8.0+ | Server-side logic, templating, sessions |
| MySQL | Database (users, news, contacts, gallery) |
| HTML5 | Semantic markup, W3C compliant |
| CSS3 | Grid, Flexbox, custom properties, responsive |
| JavaScript (vanilla) | Hamburger menu, tabs, lightbox, preview |
| OMDB REST API | Movie search (JSON via PHP cURL) |
| HNB Open API | Exchange rates (JSON → XML conversion) |
| Google Maps Embed | Contact page map iframe |

---

## Security Features

- Passwords hashed with `password_hash(PASSWORD_DEFAULT)` (bcrypt)
- `password_verify()` for login — no plain-text comparison
- `session_regenerate_id(true)` on login — prevents session fixation
- All output escaped with `htmlspecialchars()` via `e()` helper — prevents XSS
- Prepared statements with `bind_param()` throughout — prevents SQL injection
- File uploads validated by MIME type (`mime_content_type()`), not just extension
- Admin pages guarded by `requireAdmin()` — non-admins redirected instantly
- Generic login error messages — does not reveal which field is wrong

---

## Adding Sample Images

The sample data references these filenames. Add matching images to `assets/uploads/`:

**News images:** `news1.jpg` through `news5.jpg`
**Gallery images:** `gallery1.jpg` through `gallery6.jpg`

Any images will work — the app shows a cinema emoji placeholder if a file is missing.

To use your own images, upload them via the admin panel:
- News images: **Admin → Add News**
- Gallery images: **Admin → Add to Gallery**

---

## Troubleshooting

**Blank page / PHP errors**
- Enable error display: add `ini_set('display_errors', 1);` to the top of `config/db.php` temporarily
- Check Apache error log: `C:\xampp\apache\logs\error.log`

**Database connection failed**
- Ensure MySQL is running in XAMPP Control Panel
- Verify credentials in `config/db.php`
- Confirm the `filmbase` database was created and SQL imported

**Images not uploading**
- Check `assets/uploads/` folder exists and is writable
- Verify `upload_max_filesize` in `php.ini` is at least `5M`
- In XAMPP: `C:\xampp\php\php.ini` → find and set `upload_max_filesize = 10M`

**OMDB API returns no results**
- Ensure you replaced `YOUR_OMDB_API_KEY` in `api-omdb.php`
- Free OMDB keys have a 1,000 requests/day limit

**HNB API not loading**
- HNB API requires an active internet connection
- The API is only available on Croatian business days (rates not published on weekends)

---

*FilmBase — University Assignment Project*
