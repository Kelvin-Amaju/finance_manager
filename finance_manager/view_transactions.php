<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = sanitize_input($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$id, $_SESSION['user_id']])) {
        $success = "Transaction deleted successfully";
    } else {
        $error = "Failed to delete transaction";
    }
}

// Handle search and filter
$where_clause = "t.user_id = ?";
$params = [$_SESSION['user_id']];

if (isset($_GET['search'])) {
    $search = sanitize_input($_GET['search']);
    $where_clause .= " AND (t.description LIKE ? OR c.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (isset($_GET['type']) && in_array($_GET['type'], ['income', 'expense'])) {
    $type = sanitize_input($_GET['type']);
    $where_clause .= " AND t.type = ?";
    $params[] = $type;
}

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = sanitize_input($_GET['start_date']);
    $end_date = sanitize_input($_GET['end_date']);
    $where_clause .= " AND t.date BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
}

$stmt = $pdo->prepare("SELECT t.*, c.name as category_name FROM transactions t 
                       LEFT JOIN categories c ON t.category_id = c.id 
                       WHERE $where_clause ORDER BY t.date DESC");
$stmt->execute($params);
$transactions = $stmt->fetchAll();

include 'includes/header.php';
?>

<h2>View Transactions</h2>

<?php if (isset($success)): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Search description or category" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <select name="type">
        <option value="">All Types</option>
        <option value="income" <?php echo (isset($_GET['type']) && $_GET['type'] == 'income') ? 'selected' : ''; ?>>Income</option>
        <option value="expense" <?php echo (isset($_GET['type']) && $_GET['type'] == 'expense') ? 'selected' : ''; ?>>Expense</option>
    </select>
    <input type="date" name ="start_date" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">
    <input type="date" name="end_date" value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>">
    <input type="submit" value="Search">
</form>

<table border="1">
    <tr>
        <th>Date</th>
        <th>Type</th>
        <th>Category</th>
        <th>Amount</th>
        <th>Description</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($transactions as $transaction): ?>
        <tr>
            <td><?php echo $transaction['date']; ?></td>
            <td><?php echo ucfirst($transaction['type']); ?></td>
            <td><?php echo $transaction['category_name']; ?></td>
            <td><?php echo $transaction['amount']; ?></td>
            <td><?php echo $transaction['description']; ?></td>
            <td>
                <a href="edit_transaction.php?id=<?php echo $transaction['id']; ?>">Edit</a>
                <a href="view_transactions.php?delete=<?php echo $transaction['id']; ?>" onclick="return confirm('Are you sure you want to delete this transaction?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include 'includes/footer.php'; ?>