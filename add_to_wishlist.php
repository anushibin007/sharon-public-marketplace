<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['product_id'])) {
    $buyer_id = $_SESSION['user_id'];
    $product_id = intval($_GET['product_id']);

    // Check if product already in wishlist
    $sql = "SELECT * FROM wishlist WHERE buyer_id = $buyer_id AND product_id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "Product is already in your wishlist! <a href='wishlist.php'>View Wishlist</a> | <a href='products.php'>Continue Shopping</a>";
    } else {
        // Add to wishlist
        $sql = "INSERT INTO wishlist (buyer_id, product_id) VALUES ($buyer_id, $product_id)";
        if ($conn->query($sql) === TRUE) {
            echo "Product added to wishlist! <a href='wishlist.php'>View Wishlist</a> | <a href='products.php'>Continue Shopping</a>";
        } else {
            echo "Error adding to wishlist: " . $conn->error;
        }
    }
} else {
    echo "Invalid product. <a href='products.php'>Go back to products</a>";
}
?>