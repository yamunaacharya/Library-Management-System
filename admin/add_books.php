<?php
require '../includes/config.php';

if (isset($_POST['submit'])) {
    // Retrieve form inputs and sanitize them
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $ISBN = mysqli_real_escape_string($conn, $_POST['ISBN']);
    $authors = mysqli_real_escape_string($conn, $_POST['authors']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $quantity = intval($_POST['quantity']); // Convert quantity to an integer

    // Insert the book into the database
    $sql = "INSERT INTO boooks (title, ISBN, authors, status, quantity) 
            VALUES ('$title', '$ISBN', '$authors', '$status', $quantity)";
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
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Add Book</title>
</head>
<body>
<div class="container">
        <aside class="sidebar">
            <h1>Library Admin</h1>
            <nav>
            <ul>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="adduser.php">Add Users</a></li>
                    <li><a href="add_books.php">Add Books</a></li>
                    <li><a href="displaybooks.php">View Books</a></li>
                    <li><a href="#">Reports</a></li>
                    <li><a href="#">Settings</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <header class="dashboard-header">
            <h2 class="text-center">Add New Books</h2>
            </header>
    <div class="container my-5">
    <form action="" method="post">
        <div class="form-group">
            <label for="title">Book Title</label>
            <input type="text" class="form-control" name="title" placeholder="Enter book title" required>
        </div>
        <div class="form-group">
            <label for="ISBN">ISBN</label>
            <input type="text" class="form-control" name="ISBN" placeholder="Enter ISBN number" required>
        </div>
        <div class="form-group">
            <label for="authors">Authors</label>
            <input type="text" class="form-control" name="authors" placeholder="Enter authors' names" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" name="status" required>
                <option value="Available">Available</option>
                <option value="Borrowed">Borrowed</option>
                <option value="Reserved">Reserved</option>
            </select>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" class="form-control" name="quantity" placeholder="Enter quantity" value="1" min="1" required>
        </div>
        <button type="submit" class="btn-primary" name="submit">Add Book</button>
    </form>
</div>
</div>
</body>
</html>
