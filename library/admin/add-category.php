<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(isset($_POST['create'])) {
    $category = $_POST['category'];
    $status = $_POST['status'];
    $currentDate = date('Y-m-d H:i:s'); // Current timestamp

    // SQL query to insert category
    $sql = "INSERT INTO category (CategoryName, Status, CreationDate, UpdationDate) 
            VALUES (:category, :status, :creationDate, :updationDate)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':category', $category, PDO::PARAM_STR);
    $query->bindParam(':status', $status, PDO::PARAM_INT); // Changed to PDO::PARAM_INT
    $query->bindParam(':creationDate', $currentDate, PDO::PARAM_STR);
    $query->bindParam(':updationDate', $currentDate, PDO::PARAM_STR);
    $query->execute();

    $lastInsertId = $dbh->lastInsertId();
    if($lastInsertId) {
        $_SESSION['msg'] = "Category created successfully!";
        header('location:manage-categories.php'); // Redirect to manage categories page
    } else {
        $_SESSION['error'] = "Something went wrong. Please try again.";
        header('location:manage-categories.php');
    }
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Add Categories</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Add Category</h4>
                </div>
            </div>

            <!-- Display success or error messages -->
            <?php if(isset($_SESSION['msg'])) { ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['msg']; ?>
                    <?php unset($_SESSION['msg']); ?>
                </div>
            <?php } ?>

            <?php if(isset($_SESSION['error'])) { ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Category Info
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post">
                                <div class="form-group">
                                    <label>Category Name</label>
                                    <input class="form-control" type="text" name="category" autocomplete="off" required />
                                </div>

                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="status" value="1" checked="checked"> Active
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="status" value="0"> Inactive
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" name="create" class="btn btn-info">Create</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>

    <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
