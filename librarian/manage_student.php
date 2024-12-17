<?php
require '../includes/config.php'; // Database connection file

// Handle delete operation
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM usersss WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<script>alert('Student deleted successfully');</script>";
    } else {
        echo "<script>alert('Error deleting student');</script>";
    }
    $stmt->close();
    header("Location: manage_students.php"); 
    exit;
}

// Handle update operation
if (isset($_GET['update_id'])) {
    $update_id = $_GET['update_id'];

    // Fetch current student data
    $stmt = $conn->prepare("SELECT * FROM usersss WHERE id = ?");
    $stmt->bind_param("i", $update_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    // Update student data
    if (isset($_POST['update'])) {
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);

        $stmt = $conn->prepare("UPDATE usersss SET fullname = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $fullname, $email, $phone, $update_id);

        if ($stmt->execute()) {
            echo "<script>alert('Student updated successfully');</script>";
            header("Location: manage_students.php");
            exit;
        } else {
            echo "<script>alert('Error updating student');</script>";
        }
        $stmt->close();
    }
}

// Fetch all usersss
$result = $conn->query("SELECT * FROM usersss");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h3 class="text">Manage Registered Students</h3>

    <!-- Students Table -->
    <table class="table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td>
                        <a href="?update_id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Update</a>
                        <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Do you really want to delete this student?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Update Form -->
    <?php if (isset($_GET['update_id'])): ?>
        <h2 class="mt-4">Update Student Details</h2>
        <form method="POST" class="mt-3">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($student['fullname']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($student['phone']) ?>" required>
            </div>
            <button type="submit" name="update" class="btn btn-primary btn-sm">Update</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
