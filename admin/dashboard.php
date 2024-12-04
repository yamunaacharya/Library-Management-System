<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Admin Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
        }
        .sidebar h1 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        .sidebar ul {
            list-style-type: none;
        }
        .sidebar ul li {
            margin-bottom: 10px;
        }
        .sidebar ul li a {
            color: #ecf0f1;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .sidebar ul li a:hover, .sidebar ul li a.active {
            background-color: #34495e;
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
            background-color: #ecf0f1;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .dashboard-header h2 {
            font-size: 1.8em;
        }
        .search-bar {
            display: flex;
        }
        .search-bar input {
            padding: 8px;
            border: 1px solid #bdc3c7;
            border-radius: 4px 0 0 4px;
        }
        .search-bar button {
            padding: 8px 15px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .stat-card h3 {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .stat-card p {
            font-size: 2em;
            font-weight: bold;
            color: #2980b9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-buttons button {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .edit-btn {
            background-color: #f39c12;
            color: #fff;
        }
        .delete-btn {
            background-color: #e74c3c;
            color: #fff;
        }
        .add-btn {
            padding: 10px 15px;
            background-color: #2ecc71;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .add-btn:hover, .edit-btn:hover, .delete-btn:hover, .search-bar button:hover {
            opacity: 0.8;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
            }
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .search-bar {
                width: 100%;
                margin-top: 10px;
            }
            .search-bar input {
                flex-grow: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h1>Library Admin</h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="adduser.php">Users</a></li>
                    <li><a href="add_books.php">AddBooks</a></li>
                    <li><a href="displaybooks.php">Books Details</a></li>
                    <li><a href="#">Reports</a></li>
                    <li><a href="#">Settings</a></li>
                </ul>
            </nav>
        </aside>
        <!-- <main class="main-content">
            <header class="dashboard-header">
                <h2>Dashboard</h2>
                <div class="search-bar">
                    <input type="text" placeholder="Search..." aria-label="Search">
                    <button type="button">Search</button>
                </div>
            </header>
            <section class="dashboard-stats">
                <div class="stat-card">
                    <h3>Total Books</h3>
                    <p>16</p>
                </div>
                <div class="stat-card">
                    <h3>Active Users</h3>
                    <p>5</p>
                </div>
                <div class="stat-card">
                    <h3>Current Loans</h3>
                    <p>8</p>
                </div>
                <div class="stat-card">
                    <h3>Overdue Books</h3>
                    <p>2</p>
                </div>
            </section>
        </main> -->
    </div>
</body>
</html>