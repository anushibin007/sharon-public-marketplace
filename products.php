<?php
include 'config.php';

$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
}

$sql = "SELECT products.*, users.username AS seller_name FROM products 
        JOIN users ON products.seller_id = users.id";

if ($search) {
    $sql .= " WHERE products.name LIKE '%$search%'";
}

$result = $conn->query($sql);

?>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Search products" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<h2>Products</h2>
<?php
while ($row = $result->fetch_assoc()) {
    echo "<div style='border:1px solid #ccc;padding:10px;margin-bottom:10px;'>";
    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
    echo "<p>" . htmlspecialchars($row['description']) . "</p>";
    echo "<p>Price: ₹" . $row['price'] . "</p>";
    echo "<p>Seller: " . htmlspecialchars($row['seller_name']) . "</p>";
    echo "<br><a href='add_to_cart.php?product_id=" . $row['id'] . "'>Add to Cart</a>";
    echo " | <a href='add_to_wishlist.php?product_id=" . $row['id'] . "'>Add to Wishlist</a>";
    echo "</div>";
}
?>