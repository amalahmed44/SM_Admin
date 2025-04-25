<?php
include 'header.php';
include 'sidebar.php';
include 'connection.php';

if (!isset($_GET['sale_id'])) {
    echo "Sale ID is missing in the URL!";
    exit;
}

$sale_id = $_GET['sale_id'];

// Get cashiers
$cashiers_result = mysqli_query($conn, "SELECT user_id, name FROM users WHERE role = 'cashier'");

// Get products
$products_result = mysqli_query($conn, "SELECT product_id, name, selling_price FROM products");
$products = [];
while ($p = mysqli_fetch_assoc($products_result)) {
    $products[$p['product_id']] = $p;
}

// Get sale
$query_sale = "
    SELECT s.*, u.name AS cashier_name 
    FROM sales s 
    JOIN users u ON s.cashier_id = u.user_id 
    WHERE s.sale_id = '$sale_id'
";
$result_sale = mysqli_query($conn, $query_sale);
if (mysqli_num_rows($result_sale) == 0) {
    echo "Sale not found.";
    exit;
}
$sale = mysqli_fetch_assoc($result_sale);

// Get sale items
$query_items = "
    SELECT si.*, p.name 
    FROM sale_items si 
    JOIN products p ON si.product_id = p.product_id 
    WHERE si.sale_id = '$sale_id'
";
$result_items = mysqli_query($conn, $query_items);
?>

<main class="admin-content">
    <h1>Edit Sale #<?php echo $sale_id; ?></h1>
    <form method="post" action="sales.php">
        <input type="hidden" name="sale_id" value="<?php echo $sale_id; ?>">

        <label for="cashier_id"><strong>Cashier:</strong></label>
        <select name="cashier_id" required style="padding: 8px; margin-bottom: 15px;">
            <?php while ($c = mysqli_fetch_assoc($cashiers_result)) { ?>
                <option value="<?= $c['user_id']; ?>" <?= $c['user_id'] == $sale['cashier_id'] ? 'selected' : ''; ?>>
                    <?= $c['name']; ?>
                </option>
            <?php } ?>
        </select><br>

        <label for="payment_method"><strong>Payment Method:</strong></label>
        <select name="payment_method" required style="padding: 8px; margin-bottom: 20px;">
            <option value="cash" <?= $sale['payment_method'] == 'cash' ? 'selected' : ''; ?>>Cash</option>
            <option value="card" <?= $sale['payment_method'] == 'card' ? 'selected' : ''; ?>>Card</option>
        </select>

        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background-color: #333; color: white;">
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody style="background-color: #fff;">
                <?php 
                $total = 0;
                while ($item = mysqli_fetch_assoc($result_items)) { 
                    $total += $item['subtotal'];
                ?>
                    <tr>
                        <td>
                            <select name="product_id[<?= $item['sale_item_id']; ?>]" required>
                                <?php foreach ($products as $pid => $p) { ?>
                                    <option value="<?= $pid; ?>" <?= $pid == $item['product_id'] ? 'selected' : ''; ?>>
                                        <?= $p['name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="quantity[<?= $item['sale_item_id']; ?>]" value="<?= $item['quantity']; ?>" min="1" required>
                        </td>
                        <td>
                            <input type="number" name="price[<?= $item['sale_item_id']; ?>]" value="<?= $item['selling_price']; ?>" step="0.01" required>
                        </td>
                        <td class="subtotal"><?= number_format($item['subtotal'], 2); ?></td>
                    </tr>
                <?php } ?>
                <tr style="background-color: #f2f2f2;">
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td id="total-cell"><strong><?= number_format($total, 2); ?></strong></td>
                </tr>
            </tbody>
        </table>

        <br>
        <button type="submit" name="sales" class="btn btn-primary">Update Sale</button>
    </form>
</main>

<!-- STYLES -->
<style>
    .admin-content {
        margin-left: 260px;
        padding: 90px 50px 20px 50px;
        width: calc(100% - 260px);
    }

    table, th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
    }

    select, input[type="number"] {
        padding: 5px;
        font-size: 14px;
    }

    .btn {
        padding: 10px 20px;
        background-color: #BD4C41;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn:hover {
        opacity: 0.85;
    }
</style>

<!-- JAVASCRIPT FOR DYNAMIC PRICE & TOTAL -->
<script>
    const productData = <?php echo json_encode($products); ?>;

    function updateRow(row) {
        const select = row.querySelector('select');
        const quantityInput = row.querySelector('input[name^="quantity"]');
        const priceInput = row.querySelector('input[name^="price"]');
        const subtotalCell = row.querySelector('.subtotal');

        const selectedProductId = select.value;
        const selectedProduct = productData[selectedProductId];

        if (selectedProduct && priceInput) {
            priceInput.value = parseFloat(selectedProduct.selling_price).toFixed(2);
        }

        const qty = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const subtotal = qty * price;
        subtotalCell.textContent = subtotal.toFixed(2);

        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(cell => {
            total += parseFloat(cell.textContent) || 0;
        });
        document.getElementById('total-cell').textContent = total.toFixed(2);
    }

    document.querySelectorAll('tbody tr').forEach(row => {
        const select = row.querySelector('select');
        const quantityInput = row.querySelector('input[name^="quantity"]');
        const priceInput = row.querySelector('input[name^="price"]');

        if (select && quantityInput && priceInput) {
            select.addEventListener('change', () => updateRow(row));
            quantityInput.addEventListener('input', () => updateRow(row));
            priceInput.addEventListener('input', () => updateRow(row));
        }
    });
</script>
