<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Handling the author addition
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $authorName = $_POST['authorName'];

    // Insert the new author into the database
    $sql = "INSERT INTO authors (AuthorName) VALUES (:authorName)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':authorName', $authorName, PDO::PARAM_STR);

    if ($query->execute()) {
        $_SESSION['msg'] = "Author added successfully!";
        header('location: manage-authors.php');
        exit();
    } else {
        $_SESSION['error'] = "Error adding author.";
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
    <title>Online Library Management System | Add Author</title>

    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body>
    <!-- MENU SECTION START-->
    <?php include('includes/header.php'); ?>
    <!-- MENU SECTION END-->

    <div class="container">
        <h4 class="header-line">Add Author</h4>

        <!-- Error and Success Messages -->
        <?php if ($_SESSION['error'] != "") { ?>
        <div class="alert alert-danger">
            <strong>Error :</strong> <?php echo htmlentities($_SESSION['error']); ?>
        </div>
        <?php $_SESSION['error'] = ""; } ?>

        <?php if ($_SESSION['msg'] != "") { ?>
        <div class="alert alert-success">
            <strong>Success :</strong> <?php echo htmlentities($_SESSION['msg']); ?>
        </div>
        <?php $_SESSION['msg'] = ""; } ?>

        <!-- Author Add Form -->
        <form method="post" name="addauthor" class="form-horizontal">
            <div class="form-group">
                <label for="authorName" class="control-label col-md-2">Author Name</label>
                <div class="col-md-4">
                    <input type="text" name="authorName" class="form-control" required />
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4 col-md-offset-2">
                    <button type="submit" class="btn btn-success">Add Author</button>
                </div>
            </div>
        </form>
    </div>

    <!-- FOOTER SECTION END-->
    <?php include('includes/footer.php'); ?>

    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
</body>
</html>
