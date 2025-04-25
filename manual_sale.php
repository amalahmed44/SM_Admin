<?php
include 'header.php';    // Include the header
include 'sidebar.php';   // Include the sidebar
include 'connection.php'; // Include database connection

// Fetch cashiers (users with 'cashier' role)
$query_cashiers = "SELECT user_id, name FROM users WHERE role = 'cashier'";
$result_cashiers = mysqli_query($conn, $query_cashiers);

// Fetch products and their prices
$query_products = "SELECT product_id, name, cost_price, selling_price, stock FROM products";
$result_products = mysqli_query($conn, $query_products);
$products = [];
while ($product = mysqli_fetch_assoc($result_products)) {
    $products[$product['product_id']] = [
        'name' => $product['name'],
        'cost_price' => $product['cost_price'],
        'selling_price' => $product['selling_price'],
        'stock' => $product['stock']
    ];
}

// Handle form submission for new sale
if (isset($_POST['submit_sale'])) {
    $cashier_id = $_POST['cashier_id'];
    $payment_method = $_POST['payment_method'];
    $sale_date = date('Y-m-d H:i:s'); // current date and time
    $total_amount = $_POST['total_amount']; // Total calculated based on items

    // Check stock availability before proceeding with sale
    $stock_check_passed = true;
    foreach ($_POST['product_id'] as $key => $product_id) {
        $quantity = $_POST['quantity'][$key];

        // Fetch available stock from the products table
        $product_stock = $products[$product_id]['stock'];

        if ($quantity > $product_stock) {
            $stock_check_passed = false;
            echo "<script>alert('Insufficient stock for product: {$products[$product_id]['name']}. Available stock is {$product_stock}.');</script>";
            break;
        }
    }

    if ($stock_check_passed) {
        // Insert the sale record into the sales table
        $insert_sale_query = "INSERT INTO sales (cashier_id, total_amount, payment_method, sale_date) 
                              VALUES ('$cashier_id', '$total_amount', '$payment_method', '$sale_date')";
        if (mysqli_query($conn, $insert_sale_query)) {
            $sale_id = mysqli_insert_id($conn); // Get the last inserted sale_id

            // Now insert the sale items into the sale_items table and update product stock & sold quantity
            foreach ($_POST['product_id'] as $key => $product_id) {
                $quantity = $_POST['quantity'][$key];
                $selling_price = $_POST['selling_price'][$key];
                $subtotal = $quantity * $selling_price;

                // Insert into sale_items table
                $insert_sale_item_query = "INSERT INTO sale_items (sale_id, product_id, quantity, price, subtotal) 
                                           VALUES ('$sale_id', '$product_id', '$quantity', '$selling_price', '$subtotal')";
                mysqli_query($conn, $insert_sale_item_query);

                // Update stock and sold_quantity in the products table
                $update_product_query = "
                    UPDATE products
                    SET stock = stock - '$quantity', sold_quantity = sold_quantity + '$quantity'
                    WHERE product_id = '$product_id'
                ";
                mysqli_query($conn, $update_product_query);
            }

            echo "<script>alert('Sale added successfully!'); window.location.href='sales.php';</script>";
        } else {
            echo "<script>alert('Error adding sale!');</script>";
        }
    }
}
?>

<main class="admin-content" style="padding: 20px; background-color: #f9f9f9;">
    <br> <br> <br>
    <h1 style="text-align: center; color: #2c3e50; font-size: 2rem; margin-bottom: 20px;">Add New Sale</h1>
    <button class="back-btn" onclick="window.location.href='sales.php'">Back</button>
    <form method="POST" action="manual_sale.php" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <!-- Cashier Selection -->
        <div class="form-group" style="margin-bottom: 15px;">
            <label for="cashier_id" style="font-size: 1rem; color: #34495e;">Cashier:</label>
            <select name="cashier_id" id="cashier_id" required style="width: 100%; padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 5px; background-color: #fafafa;">
                <option value="">Select Cashier</option>
                <?php while ($cashier = mysqli_fetch_assoc($result_cashiers)) { ?>
                    <option value="<?php echo $cashier['user_id']; ?>"><?php echo $cashier['name']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div id="sale_items">
            <!-- Add Sale Items -->
            <div class="sale_item" style="margin-bottom: 15px; padding: 10px; background-color: #ecf0f1; border-radius: 8px;">
                <label for="product_id[]" style="font-size: 1rem; color: #34495e;">Product:</label>
                <select name="product_id[]" class="product_id" onchange="updatePrice(0)" required style="width: 100%; padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 5px; background-color: #fafafa;">
                    <option value="">Select a product</option>
                    <?php foreach ($products as $product_id => $product) { ?>
                        <option value="<?php echo $product_id; ?>"><?php echo $product['name']; ?></option>
                    <?php } ?>
                </select>

                <label for="quantity[]" style="font-size: 1rem; color: #34495e; margin-top: 10px;">Quantity:</label>
                <input type="number" name="quantity[]" class="quantity" oninput="calculateTotalAmount()" required style="width: 100%; padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 5px; background-color: #fafafa;">

                <label for="selling_price[]" style="font-size: 1rem; color: #34495e; margin-top: 10px;">Selling Price:</label>
                <input type="number" name="selling_price[]" class="selling_price" readonly required style="width: 100%; padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 5px; background-color: #fafafa;">

                <button type="button" onclick="removeSaleItem(this)" style="background-color: #e74c3c; color: white; padding: 8px 15px; border-radius: 5px; margin-top: 10px; border: none;">Remove Item</button>
            </div>
        </div>

        <button type="button" onclick="addSaleItem()" style="background-color: #3498db; color: white; padding: 8px 15px; border-radius: 5px; margin-top: 20px; border: none; cursor: pointer;">Add Another Item</button>

        <div class="form-group" style="margin-top: 20px;">
            <label for="payment_method" style="font-size: 1rem; color: #34495e;">Payment Method:</label>
            <select name="payment_method" id="payment_method" required style="width: 100%; padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 5px; background-color: #fafafa;">
                <option value="">Select Payment Method</option>
                <option value="Cash">Cash</option>
                <option value="Card">Card</option>
                <option value="Mobile Payment">Mobile Payment</option>
            </select>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <label for="total_amount" style="font-size: 1rem; color: #34495e;">Total Amount:</label>
            <input type="number" name="total_amount" id="total_amount" readonly required style="width: 100%; padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 5px; background-color: #fafafa;">
        </div>

        <button type="submit" name="submit_sale" style="background-color: #2ecc71; color: white; padding: 12px 20px; border-radius: 5px; margin-top: 30px; border: none; width: 100%;">Submit Sale</button>
    </form>
</main>

<script>
// Update the price when a product is selected
function updatePrice(index) {
    const productSelect = document.querySelectorAll('.product_id')[index];
    const priceInput = document.querySelectorAll('.selling_price')[index];
    const selectedId = productSelect.value;

    // Fetch the corresponding selling price from the products array
    const productPrices = <?php echo json_encode($products); ?>;
    if (selectedId && productPrices[selectedId]) {
        priceInput.value = productPrices[selectedId].selling_price;
    }
    calculateTotalAmount();
}

// Add a new sale item
function addSaleItem() {
    const saleItemsContainer = document.getElementById('sale_items');
    const newSaleItem = document.querySelector('.sale_item').cloneNode(true);
    newSaleItem.querySelector('.quantity').value = '';
    newSaleItem.querySelector('.selling_price').value = '';
    newSaleItem.querySelector('.product_id').value = '';
    saleItemsContainer.appendChild(newSaleItem);
}

// Remove a sale item
function removeSaleItem(button) {
    button.closest('.sale_item').remove();
    calculateTotalAmount();
}

// Calculate the total amount of the sale
function calculateTotalAmount() {
    let totalAmount = 0;
    const quantities = document.querySelectorAll('.quantity');
    const sellingPrices = document.querySelectorAll('.selling_price');

    for (let i = 0; i < quantities.length; i++) {
        const quantity = parseFloat(quantities[i].value);
        const sellingPrice = parseFloat(sellingPrices[i].value);
        if (!isNaN(quantity) && !isNaN(sellingPrice)) {
            totalAmount += quantity * sellingPrice;
        }
    }

    document.getElementById('total_amount').value = totalAmount.toFixed(2);
}
</script>
 <style>
     .back-btn {
        padding: 12px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin-bottom: 20px;
    }

    .back-btn:hover {
        background-color: #0056b3;
    }
</style>