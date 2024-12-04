<?php
// Include the database configuration file
include('../includes/config.php');

// Start session
session_start();

// Check if the user is logged in and is a librarian
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: ../auth/login.php"); // Redirect to login if not authorized
    exit;
}

// Fetch Total Students
$totalStudentsQuery = "SELECT COUNT(*) as total FROM Usersss WHERE role = 'student'";
$totalStudentsResult = $conn->query($totalStudentsQuery);
$totalStudents = $totalStudentsResult->fetch_assoc()['total'];

// Fetch Total Books
$totalBooksQuery = "SELECT COUNT(*) as total FROM Boooks";
$totalBooksResult = $conn->query($totalBooksQuery);
$totalBooks = $totalBooksResult->fetch_assoc()['total'];

// Fetch Borrowed Books
$borrowedBooksQuery = "SELECT COUNT(*) as total FROM Transactions WHERE status = 'Active'";
$borrowedBooksResult = $conn->query($borrowedBooksQuery);
$borrowedBooks = $borrowedBooksResult->fetch_assoc()['total'];

// Fetch Total Fine Due
$totalFineQuery = "SELECT SUM(fine) as total FROM Transactions WHERE status = 'Active' AND fine > 0";
$totalFineResult = $conn->query($totalFineQuery);
$totalFine = $totalFineResult->fetch_assoc()['total'] ?? 0; // Default to 0 if no fines

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Librarian Dashboard</h1>
        <div class="row mt-4">
            <!-- Total Students -->
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Students</div>
                    <div class="card-body">
                        <h5 class="card-title text-center"><?php echo $totalStudents; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Total Books -->
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Books</div>
                    <div class="card-body">
                        <h5 class="card-title text-center"><?php echo $totalBooks; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Borrowed Books -->
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Borrowed Books</div>
                    <div class="card-body">
                        <h5 class="card-title text-center"><?php echo $borrowedBooks; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Total Fine Due -->
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Total Fine Due</div>
                    <div class="card-body">
                        <h5 class="card-title text-center">Rs.<?php echo number_format($totalFine, 2); ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
