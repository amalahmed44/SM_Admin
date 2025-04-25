<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php include 'connection.php'; ?>

<!-- Include necessary libraries for PDF & Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

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
    .btn-warning { background: #005A32; color: White; }
    .btn-danger { background: #dc3545; color: white; }

    .btn:hover {
        opacity: 0.8;
    }

    .table-container {
        margin-top: 30px;
    }

    .status-available {
        color: white;
        background-color: green;
        padding: 5px;
        border-radius: 5px;
        text-align: center;
    }

    .status-unavailable {
        color: white;
        background-color: red;
        padding: 5px;
        border-radius: 5px;
        text-align: center;
    }

    .export-buttons {
        margin-top: 20px;
        display: flex;
        gap: 10px;
    }

    .export-buttons button {
        background-color: #2C1A15;
        color: white;
        border: none;
        padding: 8px 14px;
        border-radius: 6px;
        cursor: pointer;
    }

    .export-buttons button:hover {
        background-color: #BD4C41;
    }
</style>

<main class="admin-content">
    <h1>Manage Products</h1>

    <!-- Add Product Button -->
    <a href="add_product.php" class="btn btn-primary">Add New Product</a>

    <!-- Export Buttons -->
    <div class="export-buttons">
        <button onclick="printPage()">🖨️ Print</button>
        <button onclick="exportToPDF()">📄 Export PDF</button>
        <button onclick="exportToExcel()">📊 Export Excel</button>
    </div>

    <!-- Products Table -->
    <div class="table-container">
        <table class="table" id="productsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Stock</th>
                    <th>Sold</th>
                    <th>Left In Stock</th>
                    <th>Status</th>
                    <th>Supplier</th>
                    <th>Manufacture Date</th>
                    <th>Expire Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT p.product_id, p.name, c.category_name, p.cost_price, p.selling_price, p.stock, p.sold_quantity, p.supplier_id, p.manufacture_date, p.expire_date, s.supplier_name
                          FROM products p
                          LEFT JOIN categories c ON p.category_id = c.category_id
                          LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id";
                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    $left_in_stock = $row['stock'] - $row['sold_quantity'];
                    $status = ($left_in_stock > 0) ? 'Available' : 'Unavailable';
                    $status_class = ($left_in_stock > 0) ? 'status-available' : 'status-unavailable';

                    echo "<tr>";
                    echo "<td>{$row['product_id']}</td>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['category_name']}</td>";
                    echo "<td>$" . number_format($row['cost_price'], 2) . "</td>";
                    echo "<td>$" . number_format($row['selling_price'], 2) . "</td>";
                    echo "<td>{$row['stock']}</td>";
                    echo "<td>{$row['sold_quantity']}</td>";
                    echo "<td>$left_in_stock</td>";
                    echo "<td><span class='{$status_class}'>{$status}</span></td>";
                    echo "<td>{$row['supplier_name']}</td>";
                    echo "<td>{$row['manufacture_date']}</td>";
                    echo "<td>{$row['expire_date']}</td>";
                    echo "<td>
                            <a href='edit_product.php?id={$row['product_id']}' class='btn btn-warning'>Edit</a>
                            <a href='delete_product.php?id={$row['product_id']}' class='btn btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                        </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    function printPage() {
        window.print();
    }

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.text("Product List", 14, 16);
        doc.autoTable({
            html: '#productsTable',
            startY: 20,
            styles: { fontSize: 8 }
        });
        doc.save('products.pdf');
    }

    function exportToExcel() {
        const table = document.getElementById("productsTable");
        const workbook = XLSX.utils.table_to_book(table, { sheet: "Products" });
        XLSX.writeFile(workbook, 'products.xlsx');
    }
</script>

<?php include 'footer.php'; ?>
