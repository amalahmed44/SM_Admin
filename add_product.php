<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php include 'connection.php'; ?>

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

    .add-category-btn {
        padding: 12px 20px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin-bottom: 20px;
    }

    .add-category-btn:hover {
        background-color: #218838;
    }

    .add-product-form {
        width: 70%;
        margin: 30px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .add-product-form label {
        font-size: 16px;
        margin-bottom: 8px;
        color: #333;
    }

    .add-product-form input,
    .add-product-form select {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .add-product-form button {
        padding: 12px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .add-product-form button:hover {
        background-color: #0056b3;
    }
</style>

<main class="admin-content">
    <!-- Button to go back to products.php -->
    <button class="back-btn" onclick="window.location.href='products.php'">Back</button>

    <h1>Add New Product</h1>

    <!-- Button to Add New Category -->
    <button class="add-category-btn" onclick="window.location.href='add_category.php'">Add New Category</button>

    <!-- Product Add Form -->
    <form class="add-product-form" action="add_product.php" method="POST">
        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <?php
            $query = "SELECT * FROM categories";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['category_id'] . "'>" . $row['category_name'] . "</option>";
            }
            ?>
        </select>

        <label for="cost_price">Cost Price:</label>
        <input type="number" id="cost_price" name="cost_price" step="0.01" required>

        <label for="selling_price">Selling Price:</label>
        <input type="number" id="selling_price" name="selling_price" step="0.01" required>

        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" required>

        <label for="manufacture_date">Manufacture Date:</label>
        <input type="date" id="manufacture_date" name="manufacture_date" required>

        <label for="expire_date">Expire Date:</label>
        <input type="date" id="expire_date" name="expire_date" required>

        <label for="supplier">Supplier:</label>
        <select id="supplier" name="supplier_id" required>
            <option value="">Select Supplier</option>
            <?php
            $suppliers = mysqli_query($conn, "SELECT * FROM suppliers");
            while ($row = mysqli_fetch_assoc($suppliers)) {
                echo "<option value='" . $row['supplier_id'] . "'>" . $row['supplier_name'] . "</option>";
            }
            ?>
        </select>

        <button type="submit" name="submit">Add Product</button>
    </form>
</main>

<?php
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $cost_price = $_POST['cost_price'];
    $selling_price = $_POST['selling_price'];
    $stock = $_POST['stock'];
    $manufacture_date = $_POST['manufacture_date'];
    $expire_date = $_POST['expire_date'];
    $supplier_id = $_POST['supplier_id'];

    $query = "INSERT INTO products (name, category_id, cost_price, selling_price, stock, manufacture_date, expire_date, supplier_id) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "siddissi", $name, $category, $cost_price, $selling_price, $stock, $manufacture_date, $expire_date, $supplier_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Product added successfully!'); window.location.href='products.php';</script>";
    } else {
        echo "<script>alert('Error adding product!');</script>";
    }
}
?>

<?php include 'footer.php'; ?>
