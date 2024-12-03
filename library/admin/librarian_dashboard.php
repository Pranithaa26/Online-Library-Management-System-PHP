<?php
session_start();
include('includes/config.php');

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php"); // Redirect to the login page
    exit();
}

try {
    // Query for total books
    $sql_books = "SELECT COUNT(ISBN) as total FROM book";
    $query_books = $dbh->prepare($sql_books);
    $query_books->execute();
    $total_books = $query_books->fetch(PDO::FETCH_OBJ)->total ?? 0;

    // Query for books not returned
    $sql_issued = "SELECT COUNT(issueID) as notReturned FROM issuedBooks WHERE returnDate IS NULL";
    $query_issued = $dbh->prepare($sql_issued);
    $query_issued->execute();
    $not_returned_books = $query_issued->fetch(PDO::FETCH_OBJ)->notReturned ?? 0;

    // Query for total students
    $sql_students = "SELECT COUNT(userID) as total FROM user";
    $query_students = $dbh->prepare($sql_students);
    $query_students->execute();
    $total_students = $query_students->fetch(PDO::FETCH_OBJ)->total ?? 0;

    // Query for total authors
    $sql_authors = "SELECT COUNT(DISTINCT authors) as total FROM book";
    $query_authors = $dbh->prepare($sql_authors);
    $query_authors->execute();
    $total_authors = $query_authors->fetch(PDO::FETCH_OBJ)->total ?? 0;

    // Query for total categories
    $sql_categories = "SELECT COUNT(DISTINCT category) as total FROM book";
    $query_categories = $dbh->prepare($sql_categories);
    $query_categories->execute();
    $total_categories = $query_categories->fetch(PDO::FETCH_OBJ)->total ?? 0;

    // Query for recently registered students
    $sql_recent_students = "SELECT name FROM user ORDER BY userID DESC LIMIT 5";
    $query_recent_students = $dbh->prepare($sql_recent_students);
    $query_recent_students->execute();
    $recent_students = $query_recent_students->fetchAll(PDO::FETCH_OBJ) ?? [];

    // Query for newly added books
    $sql_recent_books = "SELECT title FROM book ORDER BY RegDate DESC LIMIT 5";
    $query_recent_books = $dbh->prepare($sql_recent_books);
    $query_recent_books->execute();
    $recent_books = $query_recent_books->fetchAll(PDO::FETCH_OBJ) ?? [];
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Library Management</title>
    <style>
        /* Existing styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body, html {
            height: 100%;
            display: flex;
            flex-direction: column;
            background-color: #f4f6f8;
        }

        .container {
            display: flex;
            flex: 1;
        }

        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            margin-top: 20px;
            flex-grow: 1;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            display: block;
        }

        .dashboard {
            flex: 1;
            padding: 20px;
            background-color: #f4f6f8;
            display: flex;
            flex-direction: column;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        header h1 {
            font-size: 2em;
            color: #34495e;
        }

        .search-bar input {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 250px;
            font-size: 1em;
        }

        .stats {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #fff;
            padding: 20px;
            flex: 1;
            min-width: 200px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.2);
        }

        .stat-card h3 {
            font-size: 2em;
            color: #2c3e50;
        }

        .stat-card p {
            font-size: 1em;
            color: #7f8c8d;
            margin-top: 10px;
        }

        .recent-activity {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .activity-card {
            background: #fff;
            padding: 20px;
            flex: 1;
            min-width: 300px;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .activity-card h3 {
            font-size: 1.5em;
            color: #2c3e50;
            margin-bottom: 15px;
            text-align: center;
        }

        .activity-card ul {
            list-style-type: none;
            padding: 0;
        }

        .activity-card ul li {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
            color: #34495e;
            text-align: center;
        }

        .activity-card ul li:last-child {
            border-bottom: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .search-bar input {
                width: 150px;
            }

            .stats, .recent-activity {
                flex-direction: column;
            }
        }
    </style>
    <script>
        function searchDashboard() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            
            // Handle search for stat-cards
            let statCards = document.getElementsByClassName('stat-card');
            for (let i = 0; i < statCards.length; i++) {
                let cardText = statCards[i].innerText.toLowerCase();
                statCards[i].style.display = cardText.includes(input) ? 'block' : 'none';
            }

            // Handle search for activity-cards
            let activityCards = document.getElementsByClassName('activity-card');
            for (let i = 0; i < activityCards.length; i++) {
                let cardText = activityCards[i].innerText.toLowerCase();
                activityCards[i].style.display = cardText.includes(input) ? 'block' : 'none';
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Library System</h2>
            <nav>
                <ul>
                    <li><a href="librarian_dashboard.php">Dashboard</a></li>
                    <li><a href="manage-categories.php">Categories</a></li>
                    <li><a href="manage-authors.php">Authors</a></li>
                    <li><a href="manage-books.php">Books</a></li>
                    <li><a href="manage-issued-books.php">Issue Books</a></li>
                    <li><a href="reg-students.php">Registered Students</a></li>
                    <li><a href="change-password.php">Change Password</a></li>
                    <li><a href="logout.php">Logout</a></li> <!-- Logout link -->
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard">
            <!-- Header -->
            <header>
                <h1>Welcome to the Admin Dashboard</h1>
                <div class="search-bar">
                    <input type="text" id="searchInput" onkeyup="searchDashboard()" placeholder="Search Dashboard...">
                </div>
            </header>

            <!-- Stats Section -->
            <section class="stats">
                <div class="stat-card">
                    <h3><?php echo $total_books; ?></h3>
                    <p>Total Books</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $not_returned_books; ?></h3>
                    <p>Books Not Returned</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $total_students; ?></h3>
                    <p>Total Students</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $total_authors; ?></h3>
                    <p>Total Authors</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $total_categories; ?></h3>
                    <p>Total Categories</p>
                </div>
            </section>

            <!-- Recent Activity Section -->
            <section class="recent-activity">
                <div class="activity-card">
                    <h3>Recent Students</h3>
                    <ul>
                        <?php foreach ($recent_students as $student): ?>
                            <li><?php echo $student->name; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="activity-card">
                    <h3>New Books</h3>
                    <ul>
                        <?php foreach ($recent_books as $book): ?>
                            <li><?php echo $book->title; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
