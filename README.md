# 📝 Simple PHP Blog with Admin Dashboard

A lightweight PHP blog system built without frameworks. Ideal for beginners who want to learn the full stack (PHP + MySQL + Bootstrap) while practicing CRUD, session handling, image upload, and basic security.

---

## 🚀 Features

- 🧑‍💼 Admin authentication (session-based login)
- 📝 Create, read, update, and delete blog posts
- 🖼️ Image upload support with preview
- ✍️ Rich text editor with [TinyMCE](https://www.tiny.cloud/)
- 🕓 "Time ago" formatted timestamps (`Posted 5 mins ago`)
- 🔍 Admin search, date filters (month/year), and pagination
- 🧼 Input validation and flash messages
- ⚙️ Built with vanilla PHP, PDO (MySQL), and Bootstrap

---

## 📂 Folder Structure

```bash
/simple_blog/
│
├── admin/               # Admin-only logic (login/logout)
│   ├── auth.php         # Login/auth protection middleware
│   └── login.php        # Admin login page
│
├── classes/
│   └── Blog.php         # Main blog logic (DB operations, helpers)
│
├── helpers/
│   └── flash.php        # Flash message system
│
├── uploads/             # Uploaded post images
│
├── blog.sql             # Database schema
├── create.php           # Create new post
├── edit.php             # Edit a post
├── delete.php           # Delete post by ID
├── index.php            # List all posts (public)
├── post.php             # View a single post
├── logout.php           # Destroy session and redirect
├── db.php               # PDO DB connection
└── README.md            # You're reading this
```

---

## 🔧 Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/simple-php-blog.git
cd simple-php-blog
```

### 2. Import the Database

1. Open phpMyAdmin or your MySQL client.
2. Create a new database (e.g. `simple_blog`)
3. Import the `blog.sql` file included in the root directory.

### 3. Configure Database

Update your `db.php` file with your database credentials:

```php
$pdo = new PDO("mysql:host=localhost;dbname=simple_blog", "root", "");
```

### 4. Start the Development Server

```bash
php -S localhost:8000
```

Then open your browser and visit: [http://localhost:8000](http://localhost:8000)

---

## 🔐 Admin Login

To access the admin dashboard:

- URL: `http://localhost:8000/admin/login.php`
- Default credentials:

```
Username: admin
Password: 1234
```

⚠️ These are hardcoded in `admin/login.php`. You can update them or add a proper login system later.

---

## ☁️ Deploy to Hosting

### 🅿️ Shared Hosting / cPanel

1. Upload all files to `public_html/` or a subfolder.
2. Import `blog.sql` using phpMyAdmin.
3. Edit `db.php` with your host's credentials.

### 🐘 XAMPP / Localhost

1. Copy the project to `htdocs/`
2. Start Apache and MySQL from XAMPP
3. Open [http://localhost/simple-php-blog](http://localhost/simple-php-blog)

---

## 🖼️ Screenshots

| Home Page | Rich Text Editor |
|-----------|------------------|
| ![](https://github.com/user-attachments/assets/2670b9ab-c994-4ba6-a5ce-09a15147f6c3) | ![](https://github.com/user-attachments/assets/03b036cd-b7e8-4264-8af9-55edf5f07d8a) |

| Admin View | Post View |
|------------|------------|
| ![](https://github.com/user-attachments/assets/0d30e7ea-afb6-483b-9954-d3273232c475) | ![](https://github.com/user-attachments/assets/d24e72c4-b9ed-4e9e-ba6a-b9c3b8e90e04) |

| Post List |
|-----------|
| ![](https://github.com/user-attachments/assets/c7ffe6df-8a4b-485b-ae79-d0e0a61b4ada) |

---

## 🙋‍♂️ Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

🧠 Built by learners, for learners. Happy coding!
