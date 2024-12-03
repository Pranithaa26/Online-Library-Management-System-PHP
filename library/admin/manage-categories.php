<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1); // Enable error reporting for debugging

// Include the database connection file
include('../db_connection.php');  // Make sure db.php contains the correct PDO connection code

// Check if the 'del' parameter is set
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    
    // Ensure the ID is numeric before proceeding with deletion
    if (is_numeric($id)) {
        try {
            $sql = "DELETE FROM category WHERE id=:id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            
            // Set success message and redirect
            $_SESSION['delmsg'] = "Category deleted successfully.";
            header('location:manage-categories.php');
            exit;
        } catch (Exception $e) {
            // Catch and display any errors
            $_SESSION['error'] = "Error: " . $e->getMessage();
            header('location:manage-categories.php');
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid category ID.";
        header('location:manage-categories.php');
        exit;
    }
}

// Debugging: Check the database connection
try {
    $sql = "SELECT * FROM category";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    // Debugging: Check if results are returned
    if (empty($results)) {
        echo "No categories found in the database.";
    }
} catch (PDOException $e) {
    echo "Error fetching categories: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="Online Library Management System" />
    <meta name="author" content="Your Name" />
    <title>Online Library Management System | Manage Categories</title>
    
    <!-- Stylesheets -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
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
<nav class="navbar">
    <div class="nav-brand">
        <img src="assets/img/magic book.logo.png" alt="Logo" class="logo">
    </div>
    <ul class="nav-links">
        <li><a href="librarian_dashboard.php">Home</a></li>
        <li><a href="add-category.php">Add Category</a></li>
        <li><a href="logout.php" class="login-btn">Logout</a></li>
    </ul>
</nav>

<div class="content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h4 class="header-line">Manage Categories</h4>
            </div>
        </div>

        <!-- Message Alerts -->
        <div class="row">
            <?php if (isset($_SESSION['error']) && $_SESSION['error'] != "") { ?>
                <div class="col-md-6">
                    <div class="alert alert-danger">
                        <strong>Error: </strong> 
                        <?php echo htmlentities($_SESSION['error']); ?>
                        <?php $_SESSION['error'] = ""; ?>
                    </div>
                </div>
            <?php } ?>

            <?php if (isset($_SESSION['msg']) && $_SESSION['msg'] != "") { ?>
                <div class="col-md-6">
                    <div class="alert alert-success">
                        <strong>Success: </strong> 
                        <?php echo htmlentities($_SESSION['msg']); ?>
                        <?php $_SESSION['msg'] = ""; ?>
                    </div>
                </div>
            <?php } ?>
            
            <?php if (isset($_SESSION['delmsg']) && $_SESSION['delmsg'] != "") { ?>
                <div class="col-md-6">
                    <div class="alert alert-success">
                        <strong>Success: </strong> 
                        <?php echo htmlentities($_SESSION['delmsg']); ?>
                        <?php $_SESSION['delmsg'] = ""; ?>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Category Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Categories Listing
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Category Name</th>
                                        <th>Status</th>
                                        <th>Creation Date</th>
                                        <th>Updation Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Display categories
                                    foreach ($results as $row) {
                                    ?>
                                        <tr>
                                            <td><?php echo htmlentities($row->id); ?></td>
                                            <td><?php echo htmlentities($row->categoryName); ?></td>
                                            <td><?php echo htmlentities($row->Status == 1 ? 'Active' : 'Inactive'); ?></td>
                                            <td><?php echo htmlentities($row->CreationDate); ?></td>
                                            <td><?php echo htmlentities($row->UpdationDate); ?></td>
                                            <td>  <a href="edit-category.php?id=<?php echo $row->id; ?>" class="btn btn-success">Edit</a>
                                                <a href="manage-categories.php?del=<?php echo $row->id; ?>" onclick="return confirm('Are you sure you want to delete?');" class="btn btn-danger">Delete</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
