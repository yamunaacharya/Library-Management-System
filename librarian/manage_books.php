<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'librarian') {
    echo "<script>alert('You are not authorized to manage books.'); window.location.href='dashboard.php';</script>";
    exit;
}

// Add or Update Book Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $ISBN = mysqli_real_escape_string($conn, $_POST['ISBN']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $quantity = intval($_POST['quantity']); 

    // Automatically set status to Unavailable if quantity is 0
    $status = ($quantity == 0) ? 'Unavailable' : 'Available';

    // Check if book exists (if editing)
    if (isset($_POST['edit_id'])) {
        $edit_id = intval($_POST['edit_id']);
        $update_query = "UPDATE books SET Title = '$title', Author = '$author', ISBN = '$ISBN', Category = '$category', Status = '$status', Quantity = $quantity WHERE B_id = $edit_id";
        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('Book updated successfully!'); window.location.href='manage_books.php';</script>";
        } else {
            echo "<script>alert('Error updating book: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        // Insert new book
        $insert_query = "INSERT INTO books (Title, Author, ISBN, Category, Status, Quantity) 
                         VALUES ('$title', '$author', '$ISBN', '$category', '$status', $quantity)";
        if (mysqli_query($conn, $insert_query)) {
            echo "<script>alert('Book added successfully!');</script>";
        } else {
            die("Error adding book: " . mysqli_error($conn));
        }
    }
}

// Delete Book
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM books WHERE B_id = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Book deleted successfully!'); window.location.href='manage_books.php';</script>";
    } else {
        echo "<script>alert('Error deleting book: " . mysqli_error($conn) . "');</script>";
    }
}

// Search Book Logic
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
}

$books_query = "SELECT * FROM books WHERE Title LIKE '%$search_query%' OR Author LIKE '%$search_query%'";
$books_result = mysqli_query($conn, $books_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js"></script>
    <script>
        function confirmDelete(url) {
            if (confirm("Are you sure you want to delete this book?")) {
                window.location.href = url;
            }
        }
    </script>
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
/* Table Styling */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.table th, .table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.table th {
    background-color: #2c3e50;
    color: white;
    font-weight: bold;
}

.table tbody tr:hover {
    background-color: #f1f1f1;
}

/* Form Styling */
.form {
    max-width: 600px;
    margin: 30px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.form h3 {
    margin-bottom: 20px;
    font-size: 24px;
    text-align: center;
    color: #333;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-size: 16px;
    font-weight: bold;
    color: #555;
}

.form-group input {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
}

.form-group input:focus {
    outline: none;
    border-color: #007bff;
}

/* Button Styling */
.btn {
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    border: none;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    border: none;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
    border: none;
}

.btn-danger:hover {
    background-color: #c82333;
}

.dashboard-header {
    background-color: #34495e;
    padding: 20px;
    color: white;
    text-align: center;
    margin-bottom: 30px;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    animation: slideIn 0.6s ease-in-out;
}

.dashboard-header h2 {
    font-size: 24px;
    font-weight: bold;
    margin: 0;
}

/* Search Form Styling */
.search-form {
    display: flex;
    justify-content: center;  /* Center the form */
    margin-top: 10px;
}

/* Styling for the Search Input */
.search-input {
    padding: 10px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    width: 250px;
    outline: none;
    height: 50px;
}

.search-input:focus {
    border-color: #3498db;
    outline: none;
}

/* Styling for the Search Button */
.btn-primary {
    background-color: #3498db;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
}

.btn-primary:hover {
    background-color: #2980b9;
}

/* Responsive Design for smaller screens */
@media screen and (max-width: 768px) {
    /* Adjust search form to be column layout in smaller screens */
    .search-form {
        flex-direction: column;
        align-items: flex-start;
    }

    /* Full width for search input on smaller screens */
    .search-input {
        width: 100%;
        margin-bottom: 10px;
    }

    /* Center the button on smaller screens */
    .btn-primary {
        width: 100%;
        justify-content: center;
    }
}


/* Animation Effect for Header */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dashboard-header {
    animation: slideIn 0.6s ease-in-out;
}

/* Main Content Styling */
.main-content {
    margin-left: 250px; /* Adjust the width of the sidebar */
    padding: 20px;
    min-height: 100vh;
    background-color: #f4f6f9;
    transition: margin-left 0.3s ease;
}

/* Adjust for smaller screens (Responsive Design) */
@media (max-width: 768px) {
    .main-content {
        margin-left: 200px; /* Adjust for smaller screen */
    }
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
        <h2>Manage Books</h2>
        <form method="GET" action="manage_books.php" class="search-form">
            <input type="text" name="search" placeholder="Search books by title or author" value="<?= htmlspecialchars($search_query); ?>" class="search-input">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </header>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($books_result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($books_result)): ?>
                    <tr>
                        <td><?= $row['B_id']; ?></td>
                        <td><?= htmlspecialchars($row['Title']); ?></td>
                        <td><?= htmlspecialchars($row['Author']); ?></td>
                        <td><?= htmlspecialchars($row['Quantity']); ?></td>
                        <td><?= htmlspecialchars($row['Status']); ?></td>
                        <td>
                            <a href="?edit_id=<?= $row['B_id']; ?>" class="btn btn-secondary">Edit</a>
                            <a href="javascript:void(0);" class="btn btn-danger" onclick="confirmDelete('?delete_id=<?= $row['B_id']; ?>')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No books found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (isset($_GET['edit_id'])): 
        $edit_id = intval($_GET['edit_id']);
        $edit_query = "SELECT * FROM books WHERE B_id = $edit_id";
        $edit_result = mysqli_query($conn, $edit_query);
        $edit_data = mysqli_fetch_assoc($edit_result);
        if ($edit_data):
    ?>
        <form method="POST" class="form">
            <h3>Edit Book</h3>
            <input type="hidden" name="edit_id" value="<?= $edit_data['B_id']; ?>">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($edit_data['Title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" class="form-control" name="author" value="<?= htmlspecialchars($edit_data['Author']); ?>" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" class="form-control" name="quantity" value="<?= htmlspecialchars($edit_data['Quantity']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Book</button>
        </form>
    <?php endif; endif; ?>
</main>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
