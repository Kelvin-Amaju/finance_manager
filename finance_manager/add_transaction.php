<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = sanitize_input($_POST['amount']);
    $type = sanitize_input($_POST['type']);
    $category_id = sanitize_input($_POST['category_id']);
    $date = sanitize_input($_POST['date']);
    $description = sanitize_input($_POST['description']);

    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, type, category_id, date, description) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$user_id, $amount, $type, $category_id, $date, $description])) {
        $success = "Transaction added successfully";
    } else {
        $error = "Failed to add transaction";
    }
}

// Fetch categories for the dropdown
$stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$categories = $stmt->fetchAll();

include 'includes/header.php';
?>

<h2>Add Transaction</h2>

<?php if (isset($success)): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST" action="">
    <input type="number" step="0.01" name="amount" placeholder="Amount" required><br>
    <select name="type" required>
        <option value="income">Income</option>
        <option value="expense">Expense</option>
    </select><br>
    <select name="category_id" required>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
        <?php endforeach; ?>
    </select><br>
    <input type="date" name="date" required><br>
    <textarea name="description" placeholder="Description"></textarea><br>
    <input type="submit" value="Add Transaction">
</form>

<?php include 'includes/footer.php'; ?>