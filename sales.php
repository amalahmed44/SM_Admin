<?php
include 'header.php';
include 'sidebar.php';
include 'connection.php';

// Fetch cashiers
$query_cashiers = "SELECT user_id, name FROM users WHERE role = 'cashier'";
$result_cashiers = mysqli_query($conn, $query_cashiers);

// Fetch products with prices
$query_products = "SELECT product_id, name, selling_price FROM products";
$result_products = mysqli_query($conn, $query_products);
$products = [];
while ($product = mysqli_fetch_assoc($result_products)) {
    $products[$product['product_id']] = [
        'name' => $product['name'],
        'selling_price' => $product['selling_price']
    ];
}

// Handle sale form submission
if (isset($_POST['submit_sale'])) {
    $cashier_id = $_POST['cashier_id'];
    $payment_method = $_POST['payment_method'];
    $sale_date = date('Y-m-d H:i:s');
    $total_amount = $_POST['total_amount'];

    $insert_sale_query = "INSERT INTO sales (cashier_id, total_amount, payment_method, sale_date) 
                          VALUES ('$cashier_id', '$total_amount', '$payment_method', '$sale_date')";
    if (mysqli_query($conn, $insert_sale_query)) {
        $sale_id = mysqli_insert_id($conn);

        foreach ($_POST['product_id'] as $key => $product_id) {
            $quantity = $_POST['quantity'][$key];
            $selling_price = $products[$product_id]['selling_price']; // get price from DB

            $insert_sale_item_query = "INSERT INTO sale_items (sale_id, product_id, quantity, price) 
                                       VALUES ('$sale_id', '$product_id', '$quantity', '$selling_price')";
            mysqli_query($conn, $insert_sale_item_query);

            // Update sold quantity in products
            $update_query = "UPDATE products SET sold_quantity = sold_quantity + $quantity WHERE product_id = $product_id";
            mysqli_query($conn, $update_query);
        }

        echo "<script>alert('Sale added successfully!'); window.location.href='sales.php';</script>";
    } else {
        echo "<script>alert('Error adding sale!');</script>";
    }
}
?>

<main class="admin-content" style="padding: 20px;">
    <h1>Manage Sales</h1>
    <a href="manual_sale.php" class="btn btn-primary">Add New Sale</a>

    <h1 style="margin-top: 50px; text-align: center;">All Sales</h1>
    <div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>Sale ID</th>
                <th>Product Name</th>
                <th>Cashier</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Line Total</th>
                <th>Payment</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $query_sales = "
                SELECT s.sale_id, s.payment_method, s.sale_date, 
                       si.quantity, si.price, 
                       (si.quantity * si.price) as line_total,
                       p.name AS product_name, 
                       u.name AS cashier_name
                FROM sales s
                JOIN sale_items si ON s.sale_id = si.sale_id
                JOIN products p ON si.product_id = p.product_id
                JOIN users u ON s.cashier_id = u.user_id
                ORDER BY s.sale_id DESC
            ";
            $result_sales = mysqli_query($conn, $query_sales);
            while ($row = mysqli_fetch_assoc($result_sales)) { ?>
                <tr>
                    <td><?php echo $row['sale_id']; ?></td>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['cashier_name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td>$<?php echo number_format($row['line_total'], 2); ?></td>
                    <td><?php echo $row['payment_method']; ?></td>
                    <td><?php echo $row['sale_date']; ?></td>
                    <td>
                        <a href="edit_sale.php?sale_id=<?php echo $row['sale_id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="delete_sale.php?id=<?php echo $row['sale_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this sale?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>


</main>

<style>
    .admin-content {
        margin-left: 260px;
        padding: 90px 50px 20px 50px;
        width: calc(100% - 260px);
        transition: 0.5s;
    }

    h1 {
        font-size: 26px;
        margin-bottom: 20px;
        color: #2C1A15;
    }

    .btn {
        padding: 5px 10px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary { background: #BD4C41; color: white; }
    .btn-warning { background: #005A32; color: white; }
    .btn-danger { background: #dc3545; color: white; }

    .btn:hover { opacity: 0.8; }

 
    .admin-content {
        margin-left: 260px;
        padding: 90px 50px 20px 50px;
        width: calc(100% - 260px);
        transition: 0.5s;
    }

    h1 {
        font-size: 26px;
        margin-bottom: 20px;
        color: #2C1A15;
    }

    .table-container {
        margin-top: 30px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
        background-color: #fff;
    }

    .table th, .table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    .table th {
        background: #2C1A15;
        color: white;
    }

    .table td {
        color: #333;
    }

    .btn {
        padding: 5px 10px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary { background: #BD4C41; color: white; }
    .btn-warning { background: #005A32; color: white; }
    .btn-danger { background: #dc3545; color: white; }

    .btn:hover {
        opacity: 0.8;
    }

</style>
