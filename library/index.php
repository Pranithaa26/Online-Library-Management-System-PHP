<?php
session_start();
include('includes/config.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['login'])) {
    $account_type = $_POST['account_type'];
    $email = $_POST['emailid'];
    $password = $_POST['password'];

    try {
        // Convert account type to lowercase and store it in a variable
        $account_type_lower = strtolower($account_type);

        // Check credentials in the account table
        $sql = "SELECT email, password, accountType FROM account WHERE email = :email AND accountType = :account_type";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':account_type', $account_type_lower, PDO::PARAM_STR);
        $query->execute();

        if ($query->rowCount() > 0) {
            $result = $query->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if (password_verify($password, $result['password'])) {
                $_SESSION['login'] = $email;  // Store email in session for logged-in status

                if ($account_type == 'librarian') {
                    // Retrieve and set librarian ID in session
                    $sql = "SELECT librarianID FROM librarian WHERE email = :email";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':email', $email, PDO::PARAM_STR);
                    $query->execute();
                    $librarian = $query->fetch(PDO::FETCH_ASSOC);
                    $_SESSION['librarian_id'] = $librarian['librarianID'];  // Librarian session ID

                    header('Location: admin/librarian_dashboard.php');
                    exit();
                } else {
                    // Retrieve and set user ID in session
                    $sql = "SELECT userID FROM user WHERE email = :email";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':email', $email, PDO::PARAM_STR);
                    $query->execute();
                    $user = $query->fetch(PDO::FETCH_ASSOC);
                    $_SESSION['user_id'] = $user['userID'];  // User session ID

                    header('Location: dashboard.php');
                    exit();
                }
            } else {
                echo "<script>alert('Invalid login details');</script>";
            }
        } else {
            echo "<script>alert('Invalid login details');</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <style>
        body {
            background-image: url('assets/img/bookshelf_background.png');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            font-family: 'Open Sans', sans-serif;
        }

        .account-type-container {
            max-width: 400px;
            margin: auto;
            text-align: center;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.7); /* Optional: adds a dark background to the container */
            border-radius: 10px; /* Optional: rounded corners */
        }

        .account-type-container h2 {
            margin-bottom: 20px;
            color: white;
        }

        .account-option p {
            color: white;
        }

        .account-option {
            display: inline-block;
            width: 150px;
            padding: 15px;
            background-color: black;
            cursor: pointer;
            border: 2px solid #ddd;
            border-radius: 8px;
            margin: 10px;
            transition: 0.3s;
        }

        .account-option img {
            width: 50px;
            height: 50px;
        }

        .account-option:hover,
        .account-option.selected {
            border-color: #007bff;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            color: white; /* Change label color to white */
        }

        input.form-control {
            color: white; /* Change input text color to white */
            background-color: rgba(255, 255, 255, 0.1); /* Change background to slightly transparent white */
            border-color: #ddd;
        }

        input[type="radio"] {
            accent-color: white; /* Change radio button color to white */
        }

        input[type="radio"]:checked {
            accent-color: #007bff; /* Change color of selected radio button */
        }

        .btn-login {
            width: 100%;
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: none;
        }

        .footer-text {
            margin-top: 30px;
            color: white;
            text-align: center;
        }

        .footer-text a {
            color: #007bff; /* Make the link color blue */
        }

    </style>
</head>
<body>

<div class="container account-type-container">
    <h2>Login</h2>
    <form method="post">
        <div class="form-group">
            <label for="emailid">Email:</label>
            <input type="email" name="emailid" class="form-control" id="emailid" placeholder="Email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
        </div>
        <div class="form-group">
            <label>Account Type:</label>
            <br>
            <input type="radio" name="account_type" value="librarian" checked> Librarian
            <input type="radio" name="account_type" value="user"> User
        </div>
        <button type="submit" name="login" class="btn-login">Login</button>
    </form>

    <p class="footer-text">Don't have an account? <a href="signup.php" style="color: white;">Sign up</a></p>
</div>

</body>
</html>
