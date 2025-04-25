<?php
include 'connection.php';
include 'header.php';
include 'sidebar.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = $_POST['supplier_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, contact_person, phone, email, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $supplier_name, $contact_person, $phone, $email, $address);
    $stmt->execute();

    header("Location: suppliers.php?success=1");
    exit;
}

$result = $conn->query("SELECT * FROM suppliers ORDER BY supplier_id DESC");
?>


<!-- MAIN CONTENT START -->
<main style="margin-left: 250px; padding: 30px; padding-top: 80px; background: #f5f5f5; min-height: 100vh;">
<h2 style="margin-bottom: 20px;">Supplier Management</h2>

<?php if (isset($_GET['success'])): ?>
    <p style="color: green; font-weight: bold;">✅ Supplier added successfully!</p>
<?php endif; ?>

<form method="POST" style="background: #f8f8f8; padding: 20px; border-radius: 10px; margin-bottom: 40px; max-width: 600px;">
    <h3 style="margin-bottom: 15px;">➕ Add New Supplier</h3>

    <input type="text" name="supplier_name" placeholder="Supplier Name *" required class="input-box"><br>
    <input type="text" name="contact_person" placeholder="Contact Person" class="input-box"><br>
    <input type="text" name="phone" placeholder="Phone" class="input-box"><br>
    <input type="email" name="email" placeholder="Email" class="input-box"><br>
    <textarea name="address" placeholder="Address" class="input-box" style="height: 80px;"></textarea><br>
    <button type="submit" class="btn">Add Supplier</button>
</form>
    <h2 style="margin-bottom: 20px;">Suppliers</h2>

    <!-- Styled Base Container for Table -->
    <div style="background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.07);">
        <h3 style="margin-bottom: 15px;">📋 All Suppliers</h3>

        <table style="width: 100%; border-collapse: collapse; background: #fefefe;">
            <thead style="background: #444; color: #fff;">
                <tr>
                    <th style="padding: 12px;">ID</th>
                    <th style="padding: 12px;">supplier_name</th>
                    <th style="padding: 12px;">Contact Person</th>
                    <th style="padding: 12px;">Phone</th>
                    <th style="padding: 12px;">Email</th>
                    <th style="padding: 12px;">Address</th>
                    <th style="padding: 12px;">Added On</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px; text-align: center;"><?= $row['supplier_id'] ?></td>
                        <td style="padding: 12px;"><?= $row['supplier_name'] ?></td>
                        <td style="padding: 12px;"><?= $row['contact_person'] ?></td>
                        <td style="padding: 12px;"><?= $row['phone'] ?></td>
                        <td style="padding: 12px;"><?= $row['email'] ?></td>
                        <td style="padding: 12px;"><?= $row['address'] ?></td>
                        <td style="padding: 12px;"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</main>
<!-- MAIN CONTENT END -->
<style>
    .input-box {
        width: 100%;
        padding: 10px;
        margin-bottom: 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .btn {
        padding: 10px 20px;
        background: brown;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn:hover {
        background: darkred;
    }

    
</style>
<?php include 'footer.php'; ?>
