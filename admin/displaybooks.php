<?php
// Include database connection file
require '../includes/config.php';

// Handle delete operation
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM boooks WHERE b_id = $delete_id";
    if (mysqli_query($conn, $sql_delete)) {
        echo "<script>alert('Book deleted successfully');</script>";
    } else {
        echo "<script>alert('Error deleting book');</script>";
    }
}

// Handle update operation
if (isset($_GET['update_id'])) {
    $update_id = $_GET['update_id'];
    $sql = "SELECT * FROM boooks WHERE b_id = $update_id";
    $result = mysqli_query($conn, $sql);
    $book = mysqli_fetch_assoc($result);

    if (isset($_POST['update'])) {
        // Get updated data
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $ISBN = mysqli_real_escape_string($conn, $_POST['ISBN']);
        $authors = mysqli_real_escape_string($conn, $_POST['authors']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $quantity = $_POST['quantity'];

        $update_sql = "UPDATE boooks SET title='$title', ISBN='$ISBN', authors='$authors', status='$status', quantity=$quantity WHERE b_id = $update_id";
        if (mysqli_query($conn, $update_sql)) {
            echo "<script>alert('Book updated successfully');</script>";
            header('Location: viewbooks.php');
        } else {
            echo "<script>alert('Error updating book');</script>";
        }
    }
}

// Fetch all books for display
$sql = "SELECT * FROM boooks";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="style.css">
    <title>Manage Books</title>
</head>
<body>
<div class="container">
        <aside class="sidebar">
            <h1>Library Admin</h1>
            <nav>
            <ul>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="adduser.php">Users</a></li>
                    <li><a href="add_books.php">AddBooks</a></li>
                    <li><a href="displaybooks.php">Books Details</a></li>
                    <li><a href="#">Reports</a></li>
                    <li><a href="#">Settings</a></li>
                </ul>
            </nav>
        </aside>
<div class="container my-5">
    <!-- <h2 class="text-center">Manage Books</h2> -->

    <!-- Display Book Details -->
    <h4 class="mt-5">Book Details</h4>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Book ID</th>
                <th scope="col">Title</th>
                <th scope="col">ISBN</th>
                <th scope="col">Authors</th>
                <th scope="col">Status</th>
                <th scope="col">Quantity</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['b_id']}</td>
                        <td>{$row['title']}</td>
                        <td>{$row['ISBN']}</td>
                        <td>{$row['authors']}</td>
                        <td>{$row['status']}</td>
                        <td>{$row['quantity']}</td>
                        <td>
                            <a href='?update_id={$row['b_id']}' class='btn btn-warning btn-sm'>Update</a>
                            <a href='?delete_id={$row['b_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                        </td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Update Book Form (appears when update is clicked) -->
    <?php if (isset($_GET['update_id'])) { ?>
        <?php
        $update_id = $_GET['update_id'];
        $sql = "SELECT * FROM boooks WHERE b_id = $update_id";
        $result = mysqli_query($conn, $sql);
        $book = mysqli_fetch_assoc($result);
        ?>
        <h4>Update Book Details</h4>
        <form action="" method="POST">
            <div class="form-group">
                <label for="title">Book Title</label>
                <input type="text" class="form-control" name="title" value="<?php echo $book['title']; ?>" required>
            </div>
            <div class="form-group">
                <label for="ISBN">ISBN</label>
                <input type="text" class="form-control" name="ISBN" value="<?php echo $book['ISBN']; ?>" required>
            </div>
            <div class="form-group">
                <label for="authors">Authors</label>
                <input type="text" class="form-control" name="authors" value="<?php echo $book['authors']; ?>" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" name="status" required>
                    <option value="Available" <?php if ($book['status'] == 'Available') echo 'selected'; ?>>Available</option>
                    <option value="Borrowed" <?php if ($book['status'] == 'Borrowed') echo 'selected'; ?>>Borrowed</option>
                    <option value="Reserved" <?php if ($book['status'] == 'Reserved') echo 'selected'; ?>>Reserved</option>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" class="form-control" name="quantity" value="<?php echo $book['quantity']; ?>" min="1" required>
            </div>
            <button type="submit" class="btn btn-primary" name="update">Update Book</button>
        </form>
    <?php } ?>
    </div>
</div>
</body>
</html>
