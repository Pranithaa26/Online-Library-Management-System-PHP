<?php
session_start();
include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to return a book.";
    exit();
}

$userID = $_SESSION['user_id'];

// Check if issueID is provided for returning the book
if (isset($_GET['issueID'])) {
    $issueID = $_GET['issueID'];

    try {
        // Fetch the copyID of the issued book to update the book status
        $sql = "SELECT copyID, ISBN FROM issuedBooks WHERE issueID = :issueID AND userID = :userID";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':issueID', $issueID);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $issuedBook = $stmt->fetch(PDO::FETCH_OBJ);

        if ($issuedBook) {
            // Update the returnDate in the issuedBooks table
            $sqlReturn = "UPDATE issuedBooks SET returnDate = NOW() WHERE issueID = :issueID";
            $stmtReturn = $dbh->prepare($sqlReturn);
            $stmtReturn->bindParam(':issueID', $issueID);
            $stmtReturn->execute();

            // Update the book status to 'available' in the bookCopies table
            $sqlUpdateCopy = "UPDATE bookCopies SET bookStatus = 'available' WHERE copyID = :copyID";
            $stmtUpdateCopy = $dbh->prepare($sqlUpdateCopy);
            $stmtUpdateCopy->bindParam(':copyID', $issuedBook->copyID);
            $stmtUpdateCopy->execute();

            // Update the book's available copies count
            $sqlUpdateBook = "UPDATE book SET noOfCopiesOnShelf = noOfCopiesOnShelf + 1 WHERE ISBN = :isbn";
            $stmtUpdateBook = $dbh->prepare($sqlUpdateBook);
            $stmtUpdateBook->bindParam(':isbn', $issuedBook->ISBN);
            $stmtUpdateBook->execute();

            echo "Book returned successfully!";
        } else {
            echo "Error: Book not found or you don't have this book issued.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
