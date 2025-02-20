<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $ISBN = mysqli_real_escape_string($conn, $_POST['ISBN']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $quantity = intval($_POST['quantity']); 

    $sql = "INSERT INTO books (Title, Author, ISBN, Category, Status, Quantity) 
            VALUES ('$title', '$author', '$ISBN', '$category', '$status', $quantity)";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo "<script>alert('Book added successfully!');</script>";
    } else {
        die("Error adding book: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js"></script>
    <style>
         .sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background: linear-gradient(135deg, #2c3e50, #1a252f);
    color: white;
    padding: 20px 10px;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    overflow-y: auto;
    transition: width 0.3s ease;
}

/* Sidebar Title */
.sidebar h1 {
    font-size: 24px;
    text-align: center;
    margin-bottom: 30px;
    color: #fff;
    letter-spacing: 1px;
    animation: fadeIn 0.5s ease;
}

/* Navigation Links */
.sidebar nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar nav ul li {
    margin: 10px 0;
}

.sidebar nav ul li a {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    color: #ddd;
    font-size: 16px;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.sidebar nav ul li a i {
    margin-right: 10px;
    font-size: 18px;
    color: #00d1ff;
    transition: transform 0.3s ease;
}

/* Hover Effect */
.sidebar nav ul li a:hover {
    background-color: #007bff;
    color: white;
    transform: translateX(5px);
}

.sidebar nav ul li a:hover i {
    transform: rotate(360deg);
}

/* Active Link Styling */
.sidebar nav ul li a.active {
    background-color: #007bff;
    color: white;
}

/* Dropdown Styling */
.dropdown-toggle {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.dropdown-menu {
    display: none;
    padding-left: 20px;
    margin-top: 5px;
    animation: slideDown 0.3s ease;
}

.dropdown-menu li {
    margin: 5px 0;
}

.dropdown-menu a {
    padding: 8px 15px;
    color: #ddd;
    font-size: 14px;
    border-radius: 6px;
    transition: background-color 0.3s ease;
}

.dropdown-menu a:hover {
    background-color: #007bff;
    color: white;
}

/* JavaScript Toggle for Dropdown */
.show-dropdown {
    display: block;
}

/* Scrollbar Styling */
.sidebar::-webkit-scrollbar {
    width: 8px;
}

.sidebar::-webkit-scrollbar-thumb {
    background-color: #007bff;
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-track {
    background-color: #1a252f;
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        width: 200px;
        padding: 10px;
    }

    .sidebar h1 {
        font-size: 20px;
    }

    .sidebar nav ul li a {
        font-size: 14px;
        padding: 8px 10px;
    }
}

/* Animation Effects */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
/* Reset default margin and padding for html and body */
html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    width: 100%;
}

/* Main Content Area */
.main-content {
    background-color: #ffffff;
    min-height: 100vh; /* Ensure the content takes full height of the page */
    margin: 0; /* No margin, ensures it touches top and sides */
    padding: 0; /* Remove any padding */
}

/* Dashboard Header */
.dashboard-header {
    background-color: #34495e;
    color: #fff;
    padding: 25px 0;
    text-align: center;
    margin: 0; /* Remove margin */
    border-radius: 0; /* Optional: remove rounded corners */
    box-shadow: none; /* Optional: remove shadow */
}

.dashboard-header h3 {
    font-size: 28px;
    font-weight: 700;
    margin: 0;
    letter-spacing: 2px;
    text-transform: uppercase;
}

/* Ensure the sidebar and other elements are also aligned */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 250px;
    background-color: #2c3e50;
    color: white;
    z-index: 1000;
}

/* Responsive Design */
@media (max-width: 768px) {
    .main-content {
        padding: 15px;
    }

    .dashboard-header h3 {
        font-size: 24px; /* Adjust font size for smaller screens */
    }
}
/* General container styling */
.container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Form group styling */
.form-group {
    margin-bottom: 15px;
}

/* Label styling */
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

/* Input field styling */
.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    box-sizing: border-box;
}

/* Select dropdown styling */
.form-control select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    background-color: #fff;
}

/* Button styling */
.btn-primary {
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    color: #fff;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background-color: #0056b3;
}

/* Optional: Add some spacing and alignment */
button[type="submit"] {
    width: 100%;
    margin-top: 10px;
}

    </style>
</head>
<body>
  
    <aside class="sidebar">
        <h1>Librarian Dashboard</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="add_books.php"><i class="fas fa-book"></i> Add Book</a></li>
                <li><a href="manage_books.php"><i class="fas fa-book"></i> Manage Book</a></li>
                <li><a href="issuebook.php"><i class="fas fa-book"></i> Issue Book</a></li>
                <li><a href="manage_student.php"><i class="fa-solid fa-users"></i> Manage Student</a></li>
                <li class="dropdown">
                    <a href="#" onclick="toggleDropdown()" class="dropdown-toggle"><i class="fas fa-cog"></i> Settings <i class="fa fa-chevron-down" style=" margin-left: 100px;"></i></a>
                    <ul class="dropdown-menu" id="settingsDropdown">
                        <li><a href="../auth/changepassword.php"><i class="fas fa-key"></i> Change Password</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </aside>
    <main class="main-content">
        <header class="dashboard-header">
            <h3 class="text-center">Add New Book</h3><br>
        </header>
        <div class="container">
            <form action="" method="post">
                <div class="form-group">
                    <label for="title">Book Title</label>
                    <input type="text" class="form-control" name="title" placeholder="Enter book title" required>
                </div>
                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" class="form-control" name="author" placeholder="Enter author's name" required>
                </div>
                <div class="form-group">
                    <label for="ISBN">ISBN</label>
                    <input type="text" class="form-control" name="ISBN" placeholder="Enter ISBN (optional)">
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" class="form-control" name="category" placeholder="Enter book category" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" name="status" required>
                        <option value="Available">Available</option>
                        <option value="Reserved">Reserved</option>
                        <option value="Issued">Issued</option>
                        <option value="Unavailable">Unavailable</option>
                        
                    </select>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" class="form-control" name="quantity" placeholder="Enter quantity" value="1" min="1" required>
                </div>
                <button type="submit" class="btn-primary" name="submit">Add Book</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
