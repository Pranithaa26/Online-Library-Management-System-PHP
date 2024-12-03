<?php
session_start();
error_reporting(0);
include('includes/config.php');
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Manage Issued Books</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- DATATABLE STYLE  -->
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <style>
    :root {
        --primary-color: #4f46e5; /* Blue */
        --primary-dark: #4338ca; /* Darker Blue */
        --secondary-color: #f5a623; /* Orange */
        --bg-light: #f4f7fc; /* Light Gray */
        --bg-dark: #1f2937; /* Dark Gray */
        --text-light: #ffffff; /* White */
        --text-dark: #333333; /* Dark Gray */
        --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    body {
        font-family: 'Open Sans', sans-serif;
        background-color: var(--bg-light);
        margin: 0;
    }

    /* Navbar */
    .navbar {
        background-color: #0D203B;
        color: var(--text-light);
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow);
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .navbar .nav-brand {
        flex: 1;
     }

    .navbar .logo {
        width: 50px;
        height: 50px;
        object-fit: contain;
    }

    .nav-links {
        list-style: none;
        display: flex;
        gap: 1.5rem;
        margin-left: auto;
    }

    .nav-links a {
        text-decoration: none;
        color: var(--text-light);
        font-weight: 500;
        transition: color 0.3s;
    }

    .nav-links a:hover {
        color: var(--primary-color);
    }

    .navbar .login-btn {
        background-color: var(--primary-color);
        color: var(--text-light);
        padding: 0.5rem 1.5rem;
        border-radius: 0.375rem;
        transition: background-color 0.3s;
    }

    .navbar .login-btn:hover {
        background-color: var(--primary-dark);
    }

    .content-wrapper {
        margin-top: 30px;
    }

    .header-line {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--text-dark);
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: 10px;
    }

    .alert {
        border-radius: 0.25rem;
    }

    /* Table Styles */
    .table {
        border-collapse: collapse;
        width: 100%;
        background-color: var(--bg-light);
        border-radius: 5px;
        box-shadow: var(--shadow);
    }

    .table th, .table td {
        padding: 15px;
        text-align: left;
        border: 1px solid #ddd;
    }

    .table th {
        background-color: var(--primary-color);
        color: var(--text-light);
        font-weight: bold;
    }

    .table tr:nth-child(even) {
        background-color: #f2f2f2; /* Light Gray for alternate rows */
    }

    .table tr:hover {
        background-color: #f5a623; /* Light Orange for hover effect */
    }

    .table td {
        color: var(--text-dark);
    }

    /* Button Styles */
    .btn-danger {
        background-color: var(--secondary-color);
        border: none;
        color: var(--text-light);
    }

    .btn-danger:hover {
        background-color: #e67e22; /* Lighter Orange */
    }

    .btn-success {
        background-color: #34c759; /* Green */
        border: none;
        color: var(--text-light);
    }

    .btn-success:hover {
        background-color: #28a745; /* Darker Green */
    }

    .panel {
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #fff;
        box-shadow: var(--shadow);
    }

    .panel-heading {
        background-color: var(--primary-color);
        color: var(--text-light);
        padding: 10px 15px;
        font-size: 18px;
    }

    .panel-body {
        padding: 15px;
    }
    </style>
</head>
<body>
    <!-- MENU SECTION START-->
    <!-- MENU SECTION END-->
    <nav class="navbar">
    <div class="nav-brand">
        <img src="assets/img/magic book.logo.png" alt="Logo" class="logo">
    </div>
    <ul class="nav-links">
        <li><a href="librarian_dashboard.php">Home</a></li>
        <li><a href="issue-book.php">issue book</a></li>
        <li><a href="logout.php" class="login-btn">Logout</a></li>
    </ul>
</nav>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Manage Issued Books</h4>
                </div>
                <div class="row">
                    <?php if ($_SESSION['error'] != "") { ?>
                        <div class="col-md-6">
                            <div class="alert alert-danger">
                                <strong>Error :</strong>
                                <?php echo htmlentities($_SESSION['error']); ?>
                                <?php $_SESSION['error'] = ""; ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($_SESSION['msg'] != "") { ?>
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <strong>Success :</strong>
                                <?php echo htmlentities($_SESSION['msg']); ?>
                                <?php $_SESSION['msg'] = ""; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($_SESSION['delmsg'] != "") { ?>
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <strong>Success :</strong>
                                <?php echo htmlentities($_SESSION['delmsg']); ?>
                                <?php $_SESSION['delmsg'] = ""; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Issued Books
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Student Name</th>
                                            <th>Book Title</th>
                                            <th>ISBN</th>
                                            <th>Issued Date</th>
                                            <th>Return Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT u.name as studentName, b.title as bookTitle, b.ISBN, ib.issueDate, ib.returnDate, ib.issueID 
                                                FROM issuedbooks ib 
                                                JOIN user u ON u.userID = ib.userID 
                                                JOIN book b ON b.ISBN = ib.ISBN 
                                                ORDER BY ib.issueID DESC";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;

                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) {
                                        ?>
                                                <tr class="odd gradeX">
                                                    <td class="center"><?php echo htmlentities($cnt); ?></td>
                                                    <td class="center"><?php echo htmlentities($result->studentName); ?></td>
                                                    <td class="center"><?php echo htmlentities($result->bookTitle); ?></td>
                                                    <td class="center"><?php echo htmlentities($result->ISBN); ?></td>
                                                    <td class="center"><?php echo htmlentities($result->issueDate); ?></td>
                                                    <td class="center"><?php echo ($result->returnDate == "") ? "Not Returned Yet" : htmlentities($result->returnDate); ?></td>
                                                    <td class="center">
                                                        <a href="update-issue-bookdeails.php?rid=<?php echo htmlentities($result->issueID); ?>">
                                                            <button class="btn btn-primary"><i class="fa fa-edit "></i> Edit</button>
                                                        </a>
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
                    <!--End Advanced Tables -->
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENT-WRAPPER SECTION END-->
    <?php include('includes/footer.php'); ?>
    <!-- FOOTER SECTION END-->
    
    <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME -->
    <!-- CORE JQUERY -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.js"></script>
    <!-- DATATABLE SCRIPTS -->
    <script src="assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
    <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
</body>
</html>
