<?php
// Include the database connection file
include('db_connection.php');

// Fetch all books along with category and author
$sql = "SELECT book.ISBN, book.title, book.yearOfPublication, book.totalCopies, book.noOfCopiesOnShelf, book.authors, book.category, book.image 
        FROM book";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Online Library Management System | Listed Books</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <style>
        /* Navbar Styling */
        .navbar {
            background-color: #0D203B;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar .logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
            margin-right: 1rem;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 1.5rem;
            margin-left: auto;
        }

        .nav-links a {
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #FFD700;
        }

        .login-btn {
            background-color: #FFD700;
            color: #0D203B;
            padding: 0.5rem 1.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #FFD700;
            color: #0D203B;
        }

        /* Book Grid Layout */
        .book-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s;
            font-size: 0.9rem;
        }

        .book-card:hover {
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .book-card img {
            max-width: 80%;
            height: auto;
            border-radius: 5px;
            margin: 0 auto;
            display: block;
        }

        .book-card h5 {
            margin-top: 10px;
            font-size: 1.125rem;
            font-weight: bold;
        }

        .book-card p {
            margin: 5px 0;
            font-size: 0.9rem;
            color: #555;
        }

        .book-card .btn {
            background-color: #FFD700;
            color: #0D203B;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: none;
            transition: background-color 0.3s;
        }

        .book-card .btn:hover {
            background-color: #E4C100;
        }

        /* Rating & Review Form */
        .rate-review-form {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .rate-review-form .form-group {
            margin-bottom: 15px;
        }

        .rate-review-form button {
            background-color: #FFD700;
            color: #0D203B;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .rate-review-form button:hover {
            background-color: #E4C100;
        }

        /* Footer Styling */
        footer {
            background-color: #0D203B;
            color: white;
            padding: 1.5rem 0;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Navbar Start -->
<nav class="navbar">
    <div class="nav-brand">
        <img src="assets/img/magic book.logo.png" alt="Logo" class="logo">
    </div>
    <ul class="nav-links">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="issued-books.php">Issued Books</a></li>
        <li><a href="logout.php" class="login-btn">Logout</a></li>
    </ul>
</nav>
<!-- Navbar End -->

<div class="content-wrapper">
    <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">Listed Books</h4>
            </div>
        </div>
        
        <div class="row">
            <?php if ($query->rowCount() > 0): ?>
                <?php foreach ($results as $result): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="book-card">
                            <img src="admin/bookimg/<?php echo htmlentities($result->image); ?>" alt="Book Image" />
                            <h5><?php echo htmlentities($result->title); ?></h5>
                            <p><strong>Category:</strong> <?php echo htmlentities($result->category); ?></p>
                            <p><strong>Author(s):</strong> <?php echo htmlentities($result->authors); ?></p>
                            <p><strong>ISBN:</strong> <?php echo htmlentities($result->ISBN); ?></p>
                            <a href="issued-books.php?ISBN=<?php echo $result->ISBN; ?>" class="btn">Issue Book</a>
                            
                            <!-- Rating and Review Form -->
                            <form class="rate-review-form" data-isbn="<?php echo $result->ISBN; ?>">
                                <div class="form-group">
                                    <label>Rate this book:</label>
                                    <select name="rating" class="form-control" required>
                                        <option value="">Select Rating</option>
                                        <option value="1">1 Star</option>
                                        <option value="2">2 Stars</option>
                                        <option value="3">3 Stars</option>
                                        <option value="4">4 Stars</option>
                                        <option value="5">5 Stars</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Review:</label>
                                    <textarea name="review" class="form-control" placeholder="Write your review here" required></textarea>
                                </div>
                                <button type="submit" class="btn">Submit Rating & Review</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No books available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Footer Start -->
<footer>
    <p>&copy; 2024 LibraryHub. All rights reserved.</p>
</footer>
<!-- Footer End -->

<script src="assets/js/jquery-1.10.2.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script>
    // Handle the rate/review form submission
    document.querySelectorAll('.rate-review-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            const isbn = form.getAttribute('data-isbn');
            formData.append('ISBN', isbn);

            fetch('rate_review_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.success ? 'Rating and review submitted successfully!' : 'Error: ' + data.message);
                form.reset();
            })
            .catch(error => {
                alert('An error occurred. Please try again later.');
            });
        });
    });
</script>
</body>
</html>