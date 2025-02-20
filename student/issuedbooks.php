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
    <style>
/* Sidebar Styling */
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

    .dropdown-toggle i {
        margin-left: 40px;
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
/* Body and overall layout */
body {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    height: 100vh;
    margin: 0;
    background-color: #f4f4f4;
}

/* Sidebar */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px; /* Adjust the sidebar width */
    height: 100vh;
    background-color: #333;
    color: #fff;
    padding: 20px;
    box-sizing: border-box;
    z-index: 10;
}

/* General container styles */
.container {
    max-width: 90%; /* Slightly larger container */
    width: auto; /* Ensure the container is not too wide */
    margin: 20px 0 0 250px;
    padding: 30px;
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1; /* Ensure it appears above the sidebar */
}

/* Heading styling */
.container h2 {
    text-align: center;
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin: 0;
    padding-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Table Styling */
table {
    width: auto;
    border-collapse: collapse;
    margin-top: 20px;
}

/* Table Header Styling */
table th {
    background: #34495e;
    color: #fff;
    text-align: left;
    padding: 15px 20px;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

/* Table Cell Styling */
table td {
    padding: 12px 20px;
    font-size: 14px;
    color: #555;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

/* Alternating Row Colors */
table tr:nth-child(even) {
    background-color: #f9f9f9;
}

table tr:hover {
    background-color: #e1f0ff;
}

/* Last Row No Border */
table tr:last-child td {
    border-bottom: none;
}

/* No history message styling */
.no-history {
    text-align: center;
    font-size: 18px;
    color: #888;
    margin-top: 30px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 20px;
        width: 80%; /* Make the container wider on smaller screens */
    }

    table th, table td {
        padding: 10px;
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 15px;
        width: 90%; /* Make the container wider on very small screens */
    }

    table th, table td {
        padding: 8px;
        font-size: 11px;
    }
}

    </style>
</head>
<body>
<aside class="sidebar">
        <h1>Student Dashboard</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="requestbook.php"><i class="fa-solid fa-book"></i> Request Book</a></li>
                <li><a href="issuedbooks.php"><i class="fa-solid fa-book"></i> Issued Books</a></li>
                <li><a href="returnbooks.php"><i class="fa-solid fa-book"></i> Return Books</a></li>
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
