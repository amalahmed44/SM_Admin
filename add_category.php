<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php include 'connection.php'; ?>

<style>
    /* Custom styles for category form */
    
    
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

    .add-category-form {
        width: 50%;
        margin: 30px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .add-category-form label {
        font-size: 16px;
        margin-bottom: 8px;
        color: #333;
    }

    .add-category-form input {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .add-category-form button {
        padding: 12px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .add-category-form button:hover {
        background-color: #0056b3;
    }
</style>

<main class="admin-content">
    <h1>Add New Category</h1>
<!-- Button to go back to products.php -->
<button class="back-btn" onclick="window.location.href='products.php'">Back</button>
    <!-- Category Add Form -->
    <form class="add-category-form" action="add_category.php" method="POST">
        <label for="category_name">Category Name:</label>
        <input type="text" id="category_name" name="category_name" required>

        <button type="submit" name="submit">Add Category</button>
    </form>
</main>

<?php
// Handle category form submission
if (isset($_POST['submit'])) {
    $category_name = $_POST['category_name'];

    // Insert the category into the database
    $query = "INSERT INTO categories (category_name) VALUES ('$category_name')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Category added successfully!'); window.location.href='products.php';</script>";
    } else {
        echo "<script>alert('Error adding category!');</script>";
    }
}
?>

<?php include 'footer.php'; ?>
