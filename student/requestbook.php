<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo "<script>alert('Session expired or invalid. Please log in again.'); window.location.href = '../auth/login.php';</script>";
    exit;
}

$s_email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $book_title = mysqli_real_escape_string($conn, $_POST['book_title']); 

    if ($email !== $s_email) {
        echo "<script>alert('The entered email does not match your registered email.'); window.history.back();</script>";
        exit;
    }

    // Check if the book exists and get its ID and quantity
    $book_query = "SELECT B_id, Quantity FROM books WHERE Title = '$book_title' LIMIT 1";
    $book_result = mysqli_query($conn, $book_query);

    if (mysqli_num_rows($book_result) === 0) {
        echo "<script>alert('Sorry, this book is not available in the library.'); window.history.back();</script>";
        exit;
    }

    $book_data = mysqli_fetch_assoc($book_result);
    $book_id = $book_data['B_id'];
    $quantity = $book_data['Quantity'];

    // Check if the student has already issued this book and hasn't returned it yet
    $issued_check_query = "SELECT * FROM transaction WHERE S_email = '$s_email' AND B_id = $book_id AND Status = 'Issued' AND Return_date IS NULL";
    $issued_check_result = mysqli_query($conn, $issued_check_query);

    if (mysqli_num_rows($issued_check_result) > 0) {
        echo "<script>alert('You have already issued this book and have not returned it yet.'); window.history.back();</script>";
        exit;
    }

    // Check if the book is already requested or issued
    $check_query = "SELECT * FROM transaction WHERE B_id = $book_id AND Status IN ('Requested', 'Issued')";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) >= $quantity) {
        echo "<script>alert('Sorry, this book is currently unavailable as all copies are either requested or issued.'); window.history.back();</script>";
        exit;
    }

    // Check if the student already has a pending request for this book
    $student_check_query = "SELECT * FROM transaction WHERE S_email = '$s_email' AND B_id = $book_id AND Status = 'Requested'";
    $student_check_result = mysqli_query($conn, $student_check_query);

    if (mysqli_num_rows($student_check_result) > 0) {
        echo "<script>alert('You have already requested this book.'); window.history.back();</script>";
        exit;
    }

    // Check if the student has reached the limit of 5 requested books
    $count_query = "SELECT COUNT(*) AS book_count FROM transaction WHERE S_email = '$s_email' AND Status = 'Requested'";
    $count_result = mysqli_query($conn, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);

    if ($count_row['book_count'] >= 5) {
        echo "<script>alert('You can only request up to 5 books.'); window.history.back();</script>";
        exit;
    }

    // Add the request to the transaction table
    $status = 'Requested';
    $issue_date = NULL; 
    $due_date = NULL;
    $return_date = NULL;
    $fine = 0.00;

    $insert_query = "INSERT INTO transaction (S_email, B_id, Issue_date, Due_date, Return_date, Fine, Status) 
                     VALUES ('$s_email', $book_id, '$issue_date', '$due_date', '$return_date', $fine, '$status')";

    if (mysqli_query($conn, $insert_query)) {
        echo "<script>alert('Book request submitted successfully!'); window.location.href = 'requestbook.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.history.back();</script>";
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
/* General styles for the main content */
.main-content {
    padding: 30px 40px;
    background-color: #ecf0f1;
    min-height: 100vh; /* Ensure it fills the screen */
    margin-left: 220px; /* Ensure content doesn't go behind the sidebar (adjust the value based on sidebar width) */
    transition: margin-left 0.3s ease; /* Smooth transition if the sidebar is hidden */
    
}

/* Styling for the header section */
.dashboard-header {
    background: #34495e;
    padding: 10px;
    border-radius: 12px;
    color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 40px; /* Space below the header */ 
    margin-top: 0;  /* Remove any extra margin on top */
    position: relative;
    top: -30px;
    left: 1;
    z-index: 1000;
}

.dashboard-header h3 {
    text-align: center;
    margin: 0;
    font-size: 28px;
    font-weight: bold;
    letter-spacing: 1px;
}

/* Form container styling */
.container {
    max-width: 700px;
    margin: 0 auto; /* Center the form horizontally */
    background-color: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

.container:hover {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

/* Form group styling */
.form-group {
    margin-bottom: 25px;
}

/* Label styling */
label {
    font-size: 18px;
    color: #333;
    margin-bottom: 10px;
    display: block;
    font-weight: 600;
}

/* Input fields styling */
input.form-control {
    width: 100%;
    padding: 15px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
    transition: all 0.3s ease;
}

input.form-control:focus {
    border-color: #3498db;
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.7);
    outline: none;
}

/* Button styling */
button.btn-primary {
    width: 100%;
    padding: 15px;
    background-color: #3498db;
    color: white;
    font-size: 18px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(52, 152, 219, 0.2);
}

button.btn-primary:hover {
    background-color: #2980b9;
    box-shadow: 0 6px 12px rgba(52, 152, 219, 0.3);
}

/* Button focus effect */
button.btn-primary:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .main-content {
        margin-left: 0; /* Remove the left margin on small screens */
        padding: 20px;
    }

    .container {
        padding: 20px;
    }

    .dashboard-header h3 {
        font-size: 24px;
    }

    input.form-control {
        font-size: 14px;
    }

    button.btn-primary {
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    .dashboard-header h3 {
        font-size: 20px;
    }
}
body {
    font-family: Arial, sans-serif; /* Sets the font to Arial, with a fallback to sans-serif */
    margin: 0; /* Removes default margin from the body */
    padding: 0; /* Removes default padding from the body */
    display: flex; /* Uses Flexbox for layout */
    flex-direction: column; /* Arranges children elements in a column (vertical layout) */
    min-height: 100vh; /* Ensures the body takes at least the full height of the viewport */
    background-color: #f4f7fc; /* Sets a light grey-blue background color */
}
/* General container styles */
.container {
    max-width: 500px; /* Limit the width of the form */
    margin: 0 auto;   /* Center the container horizontally */
    background-color: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center; /* Horizontally center content */
    justify-content: flex-start; /* Align form items to the top */
    height: auto; /* Let the height adjust based on content */
}

/* Container hover effect */
.container:hover {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

/* Form group styling */
.form-group {
    width: 100%; /* Ensure form groups take up the full width of the container */
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
}

/* Label styling */
label {
    font-size: 16px;
    color: #333;
    font-weight: bold;
    margin-bottom: 8px;
    display: block;
}

/* Input fields styling */
input.form-control {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
    transition: all 0.3s ease;
}

/* Focused input styling */
input.form-control:focus {
    border-color: #3498db;
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.7);
    outline: none;
}

/* Submit button styling */
button.btn-primary {
    width: 100%;
    padding: 12px;
    background-color: #3498db;
    color: white;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(52, 152, 219, 0.2);
}

/* Button hover effect */
button.btn-primary:hover {
    background-color: #2980b9;
    box-shadow: 0 6px 12px rgba(52, 152, 219, 0.3);
}

/* Button focus effect */
button.btn-primary:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.3);
}

/* Responsive Design for smaller screens */
@media (max-width: 768px) {
    .container {
        padding: 20px;
    }

    button.btn-primary {
        font-size: 16px;
    }

    input.form-control {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 15px;
    }

    button.btn-primary {
        font-size: 14px;
    }

    input.form-control {
        font-size: 12px;
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
            <h3 class="text-center">Request a Book</h3><br>
        </header>
        <div class="container my-5">
            <!-- Form to request a book -->
            <form action="" method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter your registered email" value="<?php echo $s_email; ?>" required>
                </div>
                <div class="form-group">
                    <label for="book_title">Book Title</label>
                    <input type="text" class="form-control" name="book_title" id="book_title" placeholder="Enter Book Title" required>
                </div>
                <button type="submit" class="btn-primary" name="submit">Request Book</button>
            </form>
        </div>
    </main>
</body>
</html>
