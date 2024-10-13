<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

if (!isset($_GET['id'])) {
    redirect('view_transactions.php');
}

$id = sanitize_input($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = sanitize_input($_POST['amount']);
    $type = sanitize_input($_POST['type']);
    $category_id = sanitize_input($_POST['category_id']);
    $date = sanitize_input($_POST['date']);
    $description = sanitize_input($_POST['description']);

    $stmt = $pdo->prepare("UPDATE transactions SET amount = ?, type = ?, category_id = ?, date = ?, description = ? WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$amount, $type, $category_id, $date, $description, $id, $_SESSION['user_id']])) {
        $success = "Transaction updated successfully";
    } else {
        $error = "Failed to update transaction";
    }
}

// Fetch transaction data
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$transaction = $stmt->fetch();

if (!$transaction) {
    redirect('view_transactions.php');
}

// Fetch categories for the dropdown
$stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$categories = $stmt->fetchAll();

include 'includes/header.php';
?>

<h2>Edit Transaction</h2>

<?php if (isset($success)): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST" action="">
    <input type="number" step="0.01" name="amount" value="<?php echo $transaction['amount']; ?>" required><br>
    <select name="type" required>
        <option value="income" <?php echo $transaction['type'] == 'income' ? 'selected' : ''; ?>>Income</option>
        <option value="expense" <?php echo $transaction['type'] == 'expense' ? 'selected' : ''; ?>>Expense</option>
    </select><br>
    <select name="category_id" required>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>" <?php echo $transaction['category_id'] == $category['id'] ? 'selected' : ''; ?>><?php echo $category['name']; ?></option>
        <?php endforeach; ?>
    </select><br>
    <input type="date" name="date" value="<?php echo $transaction['date']; ?>" required><br>
    <textarea name="description"><?php echo $transaction['description']; ?></textarea><br>
    <input type="submit" value="Update Transaction">
</form>

<?php include 'includes/footer.php'; ?>