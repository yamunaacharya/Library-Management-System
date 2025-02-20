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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Report Generation</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Admin Reports</h1>

    <h3>Select a report to generate:</h3>
    <form method="GET" action="">
        <select name="report" required>
            <option value="">-- Select Report --</option>
            <option value="issued_books">Issued Books Report</option>
            <option value="fine_collection">Fine Collection Report</option>
            <option value="book_availability">Book Availability Report</option>
        </select>
        <button type="submit">Generate Report</button>
    </form>

    <?php if ($reportType === 'issued_books' && $reportData): ?>
        <h3>Issued Books Report</h3>
        <table>
            <tr>
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
        <table border="1">
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
    <?php elseif ($reportType): ?>
        <p>No data available for the selected report.</p>
    <?php endif; ?>

</body>
</html>
