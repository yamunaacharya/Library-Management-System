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
