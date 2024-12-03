<?php
session_start();
error_reporting(0);
include('includes/config.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibraryHub - Your Digital Library</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #4f46e5;
            --primary-dark: #4338ca;
            --bg-light: #f4f7fc;
            --text-light: #ffffff;
            --text-dark: #333333;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            margin: 0;
        }

        /* Navbar */
        .navbar {
            background-color: #0D203B;
            color: var(--text-light);
            padding: 0.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 40px;
            height: auto;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 1rem;
            margin-left: auto;
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

        /* Logout Button */
        .logout-btn {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s;
            cursor: pointer;
            margin-left: 1rem;
        }

        .logout-btn:hover {
            background-color: var(--primary-dark);
        }

       
        /* Account Dropdown */
        .account-dropdown {
            position: relative;
        }

        

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 150px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            right: 0;
            z-index: 1;
            border-radius: 0.375rem;
            overflow: hidden;
        }

        .dropdown-content a {
            color: var(--text-dark);
            padding: 0.75rem 1rem;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: var(--bg-light);
        }

        .account-dropdown:hover .dropdown-content {
            display: block;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
                        url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 6rem 2rem;
            text-align: center;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 600;
        }

        .hero p {
            font-size: 1.2rem;
            margin-top: 1rem;
        }

        .search-bar {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
        }

        .search-bar input[type="text"] {
            padding: 0.75rem;
            width: 60%;
            border: none;
            border-radius: 0.375rem 0 0 0.375rem;
            outline: none;
        }

        .search-bar button {
            padding: 0.75rem 1.5rem;
            border: none;
            background-color: var(--primary-color);
            color: var(--text-light);
            font-weight: 600;
            border-radius: 0 0.375rem 0.375rem 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-bar button:hover {
            background-color: var(--primary-dark);
        }

        /* Featured Books Section */
        .featured-books {
            padding: 2rem;
            text-align: center;
        }

        .featured-books h2 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .book-grid {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .book-card {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 0.375rem;
            overflow: hidden;
            width: 250px;
        }

        .book-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .book-info {
            padding: 1rem;
            text-align: left;
        }

        .book-info h3 {
            font-size: 1.25rem;
            color: var(--text-dark);
            margin: 0;
        }

        .book-info p.author {
            color: #777;
            font-size: 0.875rem;
        }

        footer {
            background-color: #0D203B;
            color: var(--text-light);
            padding: 1rem;
            text-align: center;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">
        <img src="assets/img/magic book.logo.png" alt="LibraryHub Logo" class="logo">
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="listed-books.php">Books</a></li>
        <li><a href="authors.php">Authors</a></li>
        <li class="account-dropdown">
            <span class="account-btn">Account</span>
            <div class="dropdown-content">
                <a href="my-profile.php">My Profile</a>
                <a href="change-password.php">Change Password</a>
            </div>
        </li>
    </ul>
    <a href="logout.php" class="logout-btn">Logout</a>
</nav>


<!-- Hero Section -->
<section class="hero">
    <h1>Discover Your Next Great Read</h1>
    <p>Access thousands of books, manage your reading list, and connect with fellow readers.</p>
    <div class="search-bar">
        <input type="text" placeholder="Search for books, authors, or categories...">
        <button type="submit">Search</button>
    </div>
</section>

<!-- Featured Books Section -->
<section class="featured-books">
    <h2>Featured Books</h2>
    <div class="book-grid">
        <div class="book-card">
            <img src="https://d28hgpri8am2if.cloudfront.net/book_images/onix/cvr9781524879761/the-great-gatsby-9781524879761_hr.jpg" alt="Book 1" class="book-cover">
            <div class="book-info">
                <h3>The Great Gatsby</h3>
                <p class="author">F. Scott Fitzgerald</p>
            </div>
        </div>
        <div class="book-card">
            <img src="https://tse4.mm.bing.net/th?id=OIP.T55C4BiOVfcS3WFdCM3xdwHaK2&pid=Api&P=0&h=180" alt="Book 2" class="book-cover">
            <div class="book-info">
                <h3>To Kill a Mockingbird</h3>
                <p class="author">Harper Lee</p>
            </div>
        </div>
    </div>
</section>

<footer>
    © 2024 LibraryHub. All rights reserved.
</footer>

</body>
</html>
<?php  ?>
