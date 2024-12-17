<?php
require '../includes/config.php';

// Handle delete operation
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    if (mysqli_query($conn, "DELETE FROM boooks WHERE b_id = $delete_id")) {
        echo "<script>alert('Book deleted successfully');</script>";
    } else {
        echo "<script>alert('Error deleting book');</script>";
    }
}

// Handle update operation
if (isset($_GET['update_id'])) {
    $update_id = $_GET['update_id'];
    $bookQuery = "SELECT * FROM boooks WHERE b_id = $update_id";
    $result = mysqli_query($conn, $bookQuery);
    $book = mysqli_fetch_assoc($result);

    // Update operation
    if (isset($_POST['update'])) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $ISBN = mysqli_real_escape_string($conn, $_POST['ISBN']);
        $authors = mysqli_real_escape_string($conn, $_POST['authors']);
        $status = $_POST['status'];
        $quantity = $_POST['quantity'];

        // Set status to 'Unavailable' if quantity is 0
        if ($quantity == 0) $status = 'Unavailable';

        $updateQuery = "UPDATE boooks SET title='$title', ISBN='$ISBN', authors='$authors', status='$status', quantity=$quantity WHERE b_id = $update_id";
        if (mysqli_query($conn, $updateQuery)) {
            echo "<script>alert('Book updated successfully');</script>";
            header('Location: displaybooks.php');
            exit;
        } else {
            echo "<script>alert('Error updating book');</script>";
        }
    }
}

// Handle borrow operation (decrease book quantity)
if (isset($_GET['borrow_id'])) {
    $borrow_id = $_GET['borrow_id'];

    // Check current quantity
    $query = "SELECT quantity FROM boooks WHERE b_id = $borrow_id";
    $result = mysqli_query($conn, $query);
    $book = mysqli_fetch_assoc($result);

    if ($book['quantity'] > 0) {
        $new_quantity = $book['quantity'] - 1;
        $new_status = ($new_quantity == 0) ? 'Unavailable' : 'Available';

        // Update quantity and status
        $updateQuery = "UPDATE boooks SET quantity=$new_quantity, status='$new_status' WHERE b_id = $borrow_id";
        if (mysqli_query($conn, $updateQuery)) {
            echo "<script>alert('Book borrowed successfully');</script>";
        } else {
            echo "<script>alert('Error borrowing book');</script>";
        }
    } else {
        echo "<script>alert('This book is currently unavailable');</script>";
    }
}

// Handle search
$searchQuery = "";
if (isset($_POST['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_POST['search']);
    $searchQuery = "WHERE title LIKE '%$searchTerm%' AND status = 'Available'";
}

// Fetch all books
$booksQuery = "SELECT * FROM boooks $searchQuery";
$result = mysqli_query($conn, $booksQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Manage Books</title>
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <h1>Library Admin</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="adduser.php">Add Users</a></li>
                <li><a href="add_books.php">Add Books</a></li>
                <li><a href="managebooks.php">Manage Books</a></li>
                <li><a href="managelibrarian.php">Manage Librarian</a></li>
                <li><a href="#">Reports</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </nav>
    </aside>
    <div class="container1">
        <!-- Search Box -->
        <form method="POST" class="search-box">
            <input type="text" name="search" placeholder="Search available books" value="<?= isset($_POST['search']) ? htmlspecialchars($_POST['search']) : '' ?>" class="search-input">
            <button type="submit" class="search-btn">
                <img src="../assets/icons/search-icon.png" alt="Search">
            </button>
        </form>

        <h4 class="text">List of Books</h4>
        <table class="table-bordered">
            <tr>
                <th>Book ID</th>
                <th>Title</th>
                <th>ISBN</th>
                <th>Authors</th>
                <th>Status</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php if ($row['quantity'] == 0) $row['status'] = 'Unavailable'; ?>
                    <tr>
                        <td><?= $row['b_id'] ?></td>
                        <td><?= $row['title'] ?></td>
                        <td><?= $row['ISBN'] ?></td>
                        <td><?= $row['authors'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td>
                            <a href="?update_id=<?= $row['b_id'] ?>" class="btn">Update</a>
                            <a href="?delete_id=<?= $row['b_id'] ?>" class="btn" onclick="return confirm('Do you really want to delete this?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php if (isset($_GET['update_id'])): ?>
            <h4>Update Book Details</h4>
            <form method="POST">
                <div class="form-group">
                    <label for="title">Book Title</label>
                    <input type="text" name="title" class="form-control" value="<?= $book['title'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="ISBN">ISBN</label>
                    <input type="text" name="ISBN" class="form-control" value="<?= $book['ISBN'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="authors">Authors</label>
                    <input type="text" name="authors" class="form-control" value="<?= $book['authors'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="Available" <?= $book['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                        <option value="Borrowed" <?= $book['status'] == 'Borrowed' ? 'selected' : '' ?>>Borrowed</option>
                        <option value="Reserved" <?= $book['status'] == 'Reserved' ? 'selected' : '' ?>>Reserved</option>
                        <option value="Unavailable" <?= $book['status'] == 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" name="quantity" class="form-control" value="<?= $book['quantity'] ?>" min="1" required>
                </div>
                <button type="submit" class="btn" name="update">Update Book</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
