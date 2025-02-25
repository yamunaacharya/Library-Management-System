<?php
require '../includes/config.php'; 

// Function to fetch issued books report
function getIssuedBooksReport($conn) {
    $query = "
        SELECT t.T_id, u.fullname AS Borrower, b.Title AS Book, t.Issue_date, t.Due_date, t.Status
        FROM transaction t
        JOIN users u ON t.S_email = u.email
        JOIN books b ON t.B_id = b.B_id
        WHERE t.Status = 'Issued'
    ";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to fetch fine collection report
function getPaymentReport($conn) {
    $query = "
        SELECT id, purchase_order_id, email, amount, status, created_at
        FROM payments
        WHERE status = 'Completed'
    ";

    $result = mysqli_query($conn, $query);
    
    $paymentReport = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $paymentReport[] = $row;
    }
    
    return $paymentReport;
}

// Function to fetch book availability report
function getBookAvailabilityReport($conn) {
    $query = "SELECT Title, Category, Quantity, Status FROM books";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Generate reports based on admin's choice
$reportType = isset($_GET['report']) ? $_GET['report'] : '';
$reportData = [];

if ($reportType) {
    switch ($reportType) {
        case 'issued_books':
            $reportData = getIssuedBooksReport($conn);
            break;
        case 'fine_collection':
            $reportData = getPaymentReport($conn);
            break;
        case 'book_availability':
            $reportData = getBookAvailabilityReport($conn);
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Report Generation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* General Page Styling */
body {
    margin: 0; /* Removes the default margin */
    padding: 0; /* Removes the default padding */
    font-family: Arial, sans-serif; /* Sets the default font family */
    background-color: #f8f9fa; /* Sets the background color to a light gray */
    color: #333; /* Sets the text color to a dark gray */
    display: flex; /* Applies flexbox layout to the body */
    height: 100vh; /* Makes the body's height 100% of the viewport height */
    width: 100%; /* Makes the body's width 100% of the viewport width */
    overflow-x: hidden; /* Prevents horizontal scrolling */
}
/* Sidebar Styling */
.sidebar {
    width: 250px; /* Sets the width of the sidebar */
    height: 100vh; /* Makes the sidebar's height 100% of the viewport height */
    position: fixed; /* Fixes the sidebar in place */
    top: 0;
    left: 0;
    background: linear-gradient(135deg, #2c3e50, #1a252f); /* Gradient background for sidebar */
    color: white; /* Sets the text color to white */
    padding: 20px 10px; /* Adds padding inside the sidebar */
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3); /* Adds shadow to the right side of the sidebar */
    z-index: 1000; /* Ensures the sidebar appears above other content */
    overflow-y: auto; /* Allows vertical scrolling if content overflows */
    transition: width 0.3s ease; /* Smooth transition when resizing */
}

/* Sidebar Title */
.sidebar h1 {
    font-size: 24px; /* Sets the font size of the title */
    text-align: center; /* Centers the title text */
    margin-bottom: 30px; /* Adds space below the title */
    color: #fff; /* White color for the title text */
    letter-spacing: 1px; /* Adds space between the letters */
    animation: fadeIn 0.5s ease; /* Adds fade-in animation to the title */
}

/* Navigation Links */
.sidebar nav ul {
    list-style: none; /* Removes default list bullet points */
    padding: 0; /* Removes padding around the list */
    margin: 0; /* Removes margin from the list */
}

.sidebar nav ul li {
    margin: 10px 0; /* Adds space between each list item */
}

.sidebar nav ul li a {
    display: flex; /* Flexbox layout to align the icon and text horizontally */
    align-items: center; /* Aligns items vertically centered */
    padding: 10px 15px; /* Adds padding around the links */
    color: #ddd; /* Sets the color of the text to a light gray */
    font-size: 16px; /* Sets the font size of the text */
    text-decoration: none; /* Removes the underline from the links */
    border-radius: 8px; /* Rounds the corners of the links */
    transition: all 0.3s ease; /* Smooth transition effect for hover */
}

.sidebar nav ul li a i {
    margin-right: 10px; /* Adds space between the icon and text */
    font-size: 18px; /* Sets the font size of the icon */
    color: #00d1ff; /* Sets the color of the icons to a blue */
    transition: transform 0.3s ease; /* Smooth transformation for the icon on hover */
}

/* Hover Effect */
.sidebar nav ul li a:hover {
    background-color: #007bff; /* Changes the background color of the link on hover */
    color: white; /* Changes the text color to white on hover */
    transform: translateX(5px); /* Adds a slight slide effect to the right when hovered */
}

.sidebar nav ul li a:hover i {
    transform: rotate(360deg); /* Rotates the icon 360 degrees on hover */
}

/* Active Link Styling */
.sidebar nav ul li a.active {
    background-color: #007bff; /* Makes the background blue for active links */
    color: white; /* Changes the text color to white for active links */
}

/* Scrollbar Styling */
.sidebar::-webkit-scrollbar {
    width: 8px; /* Sets the width of the scrollbar */
}

.sidebar::-webkit-scrollbar-thumb {
    background-color: #007bff; /* Sets the color of the scrollbar thumb */
    border-radius: 10px; /* Rounds the corners of the scrollbar thumb */
}

.sidebar::-webkit-scrollbar-track {
    background-color: #1a252f; /* Sets the background color of the scrollbar track */
}



/* Main Content Styling */
.main-content {
    margin-left: 270px; /* Moves the content to the right to accommodate the sidebar */
    padding: 20px; /* Adds padding inside the main content area */
    flex: 1; /* Ensures the content area expands to fill the remaining space */
}

/* Header Styling */
.dashboard-header {
    background-color: #34495e; /* Sets the background color of the header */
    padding: 20px; /* Adds padding inside the header */
    color: white; /* Sets the text color of the header to white */
    text-align: center; /* Centers the text inside the header */
    margin-bottom: 30px; /* Adds space below the header */
    border-radius: 10px; /* Rounds the corners of the header */
}

.dashboard-header .text {
    font-size: 24px; /* Sets the font size of the header text */
    font-weight: bold; /* Makes the text bold */
}

/* Header for Reports Section */
h1, h3 {
    color: #34495e; /* Sets a dark color for the heading text */
    margin: 10px 0; /* Adds vertical space around the headings */
}

/* Form Container */
form {
    background-color: #fff; /* Sets the background color of the form to white */
    padding: 25px; /* Adds padding inside the form */
    border-radius: 12px; /* Rounds the corners of the form */
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Adds shadow around the form */
    max-width: 600px; /* Restricts the maximum width of the form */
    margin: 30px auto; /* Centers the form horizontally and adds margin on top and bottom */
    text-align: center; /* Centers the text inside the form */
    animation: fadeIn 0.5s ease; /* Adds fade-in animation to the form */
}

/* Form Heading */
form h3 {
    font-size: 22px; /* Sets the font size of the form heading */
    color: #34495e; /* Sets the color of the heading to a dark gray */
    margin-bottom: 20px; /* Adds space below the form heading */
    text-transform: uppercase; /* Makes the text uppercase */
    letter-spacing: 1px; /* Adds space between the letters */
}

/* Dropdown Styling */
form select {
    width: 100%; /* Makes the dropdown take up 100% of the form's width */
    padding: 12px 15px; /* Adds padding inside the dropdown */
    margin: 10px 0; /* Adds vertical space around the dropdown */
    border: 1px solid #ddd; /* Adds a light border around the dropdown */
    border-radius: 8px; /* Rounds the corners of the dropdown */
    font-size: 16px; /* Sets the font size of the dropdown text */
    background-color: #f8f9fa; /* Sets the background color of the dropdown */
    color: #333; /* Sets the text color inside the dropdown */
    cursor: pointer; /* Changes the cursor to a pointer when hovering over the dropdown */
    transition: all 0.3s ease; /* Smooth transition for the hover effect */
}

/* Dropdown Hover Effect */
form select:hover {
    border-color: #007bff; /* Changes the border color to blue on hover */
}

/* Button Styling */
form button {
    background-color: #007bff; /* Sets the background color of the button */
    color: white; /* Sets the text color of the button to white */
    padding: 12px 20px; /* Adds padding inside the button */
    border: none; /* Removes the default border */
    border-radius: 8px; /* Rounds the corners of the button */
    font-size: 16px; /* Sets the font size of the button text */
    cursor: pointer; /* Changes the cursor to a pointer when hovering over the button */
    transition: background-color 0.3s ease, transform 0.2s; /* Smooth transition for the hover effect */
    width: 100%; /* Makes the button span the full width of the form */
    margin-top: 10px; /* Adds space above the button */
}

/* Button Hover Effect */
form button:hover {
    background-color: #0056b3; /* Changes the background color of the button on hover */
    transform: scale(1.05); /* Slightly increases the size of the button on hover */
}

/* Table Styling */
table {
    width: 100%; /* Makes the table span the full width of the container */
    border-collapse: collapse; /* Ensures the table borders collapse into a single border */
    margin-top: 20px; /* Adds space above the table */
    background-color: white; /* Sets the background color of the table to white */
    border-radius: 10px; /* Rounds the corners of the table */
    overflow: hidden; /* Hides any overflowed content */
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Adds shadow around the table */
}

table th, table td {
    padding: 12px 15px; /* Adds padding inside each table cell */
    text-align: left; /* Aligns text to the left inside the cells */
    border-bottom: 1px solid #ddd; /* Adds a bottom border to each cell */
}

table th {
    background-color: #34495e; /* Sets the background color of the table header */
    color: white; /* Sets the text color of the header to white */
    text-transform: uppercase; /* Makes the text in the header uppercase */
    font-size: 14px; /* Sets the font size of the header text */
}

table tr:nth-child(even) {
    background-color: #f2f2f2; /* Sets a light gray background color for even rows */
}

table tr:hover {
    background-color: #e9ecef; /* Highlights rows when hovered */
    transition: background-color 0.3s ease; /* Smooth transition for the hover effect */
}

table th, table td {
    padding: 8px 10px; /* Reduces padding in table cells for smaller screens */
    font-size: 14px; /* Reduces font size in table cells for smaller screens */
}

/* Report Heading Styling */
h3 {
    font-size: 24px; /* Sets the font size of the heading */
    color: #34495e; /* Sets the text color */
    margin-bottom: 15px; /* Adds space below the heading */
    text-transform: uppercase; /* Makes the text uppercase */
    border-bottom: 2px solid #007bff; /* Adds an underline with blue color */
    padding-bottom: 5px; /* Adds padding below the underline */
    font-weight: bold; /* Makes the text bold */
    letter-spacing: 1px; /* Adds space between the letters */
}

/* Animation for Form Appearance */
@keyframes fadeIn {
    from {
        opacity: 0; /* Starts with no visibility */
        transform: translateY(-10px); /* Starts with a slight upward shift */
    }
    to {
        opacity: 1; /* Ends with full visibility */
        transform: translateY(0); /* Ends with the element in its normal position */
    }
}

/* Back Button Styling */
.back-btn {
    background-color: #34495e; /* Sets the background color of the button */
    color: white; /* Sets the text color to white */
    padding: 10px 20px; /* Adds padding inside the button */
    border: none; /* Removes the border */
    border-radius: 8px; /* Rounds the corners of the button */
    font-size: 16px; /* Sets the font size */
    cursor: pointer; /* Changes the cursor to a pointer */
    transition: all 0.3s ease; /* Adds smooth transition for hover effects */
    margin-bottom: 20px; /* Adds space below the button */
    display: inline-block; /* Makes the button inline */
    text-decoration: none; /* Removes any text decoration */
}

/* Back Button Hover Effect */
.back-btn:hover {
    background-color: #0056b3; /* Changes the background color on hover */
    transform: scale(1.05); /* Slightly increases the size of the button */
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        width: 200px; /* Reduces the sidebar width on smaller screens */
        padding: 10px; /* Reduces the padding inside the sidebar */
    }

    .sidebar h1 {
        font-size: 20px; /* Reduces the title font size */
    }

    .sidebar nav ul li a {
        font-size: 14px; /* Reduces the font size of the links */
        padding: 8px 10px; /* Reduces the padding inside the links */
    }

    .main-content {
        margin-left: 0; /* Removes the left margin for the main content */
        width: 100%; /* Makes the main content width 100% */
        padding: 15px; /* Reduces the padding inside the main content */
        padding-top: 70px; /* Adds padding on top to avoid header overlap */
    }

    form {
        padding: 20px; /* Reduces padding inside the form */
        max-width: 90%; /* Restricts the form width on smaller screens */
    }

    form h3 {
        font-size: 18px; /* Reduces the font size of the form heading */
    }

    form select,
    form button {
        font-size: 14px; /* Reduces the font size of dropdown and button */
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
            <li><a href="report.php" class="active"><i class="fas fa-chart-line"></i> Reports</a></li>
        </ul>
    </nav>
</aside>

<div class="main-content">
<header class="dashboard-header">
                <h2 class="text">Admin Generate Reports</h2>
            </header>

    <?php if (!$reportType): ?>
        <!-- Report Selection Form -->
        <form method="GET" action="">
            <h3>Select a report to generate:</h3>
            <select name="report" required>
                <option value="">-- Select Report --</option>
                <option value="issued_books">Issued Books Report</option>
                <option value="fine_collection">Fine Collection Report</option>
                <option value="book_availability">Book Availability Report</option>
            </select>
            <button type="submit">Generate Report</button>
        </form>
    <?php else: ?>
        <!-- Display Report Based on Selection -->
        <button onclick="window.location.href='report.php'" class="back-btn">← Back to Report Selection</button>

        <?php if ($reportType === 'issued_books' && $reportData): ?>
            <table>
                <tr>
                <tr>
        <td colspan="6" class="table-header">
            <h3>Issued Books Report</h3>
        </td>
    </tr>
                    <th>Transaction ID</th>
                    <th>Borrower</th>
                    <th>Book</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($reportData as $row): ?>
                    <tr>
                        <td><?= $row['T_id'] ?></td>
                        <td><?= $row['Borrower'] ?></td>
                        <td><?= $row['Book'] ?></td>
                        <td><?= $row['Issue_date'] ?></td>
                        <td><?= $row['Due_date'] ?></td>
                        <td><?= $row['Status'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($reportType === 'fine_collection' && $reportData): ?>
            <h3>Fine Collection Report</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Order ID</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
                <?php foreach ($reportData as $payment): ?>
                    <tr>
                        <td><?= $payment['id'] ?></td>
                        <td><?= $payment['purchase_order_id'] ?></td>
                        <td><?= $payment['email'] ?></td>
                        <td>$<?= $payment['amount'] ?></td>
                        <td><?= $payment['status'] ?></td>
                        <td><?= $payment['created_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($reportType === 'book_availability' && $reportData): ?>
            <h3>Book Availability Report</h3>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($reportData as $row): ?>
                    <tr>
                        <td><?= $row['Title'] ?></td>
                        <td><?= $row['Category'] ?></td>
                        <td><?= $row['Quantity'] ?></td>
                        <td><?= $row['Status'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php else: ?>
            <p>No data available for the selected report.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
