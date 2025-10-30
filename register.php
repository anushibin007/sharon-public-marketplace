<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; // buyer or seller
    $phone = $conn->real_escape_string($_POST['phone']);

    $sql = "INSERT INTO users (username, password, role, phone) VALUES ('$username', '$password', '$role', '$phone')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful. <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<form method="POST" action="">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    Phone: <input type="text" name="phone" required><br>
    Role: <select name="role" required>
        <option value="buyer">Buyer</option>
        <option value="seller">Seller</option>
    </select><br>
    <button type="submit">Register</button>
</form>