<?php
require '../includes/config.php';
session_start();

if (!isset($_GET['pidx']) || !isset($_GET['transaction_id']) || !isset($_GET['amount']) || !isset($_GET['status']) || !isset($_GET['purchase_order_id'])) {
    echo "Invalid payment details.";
    exit;
}

$pidx = $_GET['pidx'];
$transaction_id = $_GET['transaction_id'];
$amount = $_GET['amount'];
$status = $_GET['status'];
$purchase_order_id = $_GET['purchase_order_id'];

if ($status === 'Completed') {
    // Update payment status
    $update_payment_query = "UPDATE payments SET status = 'Completed' WHERE purchase_order_id = ?";
    $stmt = $conn->prepare($update_payment_query);
    $stmt->bind_param("s", $purchase_order_id);
    $stmt->execute();

    // Update transaction status
    $update_transaction_query = "UPDATE transaction SET Status = 'Returned', Fine = 0 WHERE payment_id = ?";
    $stmt = $conn->prepare($update_transaction_query);
    $stmt->bind_param("s", $purchase_order_id);
    $stmt->execute();

    echo "<script>alert('Payment successful and transaction updated.'); window.location.href='dashboard.php';</script>";
} else {
    echo "<script>alert('Payment failed. Please try again.'); window.location.href='pay_fine.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .success-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .success-container h1 {
            color: purple;
            font-size: 2em;
            margin-bottom: 10px;
        }
        .success-container p {
            color: #555;
            margin: 5px 0;
            line-height: 1.5;
        }
        .success-container .highlight {
            font-weight: bold;
            color: #333;
        }
        .success-container .details {
            margin-top: 15px;
            text-align: left;
            font-size: 0.9em;
            color: #444;
        }
        .success-container a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: purple;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .success-container a:hover {
            background-color: violet;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <h1>Payment Successful</h1>
        <p>Thank you for your payment!</p>
        <div class="details">
            <p><span class="highlight">Order ID:</span> <?php echo $orderId; ?></p>
            <p><span class="highlight">Order Name:</span> <?php echo $orderName; ?></p>
            <p><span class="highlight">Amount Paid:</span> NPR <?php echo $amount; ?></p>
            <p><span class="highlight">Mobile:</span> <?php echo $mobile; ?></p>
            <p><span class="highlight">Status:</span> <?php echo $status; ?></p>
        </div>
        <a href="dashboard.php">Go to Dashboard</a>
    </div>
</body>
</html>
