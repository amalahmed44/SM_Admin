<?php
include 'header.php';
include 'sidebar.php';
include 'connection.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product data
    $query = "SELECT * FROM products WHERE product_id = '$product_id'";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);
} else {
    echo "<script>alert('Product not found!'); window.location.href='products.php';</script>";
}

// Handle update
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $cost_price = $_POST['cost_price'];
    $selling_price = $_POST['selling_price'];
    $stock = $_POST['stock'];
    $supplier = $_POST['supplier'];
    $manufacture_date = $_POST['manufacture_date'];
    $expire_date = $_POST['expire_date'];

    $update_query = "UPDATE products 
                     SET name = '$name', category_id = '$category', cost_price = '$cost_price', 
                         selling_price = '$selling_price', stock = '$stock', supplier_id = '$supplier',
                         manufacture_date = '$manufacture_date', expire_date = '$expire_date' 
                     WHERE product_id = '$product_id'";

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Product updated successfully!'); window.location.href='products.php';</script>";
    } else {
        echo "<script>alert('Error updating product!');</script>";
    }
}
?>

<!-- Edit Product Form -->
<main class="admin-content">
    <h1 style="font-size: 24px; font-weight: bold; margin-bottom: 20px;">Edit Product</h1>
    <form action="edit_product.php?id=<?php echo $product['product_id']; ?>" method="POST">
        <label>Product Name:</label>
        <input type="text" name="name" value="<?php echo $product['name']; ?>" required class="input-field">

        <label>Category:</label>
        <select name="category" required class="input-field">
            <?php
            $category_query = "SELECT * FROM categories";
            $category_result = mysqli_query($conn, $category_query);
            while ($category = mysqli_fetch_assoc($category_result)) {
                echo "<option value='" . $category['category_id'] . "' " . 
                     ($category['category_id'] == $product['category_id'] ? 'selected' : '') . ">" . 
                     $category['category_name'] . "</option>";
            }
            ?>
        </select>

        <label>Cost Price:</label>
        <input type="number" name="cost_price" value="<?php echo $product['cost_price']; ?>" step="0.01" required class="input-field">

        <label>Selling Price:</label>
        <input type="number" name="selling_price" value="<?php echo $product['selling_price']; ?>" step="0.01" required class="input-field">

        <label>Stock:</label>
        <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required class="input-field">

        <label>Supplier:</label>
        <select name="supplier" required class="input-field">
            <?php
            $supplier_query = "SELECT * FROM suppliers";
            $supplier_result = mysqli_query($conn, $supplier_query);
            while ($supplier = mysqli_fetch_assoc($supplier_result)) {
                echo "<option value='" . $supplier['supplier_id'] . "' " .
                     ($supplier['supplier_id'] == $product['supplier_id'] ? 'selected' : '') . ">" . 
                     $supplier['supplier_name'] . "</option>";
            }
            ?>
        </select>

        <label>Manufacture Date:</label>
        <input type="date" name="manufacture_date" value="<?php echo $product['manufacture_date']; ?>" required class="input-field">

        <label>Expire Date:</label>
        <input type="date" name="expire_date" value="<?php echo $product['expire_date']; ?>" required class="input-field">

        <button type="submit" name="update" class="btn-submit">Update Product</button>
    </form>
</main>

<?php include 'footer.php'; ?>

<!-- Optional: Inline styling for consistency -->
<style>
    .input-field {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    label {
        font-size: 16px;
        margin-bottom: 8px;
        color: #333;
        display: block;
    }
    .btn-submit {
        padding: 12px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
</style>
