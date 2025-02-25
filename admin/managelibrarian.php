<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    echo "<script>alert('You are not authorized to manage librarians.'); window.location.href='dashboard.php';</script>";
    exit;
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM users WHERE id = $delete_id AND role = 'Librarian'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Librarian deleted successfully!'); window.location.href='managelibrarian.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

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

$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $librarians_query = "SELECT * FROM users WHERE role = 'Librarian' AND fullname LIKE '%$search_query%'";
} else {
    $librarians_query = "SELECT * FROM users WHERE role = 'Librarian'";
}

$librarians_result = mysqli_query($conn, $librarians_query);
?>

        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../librarian/style.css">
    <link rel="stylesheet" href="style.css">
    <script src="../assets/js/script.js"></script>
    <script>
        function confirmDelete(url) {
            if (confirm("Are you sure you want to delete this librarian?")) {
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

    </style>
</head>
<body>
  
    <aside class="sidebar">
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="adduser.php"><i class="fas fa-user-plus"></i> Add Librarian</a></li>
            <li><a href="managelibrarian.php"><i class="fa-solid fa-users"></i> Manage Librarian</a></li>
            <li><a href="report.php"><i class="fas fa-chart-line"></i> Reports</a></li>               
            </ul>
        </nav>
    </aside>
        <main class="main-content">
            <header class="dashboard-header">
                <h2>Manage Registered Librarian</h2>
                <form method="GET" action="managelibrarian.php" class="search-form">
                    <input type="text" name="search" placeholder="Search librarian by name" value="<?= htmlspecialchars($search_query); ?>" class="search-input">
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
