<?php
include 'header.php';
include 'sidebar.php';
include('connection.php');

$current_month = date('m');
$current_year = date('Y');
$today = date('Y-m-d');

// Total Sales this month
$sales_query = "SELECT COUNT(*) as total_sales FROM sales WHERE MONTH(sale_date) = '$current_month' AND YEAR(sale_date) = '$current_year'";
$sales_result = mysqli_query($conn, $sales_query);
$total_sales = mysqli_fetch_assoc($sales_result)['total_sales'];

// Total Revenue this month
$revenue_query = "SELECT SUM(total_amount) as total_revenue FROM sales WHERE MONTH(sale_date) = '$current_month' AND YEAR(sale_date) = '$current_year'";
$revenue_result = mysqli_query($conn, $revenue_query);
$total_revenue = mysqli_fetch_assoc($revenue_result)['total_revenue'];

// Total Products
$products_query = "SELECT COUNT(*) as total_products FROM products";
$products_result = mysqli_query($conn, $products_query);
$total_products = mysqli_fetch_assoc($products_result)['total_products'];

// Total Expired Products
$expired_query = "SELECT COUNT(*) as expired_products FROM products WHERE expire_date < '$today'";
$expired_result = mysqli_query($conn, $expired_query);
$total_expired = mysqli_fetch_assoc($expired_result)['expired_products'];

// Total Users
$users_query = "SELECT COUNT(*) as total_users FROM users";
$users_result = mysqli_query($conn, $users_query);
$total_users = mysqli_fetch_assoc($users_result)['total_users'];

// Total Suppliers
$suppliers_query = "SELECT COUNT(*) as total_suppliers FROM suppliers";
$suppliers_result = mysqli_query($conn, $suppliers_query);
$total_suppliers = mysqli_fetch_assoc($suppliers_result)['total_suppliers'];

// Most sold product categories this month
$product_categories_query = "
    SELECT c.category_name as name, SUM(si.quantity) as total_sold
    FROM sale_items si
    JOIN products p ON si.product_id = p.product_id
    JOIN categories c ON p.category_id = c.category_id
    JOIN sales s ON si.sale_id = s.sale_id
    WHERE MONTH(s.sale_date) = '$current_month' AND YEAR(s.sale_date) = '$current_year'
    GROUP BY c.category_id
    ORDER BY total_sold DESC
";

$product_categories_result = mysqli_query($conn, $product_categories_query);
$product_categories = [];
while ($row = mysqli_fetch_assoc($product_categories_result)) {
    $product_categories[] = ['name' => $row['name'], 'sold' => $row['total_sold']];
}
?>

<script>
    // Pass PHP data to JS
    const productCategories = <?php echo json_encode($product_categories); ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Prepare data for the chart
    const categoryLabels = productCategories.map(cat => cat.name);
    const categorySales = productCategories.map(cat => cat.sold);

    const ctx = document.getElementById('categoriesChart').getContext('2d');

    const categoriesChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: 'Top Selling Categories This Month',
                data: categorySales,
                backgroundColor: [
                    '#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff',
                    '#f67019', '#f53794', '#537bc4', '#acc236', '#166a8f'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
});
</script>


<main class="admin-content">
    <h1>Dashboard</h1>
    
    <div class="admin-widgets">
        <div class="widget">
            <a href="sales.php">
                <i class="fas fa-shopping-cart"></i> 
                <span>Total Sales: <?php echo $total_sales; ?></span>
            </a>
        </div>

        <div class="widget">
            <a href="products.php">
                <i class="fas fa-box"></i> 
                <span>Total Products: <?php echo $total_products; ?></span>
            </a>
        </div>

        <div class="widget">
            <i class="fas fa-dollar-sign"></i> 
            <span>Total Revenue: $<?php echo number_format($total_revenue, 2); ?></span>
        </div>

        <div class="widget">
            <a href="expired.php">
                <i class="fas fa-exclamation-triangle"></i> 
                <span>Expired Items: <?php echo $total_expired; ?></span>
            </a>
        </div>

        <div class="widget">
            <a href="users.php">
                <i class="fas fa-users"></i> 
                <span>Total Users: <?php echo $total_users; ?></span>
            </a>
        </div>

        <div class="widget">
            <a href="suppliers.php">
                <i class="fas fa-truck"></i> 
                <span>Total Suppliers: <?php echo $total_suppliers; ?></span>
            </a>
        </div>
    </div>
    <h1 style=" margin-top:30px;"> Top Selling Categories This Month</h1>
    <div class="charts-container" style="max-width: 400px; margin-top:30px;">
    <canvas id="categoriesChart" style="max-height: 600px;"></canvas>
</div>


   
</main>


<?php include 'footer.php'; ?>
