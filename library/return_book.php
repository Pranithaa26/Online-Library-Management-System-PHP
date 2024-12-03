<?php
session_start();
include('db_connection.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['return_book'])) {
    $issueID = $_POST['issueID'];

    // Fetch the issued book details
    $sql = "SELECT issueID, ISBN, fineAmount, finePaid FROM issuedBooks WHERE issueID = :issueID AND userID = :userID";
    $query = $dbh->prepare($sql);
    $query->bindParam(':issueID', $issueID, PDO::PARAM_INT);
    $query->bindParam(':userID', $user_id, PDO::PARAM_INT);
    $query->execute();
    $book = $query->fetch(PDO::FETCH_OBJ);

    if ($book) {
        // Get current fine and calculate the total fine
        $fineAmount = $book->fineAmount;
        $finePaid = $book->finePaid;
        $remainingFine = $fineAmount - $finePaid;

        // Update the book return status and fine
        $updateSQL = "UPDATE issuedBooks SET returnDate = NOW(), fineAmount = 0 WHERE issueID = :issueID";
        $updateQuery = $dbh->prepare($updateSQL);
        $updateQuery->bindParam(':issueID', $issueID);
        $updateQuery->execute();

        // Optionally: Insert payment record into payments table (if you want to track payments)
        if ($remainingFine > 0) {
            $paymentSQL = "INSERT INTO payments (userID, issueID, amount, paymentDate, paymentStatus) 
                           VALUES (:userID, :issueID, :amount, NOW(), 'Completed')";
            $paymentQuery = $dbh->prepare($paymentSQL);
            $paymentQuery->bindParam(':userID', $user_id);
            $paymentQuery->bindParam(':issueID', $issueID);
            $paymentQuery->bindParam(':amount', $remainingFine);
            $paymentQuery->execute();
        }

        echo "<script>alert('Book returned successfully! Fine paid: Rs $remainingFine'); window.location.href='issued-books.php';</script>";
    } else {
        echo "<script>alert('Error: Book not found!'); window.location.href='issued-books.php';</script>";
    }
}
?>
