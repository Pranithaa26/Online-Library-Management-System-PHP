<?php
session_start();
include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch book details for the issue action
if (isset($_GET['ISBN'])) {
    $isbn = $_GET['ISBN'];

    // Start a transaction
    try {
        $dbh->beginTransaction(); // Start the transaction

        // Check if there are available copies
        $checkSQL = "SELECT noOfCopiesOnShelf FROM book WHERE ISBN = :isbn";
        $checkQuery = $dbh->prepare($checkSQL);
        $checkQuery->bindParam(':isbn', $isbn);
        $checkQuery->execute();

        if ($checkQuery) {
            $book = $checkQuery->fetch(PDO::FETCH_OBJ);

            if ($book && $book->noOfCopiesOnShelf > 0) {
                // Fetch a valid available copy from the bookCopies table (bookStatus = 0 for available)
                $copySQL = "SELECT copyID FROM bookCopies WHERE ISBN = :isbn AND bookStatus = 0 LIMIT 1"; // 0 for available
                $copyQuery = $dbh->prepare($copySQL);
                $copyQuery->bindParam(':isbn', $isbn);
                $copyQuery->execute();

                if ($copyQuery) {
                    $copy = $copyQuery->fetch(PDO::FETCH_OBJ);

                    if ($copy) {
                        // Issue the book with the selected copyID
                        $issueDate = date("Y-m-d H:i:s");
                        $dueDate = date('Y-m-d', strtotime('+14 days'));

                        // Insert into issuedBooks table
                        $issueSQL = "INSERT INTO issuedBooks (userID, ISBN, issueDate, dueDate, copyID) 
                                     VALUES (:user_id, :isbn, :issue_date, :due_date, :copy_id)";
                        $issueQuery = $dbh->prepare($issueSQL);
                        $issueQuery->bindParam(':user_id', $user_id);
                        $issueQuery->bindParam(':isbn', $isbn);
                        $issueQuery->bindParam(':issue_date', $issueDate);
                        $issueQuery->bindParam(':due_date', $dueDate);
                        $issueQuery->bindParam(':copy_id', $copy->copyID);
                        $issueQuery->execute();

                        // Update the status of the issued copy to 'issued' (1)
                        $updateCopySQL = "UPDATE bookCopies SET bookStatus = 1 WHERE copyID = :copy_id"; // 1 for issued
                        $updateCopyQuery = $dbh->prepare($updateCopySQL);
                        $updateCopyQuery->bindParam(':copy_id', $copy->copyID);
                        $updateCopyQuery->execute();

                        // Update noOfCopiesOnShelf in the book table
                        $updateSQL = "UPDATE book SET noOfCopiesOnShelf = noOfCopiesOnShelf - 1 WHERE ISBN = :isbn";
                        $updateQuery = $dbh->prepare($updateSQL);
                        $updateQuery->bindParam(':isbn', $isbn);
                        $updateQuery->execute();

                        // Commit the transaction if everything is successful
                        $dbh->commit();

                        echo "<script>alert('Book issued successfully!'); window.location.href='issued-books.php';</script>";
                    } else {
                        echo "<script>alert('No available copy found for this book.'); window.location.href='listed-books.php';</script>";
                    }
                } else {
                    echo "<script>alert('Error executing query for fetching available copy.'); window.location.href='listed-books.php';</script>";
                }
            } else {
                echo "<script>alert('Sorry, no copies available for this book.'); window.location.href='listed-books.php';</script>";
            }
        } else {
            echo "<script>alert('Error executing query for checking available copies.'); window.location.href='listed-books.php';</script>";
        }
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $dbh->rollBack();
        echo "<script>alert('An error occurred: " . $e->getMessage() . "'); window.location.href='listed-books.php';</script>";
    }
}

// Fetch issued books for the user
$sql = "SELECT b.title, b.ISBN, ib.issueDate, ib.dueDate, ib.returnDate, ib.issueID
        FROM issuedBooks ib
        JOIN book b ON ib.ISBN = b.ISBN
        WHERE ib.userID = :user_id";
$query = $dbh->prepare($sql);
$query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$query->execute();
$issuedBooks = $query->fetchAll(PDO::FETCH_OBJ);

// Handle book return
if (isset($_POST['return_book'])) {
    $issueID = $_POST['issueID'];
    $returnDate = date("Y-m-d H:i:s");

    // Start transaction for book return
    try {
        $dbh->beginTransaction(); // Start the transaction

        // Update return date in issuedBooks table
        $updateReturnSQL = "UPDATE issuedBooks SET returnDate = :return_date WHERE issueID = :issue_id";
        $updateReturnQuery = $dbh->prepare($updateReturnSQL);
        $updateReturnQuery->bindParam(':return_date', $returnDate);
        $updateReturnQuery->bindParam(':issue_id', $issueID);
        $updateReturnQuery->execute();

        // Fetch copyID and ISBN from issuedBooks table
        $copySQL = "SELECT copyID, ISBN FROM issuedBooks WHERE issueID = :issue_id";
        $copyQuery = $dbh->prepare($copySQL);
        $copyQuery->bindParam(':issue_id', $issueID);
        $copyQuery->execute();
        $copy = $copyQuery->fetch(PDO::FETCH_OBJ);

        // Update copy status to available (bookStatus = 0)
        $updateCopyStatusSQL = "UPDATE bookCopies SET bookStatus = 0 WHERE copyID = :copy_id"; // 0 for available
        $updateCopyStatusQuery = $dbh->prepare($updateCopyStatusSQL);
        $updateCopyStatusQuery->bindParam(':copy_id', $copy->copyID);
        $updateCopyStatusQuery->execute();

        // Update noOfCopiesOnShelf in the book table
        $updateBookSQL = "UPDATE book SET noOfCopiesOnShelf = noOfCopiesOnShelf + 1 WHERE ISBN = :isbn";
        $updateBookQuery = $dbh->prepare($updateBookSQL);
        $updateBookQuery->bindParam(':isbn', $copy->ISBN);
        $updateBookQuery->execute();

        // Commit the transaction if everything is successful
        $dbh->commit();

        echo "<script>alert('Book returned successfully!'); window.location.href='issued-books.php';</script>";
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $dbh->rollBack();
        echo "<script>alert('An error occurred while returning the book: " . $e->getMessage() . "'); window.location.href='issued-books.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | Issued Books</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <style>
        /* Custom CSS for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 30px;
        }
        .header-line {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: bold;
        }
        .table {
            width: 100%;
            margin-bottom: 20px;
        }
        .table th, .table td {
            text-align: center;
            padding: 10px;
        }
        .btn {
            background-color: #28a745;
            color: white;
            padding: 5px 15px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #218838;
        }
        .text-success {
            color: #28a745;
        }
        .no-books-message {
            text-align: center;
            font-size: 18px;
            color: #888;
        }
    </style>
</head>
<body>

<div class="container">
    <h4 class="header-line">Issued Books</h4>

    <div class="row">
        <?php if ($query->rowCount() > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>ISBN</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($issuedBooks as $book): ?>
                        <tr>
                            <td><?php echo htmlentities($book->title); ?></td>
                            <td><?php echo htmlentities($book->ISBN); ?></td>
                            <td><?php echo htmlentities($book->issueDate); ?></td>
                            <td><?php echo htmlentities($book->dueDate); ?></td>
                            <td><?php echo htmlentities($book->returnDate ? $book->returnDate : "Not Returned"); ?></td>
                            <td>
                                <?php if (!$book->returnDate): ?>
                                    <form method="POST">
                                        <input type="hidden" name="issueID" value="<?php echo $book->issueID; ?>" />
                                        <button type="submit" name="return_book" class="btn">Return Book</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-success">Returned</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-books-message">No issued books at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
