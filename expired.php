<?php
include 'header.php';
include 'sidebar.php';
include('connection.php');

$today = date('Y-m-d');
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'expired';

// Determine date range based on filter
switch ($filter) {
    case 'today':
        $label = "Expiring Today";
        $where = "expire_date = '$today'";
        break;
    case '7days':
        $label = "Expiring in 7 Days";
        $week = date('Y-m-d', strtotime('+7 days'));
        $where = "expire_date > '$today' AND expire_date <= '$week'";
        break;
    case '1month':
        $label = "Expiring in 1 Month";
        $month = date('Y-m-d', strtotime('+1 month'));
        $where = "expire_date > '$today' AND expire_date <= '$month'";
        break;
    case '6months':
        $label = "Expiring in 6 Months";
        $six_months = date('Y-m-d', strtotime('+6 months'));
        $where = "expire_date > '$today' AND expire_date <= '$six_months'";
        break;
    default:
        $label = "Expired Products";
        $where = "expire_date < '$today'";
        break;
}

$query = "
    SELECT p.product_id, p.name AS product_name, p.stock, p.expire_date, c.category_name, s.supplier_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
    WHERE $where
    ORDER BY expire_date ASC
";

$result = mysqli_query($conn, $query);
?>

<main class="admin-content">
    <h1>Product Expiry Management</h1>

    <div class="admin-widgets">
        <div class="widget">
            <a href="?filter=expired">
                <i class="fas fa-exclamation-circle"></i>
                <span>Expired</span>
            </a>
        </div>
        <div class="widget">
            <a href="?filter=today">
                <i class="fas fa-calendar-day"></i>
                <span>Expires Today</span>
            </a>
        </div>
        <div class="widget">
            <a href="?filter=7days">
                <i class="fas fa-clock"></i>
                <span>In 7 Days</span>
            </a>
        </div>
        <div class="widget">
            <a href="?filter=1month">
                <i class="fas fa-calendar-week"></i>
                <span>In 1 Month</span>
            </a>
        </div>
        <div class="widget">
            <a href="?filter=6months">
                <i class="fas fa-calendar-alt"></i>
                <span>In 6 Months</span>
            </a>
        </div>
    </div>

    <h2 style="margin-top: 30px;"><?php echo $label; ?></h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Stock</th>
                    <th>Expiry Date</th>
                    <th>Category</th>
                    <th>Supplier</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['product_id']; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['stock']; ?></td>
                        <td style="color: red;"><?php echo $row['expire_date']; ?></td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td><?php echo $row['supplier_name']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: green;">No products found for this filter.</p>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
<style>
.admin-widgets {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 30px;
}

.widget {
    flex: 1;
    min-width: 180px;
    background: #f0f0f0;
    border-radius: 12px;
    text-align: center;
    padding: 20px;
    transition: 0.2s ease;
}

.widget:hover {
    background: #BD4C41;
}

.widget i {
    font-size: 24px;
    color: black;
    margin-bottom: 10px;
}

.widget span {
    display: block;
    font-weight: bold;
}
.styled-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.styled-table thead tr {
    background-color: #BD4C41;
    color: #ffffff;
    text-align: left;
}

.styled-table th,
.styled-table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
}

.styled-table tbody tr {
    background-color: #f9f9f9;
    transition: background 0.3s;
}

.styled-table tbody tr:nth-of-type(even) {
    background-color: #f1f1f1;
}

.styled-table tbody tr:hover {
    background-color: #ffe1e1;
}

.styled-table td {
    color: #333;
}

.styled-table td[style*="color: red"] {
    font-weight: bold;
}

</style>
