<?php
// Start session
session_start();
require '../includes/config.php';

// Check if the student is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo "Error: You must be logged in as a student to view this page.";
    header("Location: ../login.php");
    exit;
}

// Get the logged-in student's email from session
$student_email = $_SESSION['student_email'];

// Function to fetch borrowed books
function fetchBorrowedBooks($conn, $student_email) {
    // SQL query to fetch borrowed books with return_date included
    $sql = "SELECT t.T_id, b.title, b.authors, t.issue_date, t.due_date, t.return_date, t.status 
            FROM transaction t
            INNER JOIN boooks b ON t.b_id = b.b_id
            WHERE t.s_email = ? AND t.status IN ('borrowed', 'returned')";

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display results
    if ($result->num_rows > 0) {
        echo "<h3>Your Borrowed Books:</h3>";
        echo "<table class='table table-bordered'>";
        echo "<thead class='thead-dark'>
                <tr>
                    <th>Transaction ID</th>
                    <th>Title</th>
                    <th>Authors</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
              </thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['T_id']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['authors']}</td>
                    <td>{$row['issue_date']}</td>
                    <td>{$row['due_date']}</td>
                    <td>" . ($row['return_date'] ? $row['return_date'] : 'Not Returned') . "</td>
                    <td>{$row['status']}</td>
                  </tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>You have not borrowed any books.</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h1>
        <?php
            // Fetch and display borrowed books
            fetchBorrowedBooks($conn, $student_email);
        ?>
    </div>
</body>
</html>
