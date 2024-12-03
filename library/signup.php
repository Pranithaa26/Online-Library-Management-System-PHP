<?php
session_start();
include('includes/config.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to sanitize inputs
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Initialize $account_type to avoid the undefined variable warning
$account_type = $_POST['account_type'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['emailid'] ?? '');
    $password = sanitize_input($_POST['password'] ?? '');
    $confirm_password = sanitize_input($_POST['confirm_password'] ?? '');
    $account_type = sanitize_input($_POST['account_type'] ?? ''); // Make sure to capture account_type

    // Input validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($account_type)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Password hashing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Check if the email already exists
            $sql = "SELECT email FROM account WHERE email = :email";
            $query = $dbh->prepare($sql);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            if ($query->rowCount() > 0) {
                $error_message = "Email is already registered.";
            } else {
                // Insert into the account table (without 'confirmPassword')
                $sql = "INSERT INTO account (username, email, password, accountType) 
                        VALUES (:username, :email, :password, :account_type)";
                $query = $dbh->prepare($sql);
                $query->bindParam(':username', $username, PDO::PARAM_STR);
                $query->bindParam(':email', $email, PDO::PARAM_STR);
                $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
                $query->bindParam(':account_type', $account_type, PDO::PARAM_STR);
                $query->execute();

                // Insert into the corresponding user or librarian table
                if ($account_type === 'librarian') {
                    $sql = "INSERT INTO librarian (name, email) VALUES (:username, :email)";
                } else {
                    $sql = "INSERT INTO user (name, email) VALUES (:username, :email)";
                }
                $query = $dbh->prepare($sql);
                $query->bindParam(':username', $username, PDO::PARAM_STR);
                $query->bindParam(':email', $email, PDO::PARAM_STR);
                $query->execute();

                $success_message = ucfirst($account_type) . " account created successfully!";
                header("Location: index.php?message=" . urlencode($success_message));
                exit();
            }
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <style>
        .container {
            margin-top: 50px;
            max-width: 600px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }
        .alert {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Create Account</h2>
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php elseif (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" placeholder="Username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="emailid" class="form-control" placeholder="Email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
        </div>
        <div class="form-group">
            <label>Account Type</label><br>
            <input type="radio" name="account_type" value="librarian" <?php echo ($account_type === 'librarian') ? 'checked' : ''; ?>> Librarian
            <input type="radio" name="account_type" value="user" <?php echo ($account_type === 'user') ? 'checked' : ''; ?>> User
        </div>
        <button type="submit" name="signup" class="btn btn-primary">Signup</button>
    </form>
</div>

</body>
</html>
