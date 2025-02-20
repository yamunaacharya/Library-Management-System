<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}

$student_email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $student_email; 
    $title = mysqli_real_escape_string($conn, $_POST['title']);

    $query = "SELECT 
                  t.T_id, t.Due_date, t.Fine, t.Status, 
                  b.B_id, b.Quantity, b.Status AS Book_Status 
              FROM transaction t
              JOIN books b ON t.B_id = b.B_id
              WHERE t.S_email = ? AND b.Title = ? AND t.Status = 'Issued'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $title);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $transaction_id = $row['T_id'];
        $book_id = $row['B_id'];
        $due_date = $row['Due_date'];
        $current_date = date("Y-m-d");

        if ($current_date <= $due_date) {
            $return_query = "UPDATE transaction SET Return_date = ?, Status = 'Returned' WHERE T_id = ?";
            $stmt = $conn->prepare($return_query);
            $stmt->bind_param("si", $current_date, $transaction_id);

            if ($stmt->execute()) {
                $update_quantity_query = "UPDATE books SET Quantity = Quantity + 1, Status = 'Available' WHERE B_id = ?";
                $update_stmt = $conn->prepare($update_quantity_query);
                $update_stmt->bind_param("i", $book_id);
                $update_stmt->execute();

                echo "<script>alert('Book returned successfully!'); window.location.href='dashboard.php';</script>";
            } else {
                echo "<script>alert('Error processing the return. Please try again.');</script>";
            }
        } else {
            $late_days = (strtotime($current_date) - strtotime($due_date)) / (60 * 60 * 24);
            $fine = $late_days * 3;

            $fine_query = "UPDATE transaction SET Fine = ? WHERE T_id = ?";
            $stmt = $conn->prepare($fine_query);
            $stmt->bind_param("di", $fine, $transaction_id);
            $stmt->execute();

            $_SESSION['transaction_id'] = $transaction_id;
            $_SESSION['fine_amount'] = $fine;
            echo "<script>
                alert('You are $late_days days late. You have accrued a fine of $fine. Please proceed to pay the fine.');
                window.location.href = 'pay_fine.php';
            </script>";
            exit;
        }
    } else {
        echo "<script>alert('You haven\'t issued this book.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
/* Body Styling */
body {
    display: flex;
    justify-content: center;  /* Centers the content horizontally */
    align-items: flex-start;  /* Aligns the content at the top */
    min-height: 100vh;  /* Ensures the body covers full viewport height */
    margin: 0;
    background-color: #f4f4f4;
    padding-left: 250px; /* Adjust based on the sidebar's width */
    box-sizing: border-box;
}

/* Main Content Container */
.main-content {
    width: 100%;
    padding: 30px;
    margin-top: 50px;
    text-align: center; 
}

/* Container Styling */
.container {
background-color: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    box-sizing: border-box;
    margin-top: 30px;
    width: 50%; /* Let the container adjust its width based on content */
    display: inline-block; /* Make the container shrink to fit content */
}

/* Header Styling */
.dashboard-header h3 {
    text-align: center;
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 30px;
}

/* Form Styling */
form {
    display: flex;
    flex-direction: column;
    gap: 15px;  /* Reduce spacing between form elements */

}

/* Label Styling */
form label {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

/* Input and Select Fields Styling */
form input, form select {
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
    outline: none;
    width: 100%;
    box-sizing: border-box;
}

/* Input/Select Focus Effect */
form input:focus, form select:focus {
    border-color: #3498db;
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
}

/* Button Styling */
button {
    padding: 12px 20px;
    font-size: 16px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s;
    align-self: center;
    width: 100%;
}

button:hover {
    background-color: #2980b9;
}

/* Responsive Design */
@media (max-width: 768px) {
    body {
        padding-left: 0;  /* Remove padding on smaller screens */
    }

    .container {
        width: 80%;  /* Increase container width on small screens */
        max-width: 400px;
    }

    form input, form select {
        padding: 14px;
    }

    button {
        width: 100%;
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
    <main class="main-content">
        <header class="dashboard-header">
           </header>
        <div class="container my-5">
            <form method="POST" action="">
            <h3 class="text-center">Return Issued Book</h3><br>
                <label for="email">Your Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student_email); ?>" readonly>

                <?php
                // Fetch the list of issued books for the student
                $issued_books_query = "SELECT b.Title FROM transaction t JOIN books b ON t.B_id = b.B_id WHERE t.S_email = ? AND t.Status = 'Issued'";
                $stmt = $conn->prepare($issued_books_query);
                $stmt->bind_param("s", $student_email);
                $stmt->execute();
                $issued_books_result = $stmt->get_result();
                ?>

                <label for="title">Book Title:</label>
                <select id="title" name="title" required>
                    <option value="" disabled selected>Select a book</option>
                    <?php while ($book = $issued_books_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($book['Title']); ?>"><?php echo htmlspecialchars($book['Title']); ?></option>
                    <?php endwhile; ?>
                </select>

                <button type="submit" class=" btn-primary">Return Book</button>
            </form>
        </div>
    </main>
</body>
</html>
