<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(isset($_POST['update'])){
    // Get and sanitize input values
    $bookname = filter_var($_POST['bookname'], FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
    $author = filter_var($_POST['author'], FILTER_SANITIZE_STRING);
    $isbn = filter_var($_POST['isbn'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);

    // Check if a new image is uploaded
    if (isset($_FILES['bookimage']) && $_FILES['bookimage']['error'] == 0) {
        // Validate and move uploaded image
        $imagePath = 'bookimg/' . basename($_FILES['bookimage']['name']);
        if(move_uploaded_file($_FILES['bookimage']['tmp_name'], $imagePath)) {
            $imageUpload = $imagePath;
        } else {
            echo "<script>alert('Error uploading image');</script>";
            exit;
        }
    } else {
        // Keep existing image if no new image is uploaded
        $imageUpload = $_POST['existing_image'];
    }

    $bookid = filter_var($_GET['bookid'], FILTER_SANITIZE_NUMBER_INT);

    // Update query
    $sql = "UPDATE book SET title=:bookname, category=:category, authors=:author, ISBN=:isbn, image=:bookImage, description=:description WHERE ISBN=:isbn";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bookname', $bookname, PDO::PARAM_STR);
    $query->bindParam(':category', $category, PDO::PARAM_STR);
    $query->bindParam(':author', $author, PDO::PARAM_STR);
    $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
    $query->bindParam(':bookImage', $imageUpload, PDO::PARAM_STR);
    $query->bindParam(':description', $description, PDO::PARAM_STR);
    $query->execute();

    echo "<script>alert('Book info updated successfully');</script>";
    echo "<script>window.location.href='manage-books.php'</script>";
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Edit Book</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>

<div class="content-wrapper">
    <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">Edit Book</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">Book Info</div>
                    <div class="panel-body">
                        <form role="form" method="post" enctype="multipart/form-data">
<?php 
    $bookid = filter_var($_GET['bookid'], FILTER_SANITIZE_NUMBER_INT);
    $sql = "SELECT * FROM book WHERE ISBN = :isbn";
    $query = $dbh->prepare($sql);
    $query->bindParam(':isbn', $bookid, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Book Image</label>
                                    <img src="bookimg/<?php echo htmlentities($result->image);?>" width="100">
                                    <a href="change-bookimg.php?isbn=<?php echo htmlentities($result->ISBN);?>">Change Book Image</a>
                                    <input type="hidden" name="existing_image" value="<?php echo htmlentities($result->image);?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Book Name<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="bookname" value="<?php echo htmlentities($result->title);?>" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="category" value="<?php echo htmlentities($result->category);?>" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Author<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="author" value="<?php echo htmlentities($result->authors);?>" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>ISBN Number<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="isbn" value="<?php echo htmlentities($result->ISBN);?>" readonly />
                                    <p class="help-block">An ISBN is an International Standard Book Number. ISBN must be unique.</p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Book Description<span style="color:red;">*</span></label>
                                    <textarea class="form-control" name="description" required="required"><?php echo htmlentities($result->description);?></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Change Book Image</label>
                                    <input type="file" name="bookimage" class="form-control">
                                </div>
                            </div>
<?php }} ?>
                            <div class="col-md-12">
                                <button type="submit" name="update" class="btn btn-info">Update</button>
                            </div>
                        </form>
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
