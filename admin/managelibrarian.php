<?php
require '../includes/config.php';
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    echo "<script>alert('You are not authorized to manage librarians.'); window.location.href='dashboard.php';</script>";
    exit;
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM users WHERE id = $delete_id AND role = 'Librarian'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Librarian deleted successfully!'); window.location.href='managelibrarian.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// Handle edit action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id = intval($_POST['edit_id']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $update_query = "UPDATE users SET fullname = '$fullname', email = '$email', phone = '$phone' WHERE id = $edit_id AND role = 'Librarian'";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Librarian updated successfully!'); window.location.href='managelibrarian.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// Fetch librarians
$librarians_query = "SELECT * FROM users WHERE role = 'Librarian'";
$librarians_result = mysqli_query($conn, $librarians_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Librarians</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        // Confirm before deletion
        function confirmDelete(url) {
            if (confirm("Are you sure you want to delete this librarian?")) {
                window.location.href = url;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h1>Library Admin</h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="adduser.php">Add Librarian</a></li>
                    <li><a href="managelibrarian.php">Manage Librarian</a></li>
                    <li><a href="#">Reports</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <header class="dashboard-header">
                <h2 class="text-center">Manage Registered Librarians</h2>
            </header>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($librarians_result)): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= $row['fullname']; ?></td>
                            <td><?= $row['email']; ?></td>
                            <td><?= $row['phone']; ?></td>
                            <td>
                                <a href="?edit_id=<?= $row['id']; ?>" class="btn btn-secondary">Edit</a>
                                <a href="javascript:void(0);" class="btn btn-danger" onclick="confirmDelete('?delete_id=<?= $row['id']; ?>')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <?php if (isset($_GET['edit_id'])): 
                $edit_id = intval($_GET['edit_id']);
                $edit_query = "SELECT * FROM users WHERE id = $edit_id AND role = 'Librarian'";
                $edit_result = mysqli_query($conn, $edit_query);
                $edit_data = mysqli_fetch_assoc($edit_result);
                if ($edit_data):
            ?>
                <form method="POST" class="form">
                    <h3>Edit Librarian</h3>
                    <input type="hidden" name="edit_id" value="<?= $edit_data['id']; ?>">
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" class="form-control" name="fullname" value="<?= $edit_data['fullname']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= $edit_data['email']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" name="phone" value="<?= $edit_data['phone']; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Librarian</button>
                </form>
            <?php endif; endif; ?>
        </main>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
