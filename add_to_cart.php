<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['product_id'])) {
    $buyer_id = $_SESSION['user_id'];
    $product_id = intval($_GET['product_id']);

    // Check if product already in cart
    $sql = "SELECT * FROM cart WHERE buyer_id=$buyer_id AND product_id=$product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Increase quantity
        $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE buyer_id=$buyer_id AND product_id=$product_id");
    } else {
        // Add new item to cart
        $conn->query("INSERT INTO cart (buyer_id, product_id, quantity) VALUES ($buyer_id, $product_id, 1)");
    }
    
    echo "Product added to cart! <a href='products.php'>Continue Shopping</a> | <a href='cart.php'>View Cart</a>";
}
?>