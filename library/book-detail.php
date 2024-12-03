<?php
// Include the database connection file
include('db_connection.php');

// Get the ISBN of the book
$isbn = $_GET['ISBN'];

// Fetch the detailed information of the selected book
$sql = "SELECT book.ISBN, book.title, book.yearOfPublication, book.totalCopies, book.noOfCopiesOnShelf, book.authors, book.category, book.image, book.description 
        FROM book WHERE book.ISBN = :isbn";
$query = $dbh->prepare($sql);
$query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
$query->execute();
$book = $query->fetch(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details | <?php echo htmlentities($book->title); ?></title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Navbar Start -->
<nav class="navbar">
    <div class="nav-brand">
        <img src="assets/img/magic book.logo.png" alt="Logo" class="logo">
    </div>
    <ul class="nav-links">
        <li><a href="books.php">Back to Books</a></li>
        <li><a href="add-to-shelf.php?ISBN=<?php echo $book->ISBN; ?>" class="btn">Add to Shelf</a></li>
    </ul>
</nav>
<!-- Navbar End -->

<div class="content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <img src="admin/bookimg/<?php echo htmlentities($book->image); ?>" alt="Book Image" class="img-fluid">
            </div>
            <div class="col-md-6">
                <h1><?php echo htmlentities($book->title); ?></h1>
                <p><strong>Author(s):</strong> <?php echo htmlentities($book->authors); ?></p>
                <p><strong>Category:</strong> <?php echo htmlentities($book->category); ?></p>
                <p><strong>Year of Publication:</strong> <?php echo htmlentities($book->yearOfPublication); ?></p>
                <p><strong>Synopsis:</strong> <?php echo htmlentities($book->description); ?></p>
                <p><strong>Availability:</strong> <?php echo htmlentities($book->noOfCopiesOnShelf) . ' copies available'; ?></p>

                <!-- Rating & Review Form -->
                <form class="rate-review-form">
                    <div class="form-group">
                        <label>Rate this book:</label>
                        <select name="rating" class="form-control">
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
                        <textarea name="review" class="form-control" placeholder="Write your review here"></textarea>
                    </div>
                    <button type="submit" class="btn">Submit Rating & Review</button>
                </form>

                <!-- Reviews and Ratings Section -->
                <h3>Reviews & Ratings</h3>
                <!-- Display reviews and ratings here -->
            </div>
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
</body>
</html>
