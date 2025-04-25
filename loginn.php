<?php

include 'connection.php';

// Start session
session_start();



// Login Form Submission
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Get selected role

    // Query the database to find the user based on selected role
    $query = "SELECT * FROM users_roles WHERE username = '$username' AND role = '$role'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Verify user credentials
    if ($user && password_verify($password, $user['password'])) {
        // Start session and store user info
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on user role
        if ($user['role'] == 'admin') {
            header('Location: dashboard.php');
            exit;
        } else {
            header('Location: welcome.php');
            exit;
        }
    } else {
        $error_message = "Invalid username, password, or role.";
    }
}
?>

<main class="login-container">
    <div class="login-card">
        <h2>Login</h2>
        <form action="loginn.php" method="POST" class="login-form">
            <?php if (isset($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Enter your username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" id="role" required>
                    <option value="admin">Admin</option>
                    <option value="cashier">Cashier</option>
                </select>
            </div>

            <button type="submit" name="login" class="btn-login">Login</button>
        </form>
    </div>
</main>



<style>
/* Layout */
body {
    background-color: #f4f7fa;
    font-family: 'Arial', sans-serif;
}

.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    padding: 20px;
}

.login-card {
    background: #ffffff;
    padding: 30px;
    width: 350px;
    box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    text-align: center;
}

h2 {
    color: #333;
    font-size: 24px;
    margin-bottom: 20px;
}

.login-form .form-group {
    margin-bottom: 20px;
    text-align: left;
}

.login-form label {
    display: block;
    font-size: 14px;
    color: #555;
    margin-bottom: 8px;
}

.login-form input,
.login-form select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    margin-top: 5px;
}

.login-form input:focus,
.login-form select:focus {
    border-color: #BD4C41;
    outline: none;
}

.error {
    color: #e74c3c;
    font-size: 14px;
    margin-bottom: 20px;
}

.btn-login {
    background-color: #BD4C41;
    color: #fff;
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s;
}

.btn-login:hover {
    background-color: #a23d31;
}

footer {
    text-align: center;
    margin-top: 30px;
}

footer p {
    font-size: 12px;
    color: #777;
}
</style>
