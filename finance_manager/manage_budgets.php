<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = sanitize_input($_POST['category_id']);
    $amount = sanitize_input($_POST['amount']);
    $start_date = sanitize_input($_POST['start_date']);
    $end_date = sanitize_input($_POST['end_date']);

    $stmt = $pdo->prepare("INSERT INTO budgets (user_id, category_id, amount, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$_SESSION['user_id'], $category_id, $amount, $start_date, $end_date])) {
        $success = "Budget added successfully";
    } else {
        $error = "Failed to add budget";
    }
}

// Fetch categories for the dropdown
$stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$categories = $stmt->fetchAll();

// Fetch existing budgets
$stmt = $pdo->prepare("SELECT b.*, c.name as category_name FROM budgets b 
                       JOIN categories c ON b.category_id = c.id 
                       WHERE b.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$budgets = $stmt->fetchAll();

include 'includes/header.php';
?>

<h2>Manage Budgets</h2>

<?php if (isset($success)): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<h3>Add New Budget</h3>
<form method="POST" action="">
    <select name="category_id" required>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
        <?php endforeach; ?>
    </select><br>
    <input type="number" step="0.01" name="amount" placeholder="Budget Amount" required><br>
    <input type="date" name="start_date" required><br>
    <input type="date" name="end_date" required><br>
    <input type="submit" value="Add Budget">
</form>

<h3>Existing Budgets</h3>
<table border="1">
    <tr>
        <th>Category</th>
        <th>Amount</th>
        <th>Start Date</th>
        <th>End Date</th>
    </tr>
    <?php foreach ($budgets as $budget): ?>
        <tr>
            <td><?php echo $budget['category_name']; ?></td>
            <td><?php echo $budget['amount']; ?></td>
            <td><?php echo $budget['start_date']; ?></td>
            <td><?php echo $budget['end_date']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include 'includes/footer.php'; ?>