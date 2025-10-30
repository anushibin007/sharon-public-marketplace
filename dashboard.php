<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

echo "Welcome, " . $_SESSION['username'] . " (" . $_SESSION['role'] . ")";
echo "<br><a href='logout.php'>Logout</a>";

if ($_SESSION['role'] == 'seller') {
    echo "<br><a href='add_product.php'>Add Product</a>";
    echo "<br><a href='products.php'>My Products</a>";
} else {
    echo "<br><a href='products.php'>View Products</a>";
    echo "<br><a href='cart.php'>My Cart</a>";
    echo "<br><a href='wishlist.php'>My Wishlist</a>";
}
?>