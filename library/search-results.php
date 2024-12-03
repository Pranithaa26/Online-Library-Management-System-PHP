<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Get the query parameter from the URL
$query = isset($_GET['query']) ? $_GET['query'] : ''; // Default to empty if not set

// If no query is provided, redirect to the home page
if (empty($query)) {
    header('location:index.php');
    exit;
}

// Perform the database query to search for books
$sql = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $query . "%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - LibraryHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
         :root {
            --primary-color: #4f46e5;
            --primary-dark: #4338ca;
            --secondary-color: #f5a623;
            --bg-light: #fafafa;
            --bg-dark: #1f2937;
            --text-light: #ffffff;
            --text-dark: #333333;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Navigation Bar */
        .navbar {
            background-color: var(--bg-dark);
            color: var(--text-light);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar .logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 1.5rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-light);
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .navbar .login-btn {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 0.5rem 1.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s;
        }

        .navbar .login-btn:hover {
            background-color: var(--primary-dark);
        }

        /* Dropdown container */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Dropdown content (hidden by default) */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: var(--bg-light);
            min-width: 160px;
            box-shadow: var(--shadow);
            z-index: 1;
            border-radius: 0.375rem;
            padding: 0.5rem 0;
        }

        .dropdown-content a {
            color: var(--text-dark);
            padding: 0.8rem 1rem;
            text-decoration: none;
            display: block;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .dropdown-content a:hover {
            background-color: var(--primary-light);
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                        url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&q=80');
            background-size: cover;
            background-position: center;
            padding: 8rem 2rem;
            color: var(--text-light);
            text-align: center;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .search-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            max-width: 700px;
            margin: 0 auto;
        }

        .search-container input {
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 0.375rem;
            font-size: 1.1rem;
            flex-grow: 1;
        }

        .search-container button {
            padding: 0.8rem 1.5rem;
            background-color: var(--primary-color);
            color: var(--text-light);
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-container button:hover {
            background-color: var(--primary-dark);
        }

        /* Featured Books Section */
        .featured-books {
            padding: 4rem 2rem;
            background-color: var(--bg-light);
        }

        .featured-books h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 3rem;
            font-weight: 600;
        }

        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
        }

        .book-card {
            background-color: var(--text-light);
            border-radius: 0.5rem;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: transform 0.3s;
        }

        .book-card:hover {
            transform: translateY(-10px);
        }

        .book-cover {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .book-info {
            padding: 1rem;
        }

        .book-info h3 {
            font-size: 1.2rem;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .book-info .author {
            font-size: 1rem;
            color: var(--primary-color);
        }

        /* Footer */
        footer {
            background-color: var(--bg-dark);
            color: var(--text-light);
            padding: 3rem 2rem;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .footer-section h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .footer-section ul {
            list-style: none;
            margin: 0;
        }

        .footer-section ul li {
            margin-bottom: 1rem;
        }

        .footer-section a {
            text-decoration: none;
            color: var(--text-light);
            font-size: 1rem;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: var(--secondary-color);
        }

        @media (max-width: 768px) {
            .nav-links {
                display: block;
                gap: 1rem;
            }

            .book-grid {
                grid-template-columns: 1fr 1fr;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .navbar .toggle-btn {
                display: block;
                background-color: var(--primary-color);
                padding: 1rem;
                color: #fff;
                border: none;
                font-size: 1.2rem;
            }

            .navbar .nav-links {
                display: none;
                flex-direction: column;
                width: 100%;
            }

            .navbar .nav-links.show {
                display: block;
            }

            .navbar .nav-links a {
                padding: 1rem;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">
        <img src="https://i.pinimg.com/474x/13/7e/e9/137ee9c5b52fc1b6d8f4e9b731c5d032.jpg" alt="Logo" class="logo">
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="books.php">Books</a></li>
        <li><a href="authors.php">Authors</a></li>
        <li><a href="my-account.php">My Account</a></li>
        <li><a href="logout.php" class="login-btn">Logout</a></li>
    </ul>
</nav>

<section class="search-results">
    <h2>Search Results for: "<?php echo htmlspecialchars($query); ?>"</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="book-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="book-card">
                    <img src="path/to/book/cover/<?php echo $row['cover_image']; ?>" alt="Book Cover" class="book-cover">
                    <div class="book-info">
                        <h3><?php echo $row['title']; ?></h3>
                        <p class="author"><?php echo $row['author']; ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No results found for "<?php echo htmlspecialchars($query); ?>".</p>
    <?php endif; ?>
</section>

<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>About</h3>
            <ul>
                <li><a href="#">Our Story</a></li>
                <li><a href="#">Our Mission</a></li>
                <li><a href="#">Team</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Help</h3>
            <ul>
                <li><a href="#">Contact Us</a></li>
                <li><a href="#">FAQs</a></li>
                <li><a href="#">Support</a></li>
            </ul>
        </div>
    </div>
</footer>

</body>
</html>
