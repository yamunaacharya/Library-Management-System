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
/* Main Content Styling */
.main-content {
    margin-left: 250px; /* Adjust the width of the sidebar */
    padding: 20px;
    min-height: 100vh;
    background-color: #f4f6f9;
    transition: margin-left 0.3s ease;
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
    justify-content: center;
    align-items: center; /* Ensures both input and button align properly */
    gap: 10px; /* Adds a little space between the input and button */
    margin-top: 20px; 
}

/* Search Input Styling */
.search-input {
    padding: 8px 15px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
    width: 80%; /* Adjust input width */
}
/* Refined Search Form */

/* Search Input */
.search-input {
    padding: 10px 14px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 10px;
    width: 100%;
    max-width: 350px;
    outline: none;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.search-input:focus {
    border-color: #3498db;
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.6);
}
/* Responsive Design */
@media screen and (max-width: 768px) {
    .search-form {
        flex-direction: column;
        gap: 8px;
        width: 100%;
    }

    .search-input {
        width: 100%;
        margin: 0;
    }

    .btn-primary {
        width: 100%;
        justify-content: center;
    }
}
/* Adjust for smaller screens (Responsive Design) */
@media (max-width: 768px) {
    .main-content {
        margin-left: 200px; /* Adjust for smaller screen */
    }
}

/* Compact Search Button */
.btn-primary {
    background-color: #3498db;
    color: white;
    padding: 8px 12px; /* Smaller padding for compact size */
    border: none;
    border-radius: 10px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    display: flex;
    align-items: center;
    height: 42px; /* Match input height for alignment */
    white-space: nowrap;
}

.btn-primary:hover {
    background-color: #2980b9;
    transform: translateY(-1px);
}
/* Table Styling */
.table {
    width: 100%;
    margin-top: 30px;
    border-collapse: collapse;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.table th,
.table td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.table th {
    background-color: #2c3e50;
    color: white;
    font-size: 16px;
}

.table tr:nth-child(even) {
    background-color: #f4f4f4;
}

/* Buttons */
.btn {
    padding: 8px 15px;
    font-size: 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

/* Form Styling */
.form {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 30px;
}

.form h3 {
    font-size: 24px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-size: 16px;
    font-weight: bold;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border-radius: 6px;
    border: 1px solid #ddd;
}

.form-control:focus {
    border-color: #007bff;
    outline: none;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    width: 100%;
    padding: 10px;
    border-radius: 6px;
}

.btn-primary:hover {
    background-color: #0056b3;
}

/* Center text when no students found */
.text-center {
    font-size: 18px;
    color: #555;
    margin-top: 30px;
    font-style: italic;
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
                <h2>Manage Registered Students</h2>
                <div class="search-form">
                <form method="GET" action="manage_student.php" >
                    <input type="text" name="search" placeholder="Search students by name" value="<?= htmlspecialchars($search_query); ?>" class="search-input">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
                </div>
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