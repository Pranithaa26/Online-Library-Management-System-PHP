<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Code for blocking student
if (isset($_GET['inid'])) {
    $id = $_GET['inid'];
    $status = 0; // Block the user
    $sql = "UPDATE user SET Status=:status WHERE userID=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->bindParam(':status', $status, PDO::PARAM_INT);
    $query->execute();
    header('location:reg-students.php');
}

// Code for activating student
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = 1; // Activate the user
    $sql = "UPDATE user SET Status=:status WHERE userID=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->bindParam(':status', $status, PDO::PARAM_INT);
    $query->execute();
    header('location:reg-students.php');
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Manage Registered Students</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Manage Registered Students</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Registered Students
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>S.No</th>
                                            <th>User Name</th>
                                            <th>Email id</th>
                                            <th>Status</th>
                                            <th>Unpaid Fines</th>
                                            <th>Issued Books</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $sql = "
                                    SELECT u.userID, u.name, u.email, u.Status, u.unpaidFines, 
                                    GROUP_CONCAT(b.title SEPARATOR ', ') AS issuedBooks 
                                    FROM user u
                                    LEFT JOIN issuedBooks ib ON u.userID = ib.userID
                                    LEFT JOIN book b ON ib.ISBN = b.ISBN
                                    GROUP BY u.userID
                                    ";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    $cnt = 1;

                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $result) { 
                                    ?>                                       
                                        <tr class="odd gradeX">
                                            <td class="center"><?php echo htmlentities($cnt);?></td>
                                            <td class="center"><?php echo htmlentities($result->userID);?></td>
                                            <td class="center"><?php echo htmlentities($result->name);?></td>
                                            <td class="center"><?php echo htmlentities($result->email);?></td>
                                            <td class="center">
                                                <?php 
                                                if ($result->Status == 1) {
                                                    echo htmlentities("Active");
                                                } else {
                                                    echo htmlentities("Blocked");
                                                }
                                                ?>
                                            </td>
                                            <td class="center"><?php echo htmlentities($result->unpaidFines); ?></td>
                                            <td class="center"><?php echo htmlentities($result->issuedBooks); ?></td>
                                            <td class="center">
                                                <?php 
                                                if ($result->Status == 1) { 
                                                ?>
                                                    <a href="reg-students.php?inid=<?php echo htmlentities($result->userID);?>" onclick="return confirm('Are you sure you want to block this student?');">  
                                                        <button class="btn btn-danger"> Block</button>
                                                    </a>
                                                <?php 
                                                } else {
                                                ?>
                                                    <a href="reg-students.php?id=<?php echo htmlentities($result->userID);?>" onclick="return confirm('Are you sure you want to activate this student?');">
                                                        <button class="btn btn-primary"> Activate</button> 
                                                    </a>
                                                <?php } ?>
                                                  </td>
                                        </tr>
                                    <?php 
                                        $cnt++;
                                    }
                                    }
                                    ?>                                      
                                    </tbody>
                                </table>
                            </div>                    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php');?>

    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
