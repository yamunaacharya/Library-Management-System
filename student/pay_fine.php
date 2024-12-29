<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}

$student_email = $_SESSION['email'];

$query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "Error: User details not found.";
    exit;
}

$transaction_query = "SELECT Fine FROM transaction WHERE S_email = ? AND Status = 'Issued' LIMIT 1";
$stmt = $conn->prepare($transaction_query);
$stmt->bind_param("s", $student_email);
$stmt->execute();
$transaction = $stmt->get_result()->fetch_assoc();

if (!$transaction) {
    echo "Error: No outstanding transactions found.";
    exit;
}

$fine_amount = $transaction['Fine'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $user['fullname'];
    $email = $user['email'];
    $amount = $fine_amount; 
    $purchase_order_id = uniqid();
    $purchase_order_name = "Library Fee";

    $insert_query = "INSERT INTO payments (purchase_order_id, email, name, amount, status) VALUES (?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sssd", $purchase_order_id, $email, $name, $amount);

    if (!$stmt->execute()) {
        echo "Error: Unable to save payment details.";
        exit;
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/initiate/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(array(
            "return_url" => "http://localhost/library/payment_success.php",
            "website_url" => "http://localhost/library",
            "amount" => $amount * 100, 
            "purchase_order_id" => $purchase_order_id,
            "purchase_order_name" => $purchase_order_name,
            "customer_info" => array(
                "name" => $name,
                "email" => $email,
            )
        )),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Key e2ac8b6feae440a1838ee5d06d4cc05e',
            'Content-Type: application/json',
        ),
    ));

    $response = curl_exec($curl);
    $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl)) {
        echo "cURL Error: " . curl_error($curl);
        curl_close($curl);
        exit;
    }

    curl_close($curl);

    if ($httpStatus == 200 && $response) {
        $responseData = json_decode($response, true);
        if (isset($responseData["payment_url"])) {
            // Redirect to Khalti's payment page
            header('Location: ' . $responseData["payment_url"]);
            exit;
        } else {
            echo "Error: Payment URL not found in the response.";
            echo "<pre>";
            print_r($responseData);
            echo "</pre>";
        }
    } else {
        echo "Error: Failed to initiate payment. Please try again.";
        echo "<pre>";
        echo "HTTP Status: $httpStatus\nResponse: $response";
        echo "</pre>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Initiation</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <pre>
        <h1>Payment Initiation</h1>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Amount:</strong> NPR <?php echo htmlspecialchars($fine_amount); ?></p>
        <form method="POST" action="">
            <button type="submit">Pay Now</button>
        </form>
        </pre>
    </div>
</body>
</html>
