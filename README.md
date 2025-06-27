# 📝 Simple PHP Blog with Admin Dashboard

A lightweight PHP blog system built without frameworks. Ideal for beginners who want to learn the full stack (PHP + MySQL + Bootstrap) while practicing CRUD, session handling, image upload, and basic security.

---

## 🚀 Features

- 🧑‍💼 Admin authentication (basic session-based login)
- 📝 Create, read, update, and delete blog posts
- 🖼️ Image upload support with preview
- ✍️ Rich text editor with [TinyMCE](https://www.tiny.cloud/)
- 🕓 "Time ago" formatted timestamps (`Posted 5 mins ago`)
- 🔍 Admin search, date filters (month/year), and pagination
- 🧼 Input validation and flash messages
- ⚙️ Built with vanilla PHP, PDO (MySQL), and Bootstrap

---

## 📂 Folder Structure
``` bash
/simple_blog/
│
├── admin/
│ └── auth.php # Login/auth protection
│
├── classes/
│ └── Blog.php # Main blog logic (DB operations)
│
├── helpers/
│ └── flash.php # Flash message system
│
├── uploads/ # Uploaded post images
│
├── blog.sql # sql schema commands
├── create.php # Create new post
├── edit.php # Edit a post
├── delete.php # Delete post by ID
├── index.php # List all posts (public)
├── post.php # View a single post
├── db.php # PDO DB connection
└── README.md # (You're reading this)
```
## 🔧 Setup Instructions

### 1. Clone the Repo
```bash
git clone https://github.com/yourusername/simple-php-blog.git
cd simple-php-blog
```

### 2. Import the Database
```bash
1.Create a MySQL database (e.g. simple_blog)
2.Import the blog.sql 
```

### 3. Set Up Config
Edit db.php with your DB credentials:
```bash
$pdo = new PDO("mysql:host=localhost;dbname=simple_blog", "root", "");
```
### 4. Start Local Server
```bash
php -S localhost:8000
```
Then visit: http://localhost:8000

## 🛡️ Admin Area
Only authenticated users can: Create, edit, or delete posts Upload images

You can simulate login for now using a basic session variable in admin/auth.php. (Extend later if needed.)

## 📸 Screenshot

![image](https://github.com/user-attachments/assets/2670b9ab-c994-4ba6-a5ce-09a15147f6c3)
![image](https://github.com/user-attachments/assets/03b036cd-b7e8-4264-8af9-55edf5f07d8a)
![image](https://github.com/user-attachments/assets/0d30e7ea-afb6-483b-9954-d3273232c475)
![image](https://github.com/user-attachments/assets/d24e72c4-b9ed-4e9e-ba6a-b9c3b8e90e04)
![image](https://github.com/user-attachments/assets/c7ffe6df-8a4b-485b-ae79-d0e0a61b4ada)




