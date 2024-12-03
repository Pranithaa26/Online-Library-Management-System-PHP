<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

// Validate and fetch the 'catid' parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || intval($_GET['id']) <= 0) {
    die("Invalid or missing Category ID.");
}

$catid = intval($_GET['id']); // Ensure 'catid' is a valid integer

// Debugging: Output the category ID
echo "Category ID: " . $catid . "<br>";

// Fetch the category data for editing
$sql = "SELECT * FROM category WHERE id=:id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $catid, PDO::PARAM_INT);
$query->execute();

// Check if any result is returned
$result = $query->fetch(PDO::FETCH_OBJ);

// Debugging: Check if a category was found
if (!$result) {
    echo "No category found for ID: " . $catid;
    die();  // Stop execution if no category is found
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet"> <!-- Custom styles -->
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Edit Category</h2>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Category Info</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label for="category">Category Name</label>
                        <!-- Display category name if found -->
                        <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlentities($result->CategoryName ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="status" value="1" <?php echo ($result->Status == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Active</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="status" value="0" <?php echo ($result->Status == 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Inactive</label>
                        </div>
                    </div>

                    <button type="submit" name="update" class="btn btn-info btn-block">Update</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
