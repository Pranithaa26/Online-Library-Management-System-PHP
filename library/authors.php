<?php
// Database connection
include('db_connection.php');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle search and filter
$search = $_GET['search'] ?? '';
$genreFilter = $_GET['genre'] ?? '';
$query = "SELECT * FROM authors WHERE AuthorName LIKE :search";
$params = [':search' => "%$search%"];

if (!empty($genreFilter)) {
    $query .= " AND Genre = :genre";
    $params[':genre'] = $genreFilter; // Assuming Genre column exists
}

$query .= " ORDER BY AuthorName ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authors Page</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .container { width: 90%; margin: auto; }
        .search-bar { margin: 20px 0; display: flex; justify-content: space-between; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .card { border: 1px solid #ddd; padding: 20px; border-radius: 5px; text-align: center; }
        .card img { max-width: 100px; border-radius: 50%; margin-bottom: 10px; }
        .card h3 { margin: 0; font-size: 1.2rem; }
        .card p { font-size: 0.9rem; color: #666; }
        .filters { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Authors</h1>

        <div class="search-bar">
            <form method="GET" style="flex: 1; margin-right: 10px;">
                <input type="text" name="search" placeholder="Search authors..." value="<?= htmlspecialchars($search) ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </form>
            <form method="GET">
                <select name="genre" onchange="this.form.submit()" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">Filter by Genre</option>
                    <option value="Fiction" <?= $genreFilter == 'Fiction' ? 'selected' : '' ?>>Fiction</option>
                    <option value="Non-fiction" <?= $genreFilter == 'Non-fiction' ? 'selected' : '' ?>>Non-fiction</option>
                </select>
            </form>
        </div>

        <div class="grid">
            <?php if ($authors): ?>
                <?php foreach ($authors as $author): ?>
                    <div class="card">
                        <img src="https://up.yimg.com/ib/th?id=OIP.awAiMS1BCAQ2xS2lcdXGlwHaHH&pid=Api&rs=1&c=1&qlt=95&w=125&h=120/<?= $author['id'] ?>.jpg" alt="<?= htmlspecialchars($author['AuthorName']) ?>">
                        <h3><?= htmlspecialchars($author['AuthorName']) ?></h3>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No authors found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
