<?php
include 'header.php';
include 'sidebar.php';
include 'connection.php';

// Add User
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing password
    $role = $_POST['role'];

    // Insert user into the users_roles table
    $query = "INSERT INTO users_roles (username, password, role) VALUES ('$username', '$password', '$role')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('User added successfully!'); window.location.href='users.php';</script>";
    } else {
        echo "<script>alert('Error adding user!');</script>";
    }
}

// Delete User
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM users_roles WHERE user_id = '$delete_id'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('User deleted successfully!'); window.location.href='users.php';</script>";
    } else {
        echo "<script>alert('Error deleting user!');</script>";
    }
}

$query = "SELECT * FROM users_roles";
$result = mysqli_query($conn, $query);
?>

<main class="admin-content">
    <br><br>
    <h1>User Management</h1>

    <section class="form-section">
        <h2>Add New User</h2>
        <form action="users.php" method="POST" class="user-form">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Role:</label>
                <select name="role" required>
                    <option value="admin">Admin</option>
                    <option value="cashier">Cashier</option>
                </select>
            </div>

            <button type="submit" name="add_user" class="btn-add">Add User</button>
        </form>
    </section>

    <section class="table-section">
        <h2>Existing Users</h2>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo ucfirst($user['role']); ?></td>
                        <td>
                            <a href="edit_users.php?id=<?php echo $user['user_id']; ?>" class="btn-edit">Edit</a>
                            <a href="users.php?delete_id=<?php echo $user['user_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </section>
</main>

<?php include 'footer.php'; ?>

<style>
/* Layout */
.admin-content {
    padding: 30px;
    background-color: #f9f9f9;
    min-height: 100vh;
    font-family: 'Segoe UI', sans-serif;
}

h1 {
    font-size: 28px;
    margin-bottom: 30px;
    color: #333;
}

/* Form */
.form-section {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 40px;
}

.form-section h2 {
    font-size: 22px;
    margin-bottom: 20px;
    color: #BD4C41;
}

.user-form .form-group {
    margin-bottom: 15px;
}

.user-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.user-form input,
.user-form select {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}

.btn-add {
    background-color: #BD4C41;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 15px;
    cursor: pointer;
    transition: 0.2s;
}

.btn-add:hover {
    background-color: black;
    color: white;
}

/* Table Section */
.table-section {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.table-section h2 {
    font-size: 22px;
    margin-bottom: 15px;
    color: #BD4C41;
}

/* Table Styling */
.styled-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
}

.styled-table thead {
    background-color: #BD4C41;
    color: white;
}

.styled-table th,
.styled-table td {
    padding: 12px 15px;
    border: 1px solid #eee;
    text-align: left;
}

.styled-table tbody tr:nth-child(even) {
    background-color: #f5f5f5;
}

.styled-table tbody tr:hover {
    background-color: #ffe1e1;
}

/* Action Buttons */
.btn-edit,
.btn-delete {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 14px;
    text-decoration: none;
    margin-right: 5px;
}

.btn-edit {
    background-color: green;
    color: white;
}

.btn-edit:hover {
    background-color: white;
    color: black;
}

.btn-delete {
    background-color: #e74c3c;
    color: white;
}

.btn-delete:hover {
    background-color: white;
    color: black;
}
</style>
