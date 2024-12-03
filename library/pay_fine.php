<?php
session_start();
include('db_connection.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['pay_fine'])) {
    $issueID = $_POST['issueID'];

    // Fetch fine amount for the selected issueID
    $sql = "SELECT fineAmount, finePaid FROM issuedBooks WHERE issueID = :issueID AND userID = :userID";
    $query = $dbh->prepare($sql);
    $query->bindParam(':issueID', $issueID, PDO::PARAM_INT);
    $query->bindParam(':userID', $user_id, PDO::PARAM_INT);
    $query->execute();
    $book = $query->fetch(PDO::FETCH_OBJ);

    if ($book) {
        $fineAmount = $book->fineAmount;
        $finePaid = $book->finePaid;

        // Calculate remaining fine to be paid
        $remainingFine = $fineAmount - $finePaid;

        if ($remainingFine > 0) {
            // Update finePaid with the remaining fine
            $updateSQL = "UPDATE issuedBooks SET finePaid = finePaid + :remainingFine WHERE issueID = :issueID";
            $updateQuery = $dbh->prepare($updateSQL);
            $updateQuery->bindParam(':remainingFine', $remainingFine);
            $updateQuery->bindParam(':issueID', $issueID);
            $updateQuery->execute();

            // Optionally: Insert payment record into payments table (if you want to track payments)
            $paymentSQL = "INSERT INTO payments (userID, issueID, amount, paymentDate, paymentStatus) VALUES (:userID, :issueID, :amount, NOW(), 'Completed')";
            $paymentQuery = $dbh->prepare($paymentSQL);
            $paymentQuery->bindParam(':userID', $user_id);
            $paymentQuery->bindParam(':issueID', $issueID);
            $paymentQuery->bindParam(':amount', $remainingFine);
            $paymentQuery->execute();

            echo "<script>alert('Fine of Rs $remainingFine paid successfully!'); window.location.href='issued-books.php';</script>";
        } else {
            echo "<script>alert('No remaining fine to pay!'); window.location.href='issued-books.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid book or fine not found!'); window.location.href='issued-books.php';</script>";
    }
}
?>
