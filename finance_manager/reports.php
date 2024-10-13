<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

// Fetch monthly income and expenses for the current year
$stmt = $pdo->prepare("SELECT 
                        MONTH(date) as month,
                        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
                        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
                       FROM transactions 
                       WHERE user_id = ? AND YEAR(date) = YEAR(CURDATE())
                       GROUP BY MONTH(date)
                       ORDER BY MONTH(date)");
$stmt->execute([$_SESSION['user_id']]);
$monthly_data = $stmt->fetchAll();

// Prepare data for Chart.js
$months = [];
$income = [];
$expenses = [];

foreach ($monthly_data as $data) {
    $months[] = date("F", mktime(0, 0, 0, $data['month'], 10));
    $income[] = $data['income'];
    $expenses[] = $data['expense'];
}

include 'includes/header.php';
?>

<h2>Financial Reports</h2>

<div class="chart-container">
    <canvas id="monthlyChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var ctx = document.getElementById('monthlyChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Income',
            data: <?php echo json_encode($income); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        },
        {
            label: 'Expenses',
            data: <?php echo json_encode($expenses); ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>