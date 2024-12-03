<?php
session_start();
error_reporting(0);
include('includes/db_connection.php'); // Include database connection

if (!$dbh) {
    die("Database connection failed.");
}

if (isset($_POST['add'])) {
    // Sanitize inputs
    $bookname = htmlspecialchars($_POST['bookname']);
    $category = (int)$_POST['category']; // Ensure category is an integer
    $author = (int)$_POST['author']; // Ensure author is an integer
    $isbn = htmlspecialchars($_POST['isbn']);
    $bookimg = $_FILES["bookpic"]["name"];
    $description = htmlspecialchars($_POST['description']);

    // File extension and upload validation
    $extension = substr($bookimg, strlen($bookimg) - 4, strlen($bookimg));
    $allowed_extensions = array(".jpg", ".jpeg", ".png", ".gif");
    $imgnewname = md5($bookimg . time()) . $extension;

    // Check file extension
    if (!in_array($extension, $allowed_extensions)) {
        echo "<script>alert('Invalid format. Only jpg / jpeg / png / gif format allowed');</script>";
    } else {
        // Move uploaded image to the directory
        if (!is_dir("bookimg")) {
            mkdir("bookimg", 0755, true); // Create the directory if it doesn't exist
        }
        move_uploaded_file($_FILES["bookpic"]["tmp_name"], "bookimg/" . $imgnewname);

        try {
            // Prepare SQL query for insertion into the `book` table
            $sql = "INSERT INTO book (title, CatId, AuthorId, ISBN, image, description) 
                    VALUES (:bookname, :category, :author, :isbn, :imgnewname, :description)";
            $query = $dbh->prepare($sql);

            // Bind parameters to the SQL query
            $query->bindParam(':bookname', $bookname, PDO::PARAM_STR);
            $query->bindParam(':category', $category, PDO::PARAM_INT);
            $query->bindParam(':author', $author, PDO::PARAM_INT);
            $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
            $query->bindParam(':imgnewname', $imgnewname, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);

            // Execute the query
            $query->execute();

            // Check if insertion was successful
            $lastInsertId = $dbh->lastInsertId();
            if ($lastInsertId) {
                echo "<script>alert('Book Listed successfully');</script>";
                echo "<script>window.location.href='manage-books.php'</script>";
            } else {
                echo "<script>alert('Something went wrong. Please try again');</script>";
            }
        } catch (PDOException $e) {
            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Online Library Management System | Add Book</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
</head>
<body>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Add Book</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Book Info
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Book Name<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="bookname" autocomplete="off" required />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Category<span style="color:red;">*</span></label>
                                        <select class="form-control" name="category" required="required">
                                            <option value="">Select Category</option>
                                            <?php
                                            $status = 1;
                                            $sql = "SELECT * FROM category WHERE Status=:status";
                                            $query = $dbh->prepare($sql);
                                            $query->bindParam(':status', $status, PDO::PARAM_INT);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);

                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) {
                                                    echo '<option value="' . htmlentities($result->id) . '">' . htmlentities($result->categoryName) . '</option>';
                                                }
                                            } else {
                                                echo '<option value="">No categories available</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Author<span style="color:red;">*</span></label>
                                        <select class="form-control" name="author" required="required">
                                            <option value="">Select Author</option>
                                            <?php
                                            $sql = "SELECT * FROM authors";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);

                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) {
                                                    echo '<option value="' . htmlentities($result->id) . '">' . htmlentities($result->AuthorName) . '</option>';
                                                }
                                            } else {
                                                echo '<option value="">No authors available</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ISBN Number<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="isbn" id="isbn" required="required" autocomplete="off" />
                                        <p class="help-block">ISBN must be unique</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Book Picture<span style="color:red;">*</span></label>
                                        <input class="form-control" type="file" name="bookpic" autocomplete="off" required="required" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" name="description" rows="4"></textarea>
                                    </div>
                                </div>

                                <button type="submit" name="add" id="add" class="btn btn-info">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER SECTION END-->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
