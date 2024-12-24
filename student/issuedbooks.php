<?php
require '../includes/config.php'; 
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php"); 
    exit();
}

$student_email = $_SESSION['email'];

$sql = "SELECT b.Title, b.Author, t.Issue_date, t.Due_date, t.Return_date, t.Fine, t.Status
        FROM transaction t
        JOIN books b ON t.B_id = b.B_id
        WHERE t.S_email = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_email); 
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Book History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../librarian/style.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js"></script>
</head>
<body>
<aside class="sidebar">
        <h1>Student Dashboard</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="requestbook.php"><i class="fa-solid fa-book"></i> Request Book</a></li>
                <li><a href="issuedbooks.php"><i class="fa-solid fa-book"></i> Issued Books</a></li>
                <li class="dropdown">
                    <a href="#" onclick="toggleDropdown()" class="dropdown-toggle"><i class="fas fa-cog"></i> Settings <i class="fa fa-chevron-down" style=" margin-left: 100px;"></i></a>
                    <ul class="dropdown-menu" id="settingsDropdown">
                        <li><a href="../auth/changepassword.php"><i class="fas fa-key"></i> Change Password</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </aside>
<div class="container">
    <h2>Your Book History</h2>

    <?php
    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Fine</th>
                    <th>Status</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['Title']) . "</td>
                    <td>" . htmlspecialchars($row['Author']) . "</td>
                    <td>" . htmlspecialchars($row['Issue_date']) . "</td>
                    <td>" . htmlspecialchars($row['Due_date']) . "</td>
                    <td>" . htmlspecialchars($row['Return_date'] === '0000-00-00' ? 'Not Returned' : $row['Return_date']) . "</td>
                    <td>" . htmlspecialchars($row['Fine']) . "</td>
                    <td>" . htmlspecialchars($row['Status']) . "</td>
                  </tr>";
        }

        echo "</table>";
    } else {
        echo "<p class='no-history'>No books issued yet.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>

</body>
</html>
