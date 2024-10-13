<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize_input($_POST['name']);
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO categories (name, user_id) VALUES (?, ?)");
    
    if ($stmt->execute([$name, $user_id])) {
        $success = "Category added successfully";
    } else {
        $error = "Failed to add category";
    }
}

// Fetch existing categories
$stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$categories = $stmt->fetchAll();

include 'includes/header.php';
?>

<h2>Manage Categories</h2>

<?php if (isset($success)): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<h3>Add New Category</h3>
<form method="POST" action="">
    <input type="text" name="name" placeholder="Category Name" required>
    <input type="submit" value="Add Category">
</form>

<h3>Existing Categories</h3>
<ul>
    <?php foreach ($categories as $category): ?>
        <li><?php echo $category['name']; ?></li>
    <?php endforeach; ?>
</ul>

<?php include 'includes/footer.php'; ?>