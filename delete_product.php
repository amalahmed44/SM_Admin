<?php
// delete_product.php

include 'connection.php';

// Check if the product ID is set in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    // Delete the product from the products table
    $delete_product_query = "DELETE FROM products WHERE product_id = '$product_id'";

    if (mysqli_query($conn, $delete_product_query)) {
        echo "<script>alert('Product deleted successfully!'); window.location.href='products.php';</script>";
    } else {
        echo "<script>alert('Error deleting product from the database!'); window.location.href='products.php';</script>";
    }
} else {
    echo "<script>alert('Invalid product ID!'); window.location.href='products.php';</script>";
}
?>
