<?php
include 'header.php';
include 'sidebar.php';
include 'connection.php';

// Get today's date and other date ranges
$today = date('Y-m-d');
$week_ago = date('Y-m-d', strtotime('-7 days'));
$month_ago = date('Y-m-d', strtotime('-1 month'));
$year_ago = date('Y-m-d', strtotime('-1 year'));

// Inventory Stats
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))['total'];
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE stock < 10"))['total'];
$expired = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE expire_date < '$today'"))['total'];
$expiring_soon = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE expire_date BETWEEN '$today' AND DATE_ADD('$today', INTERVAL 7 DAY)"))['total'];

// Users Stats
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
$admins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'admin'"))['total'];
$cashiers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'cashier'"))['total'];

// Sales Summary for different periods
$Revenue_today = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM sales WHERE DATE(sale_date) = '$today'"))['total'] ?? 0;
$Revenue_week = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM sales WHERE DATE(sale_date) >= '$week_ago'"))['total'] ?? 0;
$Revenue_month = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM sales WHERE DATE(sale_date) >= '$month_ago'"))['total'] ?? 0;
$Revenue_year = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM sales WHERE DATE(sale_date) >= '$year_ago'"))['total'] ?? 0;

// Top-selling products
$top_products_query = mysqli_query($conn, "
    SELECT p.name, SUM(si.quantity) AS total_sold 
    FROM sale_items si 
    JOIN products p ON si.product_id = p.product_id 
    GROUP BY si.product_id 
    ORDER BY total_sold DESC 
    LIMIT 5
");

// Filter Sales by Date Range
$filter_from = isset($_GET['from_date']) ? $_GET['from_date'] : null;
$filter_to = isset($_GET['to_date']) ? $_GET['to_date'] : null;

$filter_query = "";
if ($filter_from && $filter_to) {
    $filter_query = "WHERE DATE(sale_date) BETWEEN '$filter_from' AND '$filter_to'";
} else {
    $filter_query = "WHERE DATE(sale_date) = '$today'";
}

$sales_query = "
    SELECT s.sale_id, s.sale_date, SUM(si.quantity * si.price) AS total_amount 
    FROM sales s
    JOIN sale_items si ON s.sale_id = si.sale_id
    $filter_query
    GROUP BY s.sale_id
    ORDER BY s.sale_date DESC
";
$sales_result = mysqli_query($conn, $sales_query);
?>

<main class="admin-content">
    <h1>System Reports</h1>

    <!-- Inventory and User Widgets -->
    <div class="admin-widgets">
        <div class="widget red"><i class="fas fa-box"></i><span>Total Products</span><strong><?php echo $total_products; ?></strong></div>
        <div class="widget orange"><i class="fas fa-exclamation-triangle"></i><span>Low Stock</span><strong><?php echo $low_stock; ?></strong></div>
        <div class="widget red-dark"><i class="fas fa-times-circle"></i><span>Expired</span><strong><?php echo $expired; ?></strong></div>
        <div class="widget yellow"><i class="fas fa-clock"></i><span>Expiring Soon</span><strong><?php echo $expiring_soon; ?></strong></div>
        <div class="widget green"><i class="fas fa-users"></i><span>Total Users</span><strong><?php echo $total_users; ?></strong></div>
        <div class="widget blue"><i class="fas fa-user-shield"></i><span>Admins</span><strong><?php echo $admins; ?></strong></div>
        <div class="widget teal"><i class="fas fa-user-tag"></i><span>Cashiers</span><strong><?php echo $cashiers; ?></strong></div>
    </div>

    <!-- Revenue Summary Widgets -->
    <div class="admin-widgets">
        <div class="widget Revenue-today"><i class="fas fa-calendar-day"></i><span>Revenue Today</span><strong>$<?php echo number_format($Revenue_today, 2); ?></strong></div>
        <div class="widget Revenue-week"><i class="fas fa-calendar-week"></i><span>Revenue This Week</span><strong>$<?php echo number_format($Revenue_week, 2); ?></strong></div>
        <div class="widget Revenue-month"><i class="fas fa-calendar-month"></i><span>Revenue This Month</span><strong>$<?php echo number_format($Revenue_month, 2); ?></strong></div>
        <div class="widget Revenue-year"><i class="fas fa-calendar-year"></i><span>Revenue This Year</span><strong>$<?php echo number_format($Revenue_year, 2); ?></strong></div>
    </div>

 
    <!-- Filter Form -->
    <section class="filter-box">
        <h2>Filter Revenue by Date</h2>
        <form action="reports.php" method="get">
            <div class="filter-group">
                <label for="from_date">From:</label>
                <input type="date" id="from_date" name="from_date" value="<?php echo $filter_from; ?>">

                <label for="to_date">To:</label>
                <input type="date" id="to_date" name="to_date" value="<?php echo $filter_to; ?>">

                <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Apply Filter</button>
            </div>
        </form>
    </section>

    <!-- Filtered Results -->
    <section class="report-box">
        <h2>Results</h2>
        <?php if (mysqli_num_rows($sales_result) > 0): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_revenue = 0;
                    while ($row = mysqli_fetch_assoc($sales_result)) { 
                        $total_revenue += $row['total_amount'];
                    ?>
                        <tr>
                            <td><?php echo $row['sale_id']; ?></td>
                            <td><?php echo $row['sale_date']; ?></td>
                            <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" style="text-align: right;">Total Revenue:</th>
                        <th>$<?php echo number_format($total_revenue, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        <?php else: ?>
            <p>No sales found for this period.</p>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>
  
<style>
    .admin-widgets {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin: 30px 0;
}
.widget {
    flex: 1;
    min-width: 180px;
    padding: 20px;
    border-radius: 12px;
    background: #f4f4f4;
    color: #333;
    text-align: center;
    transition: 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.widget i {
    font-size: 22px;
    margin-bottom: 8px;
    display: block;
}
.widget span {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}
.widget strong {
    font-size: 24px;
    color: #222;
}
.red { background-color: #FF8C8C; }
.red-dark { background-color: #e57373; }
.orange { background-color: #f4a261; }
.yellow { background-color: #f6d365; }
.green { background-color: #81c784; }
.blue { background-color: #64b5f6; }
.teal { background-color: #4dd0e1; }

.Revenue-today { background-color: #81c784; }
.Revenue-week { background-color: #64b5f6; }
.Revenue-month { background-color: #f4a261; }
.Revenue-year { background-color: #f6d365; }

.report-box {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.report-box h2 {
    font-size: 20px;
    margin-bottom: 15px;
    color: #BD4C41;
}
.styled-table {
    width: 100%;
    border-collapse: collapse;
}
.styled-table th, .styled-table td {
    padding: 12px 15px;
    border: 1px solid #eee;
    text-align: left;
}
.styled-table thead {
    background-color: #BD4C41;
    color: white;
}
.styled-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}
.styled-table tbody tr:hover {
    background-color: #fff0f0;
}

/* Fancy Filter Design */
.filter-box {
    background: #f4f4f4;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}
.filter-box h2 {
    font-size: 20px;
    margin-bottom: 15px;
    color: #BD4C41;
}
.filter-group {
    display: flex;
    gap: 15px;
    align-items: center;
}
.filter-group label {
    font-size: 14px;
    font-weight: bold;
    color: #555;
}
.filter-group input {
    padding: 8px 15px;
    border-radius: 8px;
    border: 1px solid #ddd;
    width: 200px;
    font-size: 14px;
}
.filter-group input:focus {
    border-color: #BD4C41;
    outline: none;
    box-shadow: 0 0 0 2px rgba(189, 76, 65, 0.2);
}
.filter-group button {
    padding: 10px 20px;
    background-color: #BD4C41;
    color: white;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s ease;
}
.filter-group button:hover {
    background-color: #a33d30;
}
</style>