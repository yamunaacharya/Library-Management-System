<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'librarian') {
    echo "<script>alert('You are not authorized to manage students.'); window.location.href='dashboard.php';</script>";
    exit;
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM users WHERE id = $delete_id AND role = 'student'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Student deleted successfully!'); window.location.href='manage_student.php';</script>";
    } else {
        echo "<script>alert('Error deleting student: " . mysqli_error($conn) . "');</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id = intval($_POST['edit_id']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $update_query = "UPDATE users SET fullname = '$fullname', email = '$email', phone = '$phone' WHERE id = $edit_id AND role = 'student'";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Student updated successfully!'); window.location.href='manage_student.php';</script>";
    } else {
        echo "<script>alert('Error updating student: " . mysqli_error($conn) . "');</script>";
    }
}

$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
}

$students_query = "SELECT * FROM users WHERE role = 'student' AND fullname LIKE '%$search_query%'";
$students_result = mysqli_query($conn, $students_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js"></script>
    <script>
        function confirmDelete(url) {
            if (confirm("Are you sure you want to delete this student?")) {
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
                <h2>Manage Registered Students</h2>
                <form method="GET" action="manage_student.php" class="search-form">
                    <input type="text" name="search" placeholder="Search students by name" value="<?= htmlspecialchars($search_query); ?>" class="search-input">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
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
                    <?php if (mysqli_num_rows($students_result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($students_result)): ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['fullname']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td><?= htmlspecialchars($row['phone']); ?></td>
                                <td>
                                    <a href="?edit_id=<?= $row['id']; ?>" class="btn btn-secondary">Edit</a>
                                    <a href="javascript:void(0);" class="btn btn-danger" onclick="confirmDelete('?delete_id=<?= $row['id']; ?>')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if (isset($_GET['edit_id'])): 
                $edit_id = intval($_GET['edit_id']);
                $edit_query = "SELECT * FROM users WHERE id = $edit_id AND role = 'student'";
                $edit_result = mysqli_query($conn, $edit_query);
                $edit_data = mysqli_fetch_assoc($edit_result);
                if ($edit_data):
            ?>
                <form method="POST" class="form">
                    <h3>Edit Student</h3>
                    <input type="hidden" name="edit_id" value="<?= $edit_data['id']; ?>">
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" class="form-control" name="fullname" value="<?= htmlspecialchars($edit_data['fullname']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($edit_data['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($edit_data['phone']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </form>
            <?php endif; endif; ?>
        </main>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
