<?php
session_start();
include('db_connection.php');

// Check if user is logged in (session variable should be set)
if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in.");
    echo json_encode(["success" => false, "message" => "User is not logged in."]);
    exit;
}

$userID = $_SESSION['user_id'];
$isbn = isset($_POST['ISBN']) ? $_POST['ISBN'] : null;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
$reviewText = isset($_POST['review']) ? trim($_POST['review']) : null;

// Log incoming POST data
error_log("POST Data: ISBN = " . (isset($_POST['ISBN']) ? $_POST['ISBN'] : 'not set') . ", Rating = " . (isset($_POST['rating']) ? $_POST['rating'] : 'not set') . ", Review = " . (isset($_POST['review']) ? $_POST['review'] : 'not set'));

// Check if necessary POST parameters are set
if (empty($isbn)) {
    error_log("ISBN is missing.");
    echo json_encode(["success" => false, "message" => "ISBN is missing."]);
    exit;
}

if ($rating === null && empty($reviewText)) {
    error_log("Rating or review is missing.");
    echo json_encode(["success" => false, "message" => "Rating or review must be provided."]);
    exit;
}

// Ensure database connection is valid
if (!$dbh) {
    error_log("Database connection failed: " . print_r($dbh->errorInfo(), true));
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

try {
    // Validate rating
    if ($rating && ($rating < 1 || $rating > 5)) {
        throw new Exception('Rating must be between 1 and 5.');
    }

    // Sanitize the review text
    if ($reviewText) {
        $reviewText = htmlspecialchars($reviewText, ENT_QUOTES, 'UTF-8'); // Sanitize for HTML
    }

    // Start transaction
    $dbh->beginTransaction();
    error_log("Transaction started.");

    // Handle Rating
    if ($rating !== null) {
        // Check if the user has already rated this book
        $sqlCheckRating = "SELECT * FROM rating WHERE ISBN = :isbn AND userID = :userID";
        $stmtCheckRating = $dbh->prepare($sqlCheckRating);
        $stmtCheckRating->bindParam(':isbn', $isbn);
        $stmtCheckRating->bindParam(':userID', $userID);
        
        // Log the query and execute it
        error_log("Executing query for checking rating: " . $sqlCheckRating);
        $stmtCheckRating->execute();

        // Log result count
        error_log("Rating row count: " . $stmtCheckRating->rowCount());

        if ($stmtCheckRating->rowCount() > 0) {
            // Update existing rating
            $sqlRating = "UPDATE rating SET rating = :rating WHERE ISBN = :isbn AND userID = :userID";
        } else {
            // Insert new rating
            $sqlRating = "INSERT INTO rating (ISBN, userID, rating) VALUES (:isbn, :userID, :rating)";
        }

        error_log("Executing query for rating: " . $sqlRating);
        $stmtRating = $dbh->prepare($sqlRating);
        $stmtRating->bindParam(':isbn', $isbn);
        $stmtRating->bindParam(':userID', $userID);
        $stmtRating->bindParam(':rating', $rating);
        $stmtRating->execute();
    }

    // Handle Review
    if ($reviewText) {
        // Check if the user has already reviewed this book
        $sqlCheckReview = "SELECT * FROM review WHERE ISBN = :isbn AND userID = :userID";
        $stmtCheckReview = $dbh->prepare($sqlCheckReview);
        $stmtCheckReview->bindParam(':isbn', $isbn);
        $stmtCheckReview->bindParam(':userID', $userID);
        
        // Log the query and execute it
        error_log("Executing query for checking review: " . $sqlCheckReview);
        $stmtCheckReview->execute();

        // Log result count
        error_log("Review row count: " . $stmtCheckReview->rowCount());

        if ($stmtCheckReview->rowCount() > 0) {
            // Update existing review
            $sqlReview = "UPDATE review SET reviewText = :reviewText WHERE ISBN = :isbn AND userID = :userID";
        } else {
            // Insert new review
            $sqlReview = "INSERT INTO review (ISBN, userID, reviewText) VALUES (:isbn, :userID, :reviewText)";
        }

        error_log("Executing query for review: " . $sqlReview);
        $stmtReview = $dbh->prepare($sqlReview);
        $stmtReview->bindParam(':isbn', $isbn);
        $stmtReview->bindParam(':userID', $userID);
        $stmtReview->bindParam(':reviewText', $reviewText);
        $stmtReview->execute();
    }

    // Commit transaction
    if ($dbh->commit()) {
        error_log("Transaction committed successfully.");
    } else {
        error_log("Error committing transaction: " . implode(":", $dbh->errorInfo()));
    }

    // Return JSON success response
    echo json_encode(["success" => true, "message" => "Rating and review submitted successfully!"]);

} catch (PDOException $e) {
    // Rollback transaction in case of error
    $dbh->rollBack();
    error_log("PDO Exception: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    // Log the general exception
    error_log("General Exception: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
