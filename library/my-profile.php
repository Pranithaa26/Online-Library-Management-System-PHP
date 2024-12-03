<?php
session_start();
include('includes/config.php');
error_reporting(E_ALL);  // Enable all error reporting for debugging

if(strlen($_SESSION['login']) == 0) {   
    header('location:index.php');
} else {
    $userid = $_SESSION['user_id']; // userID from session
    if(empty($userid)) {
        echo "User ID not found in session.";
        exit();
    }

    // Calculate fines for overdue books
    $sql = "SELECT i.issueID, i.issueDate FROM issuedbooks i WHERE i.userID = :userid AND i.returnDate IS NULL"; 
    $query = $dbh->prepare($sql);
    $query->bindParam(':userid', $userid, PDO::PARAM_STR); // Using userID
    $query->execute();

    $unpaidFines = 0; // Initialize total fine

    // Check each issued book for overdue fines
    if ($query->rowCount() > 0) {
        $overdueBooks = $query->fetchAll(PDO::FETCH_OBJ);
        foreach ($overdueBooks as $book) {
            // Calculate the due date (15 days after the issue date)
            $issueDate = new DateTime($book->issueDate);
            $dueDate = $issueDate->add(new DateInterval('P15D'));  // 15 days after issue date
            $currentDate = new DateTime();
            
            // Calculate the overdue days
            if ($currentDate > $dueDate) {
                $interval = $dueDate->diff($currentDate);
                $overdueDays = $interval->days;
                
                // Apply fine (₹2 per overdue day)
                if ($overdueDays > 0) {
                    $unpaidFines += $overdueDays * 2; // ₹2 per day
                }
            }
        }

        // Insert or update unpaid fines in the user table
        $updateFinesSql = "UPDATE user SET unpaidFines = :unpaidFines WHERE userID = :userid"; 
        $updateFinesQuery = $dbh->prepare($updateFinesSql);
        $updateFinesQuery->bindParam(':unpaidFines', $unpaidFines, PDO::PARAM_INT);
        $updateFinesQuery->bindParam(':userid', $userid, PDO::PARAM_STR); 
        $updateFinesQuery->execute();
    }

    if(isset($_POST['update'])) {
        $name = $_POST['name']; // Name field from form

        // Update user details (name)
        $sql = "UPDATE user SET name=:name WHERE userID=:userid"; 
        $query = $dbh->prepare($sql);
        $query->bindParam(':userid', $userid, PDO::PARAM_STR); 
        $query->bindParam(':name', $name, PDO::PARAM_STR); 
        $query->execute();

        echo '<script>alert("Your profile has been updated")</script>';
    }
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | My Profile</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
</head>
<body>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">My Profile</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-9 col-md-offset-1">
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                           My Profile
                        </div>
                        <div class="panel-body">
                            <form name="signup" method="post">
                                <?php 
                                    $sql = "SELECT userID, name, email, unpaidFines FROM user WHERE userID=:userid"; // userID from session
                                    $query = $dbh->prepare($sql);
                                    $query->bindParam(':userid', $userid, PDO::PARAM_STR); 
                                    $query->execute();

                                    echo "Rows returned: " . $query->rowCount();  // Debugging line

                                    $results = $query->fetchAll(PDO::FETCH_OBJ);

                                    if($query->rowCount() > 0) {
                                        foreach($results as $result) {
                                ?>

                                <div class="form-group">
                                    <label>User ID: </label>
                                    <?php echo htmlentities($result->userID); ?>
                                </div>

                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input class="form-control" type="text" name="name" value="<?php echo htmlentities($result->name); ?>" required /> <!-- Updated to use 'name' -->
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input class="form-control" type="email" name="email" value="<?php echo htmlentities($result->email); ?>" required readonly />
                                </div>

                                <?php }} else { echo "No results found."; } ?>

                                <button type="submit" name="update" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>

                    <!-- Display Unpaid Fines -->
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            Unpaid Fines
                        </div>
                        <div class="panel-body">
                            <?php
                                // Display the updated unpaid fines
                                $sql = "SELECT unpaidFines FROM user WHERE userID = :userid"; 
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':userid', $userid, PDO::PARAM_STR); 
                                $query->execute();
                                $result = $query->fetch(PDO::FETCH_OBJ);

                                if($result) {
                                    echo '<p>Total Unpaid Fine: ₹' . htmlentities($result->unpaidFines) . '</p>';
                                } else {
                                    echo '<p>No unpaid fines.</p>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>

<?php } ?>
