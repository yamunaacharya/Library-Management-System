<?php
require '../includes/config.php'; 

// DELETE librarian
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM usersss WHERE id = ? AND role = 'librarian'");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Librarian deleted successfully.";
    } else {
        echo "Failed to delete librarian.";
    }
    $stmt->close();
    header("Location: managelibrarian.php"); // Redirect back to the main page
    exit;
}

// UPDATE librarian
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE usersss SET fullname = ?, email = ?, phone = ? WHERE id = ? AND role = 'librarian'");
    $stmt->bind_param("sssi", $fullname, $email, $phone, $id);

    if ($stmt->execute()) {
        echo "Librarian updated successfully.";
    } else {
        echo "Failed to update librarian.";
    }
    $stmt->close();
    header("Location: managelibrarian.php"); // Redirect back to the main page
    exit;
}

// FETCH all librarians
$stmt = $conn->prepare("SELECT * FROM usersss WHERE role = 'librarian'");
$stmt->execute();
$result = $stmt->get_result();
$librarians = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// If editing a librarian, fetch their details
$editingLibrarian = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM usersss WHERE id = ? AND role = 'librarian'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editingLibrarian = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
                    <li><a href="displaybooks.php">Manage Books</a></li>
                    <li><a href="managelibrarian.php">Manage Librarian</a></li>
                    <li><a href="#">Reports</a></li>
                    <li><a href="#">Settings</a></li>
                </ul>
            </nav>
        </aside>
        <div class="container1">
        <h2 class="text">Manage Libraian</h2>
        <table class="table-bordered">
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($librarians as $librarian): ?>
        <tr>
            <td><?php echo $librarian['id']; ?></td>
            <td><?php echo $librarian['fullname']; ?></td>
            <td><?php echo $librarian['email']; ?></td>
            <td><?php echo $librarian['phone']; ?></td>
            <td>
                <!-- Update link -->
                <a href="?edit=<?php echo $librarian['id']; ?>">Update</a>
                <!-- Delete link -->
                <a href="?delete=<?php echo $librarian['id']; ?>" onclick="return confirm('Do you really want to delete this librarian?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <hr>

    <!-- Update Librarian Form -->
    <?php if ($editingLibrarian): ?>
    <h3>Update Librarian</h3>
    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $editingLibrarian['id']; ?>">
        
        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname" value="<?php echo $editingLibrarian['fullname']; ?>" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $editingLibrarian['email']; ?>" required><br><br>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?php echo $editingLibrarian['phone']; ?>" required><br><br>

        <input type="submit" name="update" value="Update Librarian">
    </form>
    <?php endif; ?>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
