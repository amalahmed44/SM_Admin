<?php
include 'connection.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Sale ID is missing!";
    exit;
}

$sale_id = intval($_GET['id']);

// First delete related items
mysqli_query($conn, "DELETE FROM sale_items WHERE sale_id = $sale_id");

// Then delete the sale
mysqli_query($conn, "DELETE FROM sales WHERE sale_id = $sale_id");

// Redirect back to sales page
header("Location: sales.php");
exit;
?>
