<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

// Fetch summary data
$stmt = $pdo->prepare("SELECT 
                        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
                        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
                       FROM transactions 
                       WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$summary = $stmt->fetch();

$balance = $summary['total_income'] - $summary['total_expense'];

include 'includes/header.php';
?>

<h2>Dashboard</h2>

<div class="dashboard-grid">
    <div class="summary-card">
        <h3>Total Income</h3>
        <p>$<?php echo number_format($summary['total_income'], 2); ?></p>
    </div>
    <div class="summary-card">
        <h3>Total Expenses</h3>
        <p>$<?php echo number_format($summary['total_expense'], 2); ?></p>
    </div>
    <div class="summary-card">
        <h3>Current Balance</h3>
        <p>$<?php echo number_format($balance, 2); ?></p>
    </div>
</div>

<div class="card">
    <h3>Quick Actions</h3>
    <a href="add_transaction.php" class="btn">Add Transaction</a>
    <a href="view_transactions.php" class="btn">View Transactions</a>
    <a href="manage_budgets.php" class="btn">Manage Budgets</a>
    <a href="reports.php" class="btn">Financial Reports</a>
</div>

<?php include 'includes/footer.php'; ?>