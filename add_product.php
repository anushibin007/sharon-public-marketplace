<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $desc = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    
    $seller_id = $_SESSION['user_id'];
    $sql = "INSERT INTO products (seller_id, name, description, price) VALUES ($seller_id, '$name', '$desc', $price)";
    
    if ($conn->query($sql) === TRUE) {
        echo "Product added successfully. <a href='dashboard.php'>Back to Dashboard</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<form method="POST" action="">
    Product Name: <input type="text" name="name" required><br>
    Description: <textarea name="description" required></textarea><br>
    Price: <input type="number" step="0.01" name="price" required><br>
    <button type="submit">Add Product</button>
</form>