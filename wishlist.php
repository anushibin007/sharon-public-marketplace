<?php
include 'config.php';

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header('Location: login.php');
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Handle wishlist actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $wishlist_item_id = intval($_POST['wishlist_item_id']);
    
    if ($_POST['action'] === 'remove') {
        $sql = "DELETE FROM wishlist WHERE id = $wishlist_item_id AND buyer_id = $buyer_id";
        $conn->query($sql);
    } elseif ($_POST['action'] === 'add_to_cart') {
        // Get product_id from wishlist item
        $sql = "SELECT product_id FROM wishlist WHERE id = $wishlist_item_id AND buyer_id = $buyer_id";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $product_id = $row['product_id'];
            
            // Check if product already in cart
            $check_cart = "SELECT * FROM cart WHERE buyer_id = $buyer_id AND product_id = $product_id";
            $cart_result = $conn->query($check_cart);
            
            if ($cart_result->num_rows > 0) {
                // Increase quantity
                $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE buyer_id = $buyer_id AND product_id = $product_id");
            } else {
                // Add new item to cart
                $conn->query("INSERT INTO cart (buyer_id, product_id, quantity) VALUES ($buyer_id, $product_id, 1)");
            }
            
            // Optionally remove from wishlist after adding to cart
            $conn->query("DELETE FROM wishlist WHERE id = $wishlist_item_id AND buyer_id = $buyer_id");
        }
    }
    
    // Redirect to avoid form resubmission
    header('Location: wishlist.php');
    exit();
}

// Get wishlist items with product details
$sql = "SELECT w.id as wishlist_id, p.id as product_id, p.name, p.description, p.price, u.username as seller_name, w.added_at
        FROM wishlist w 
        JOIN products p ON w.product_id = p.id 
        JOIN users u ON p.seller_id = u.id 
        WHERE w.buyer_id = $buyer_id 
        ORDER BY w.added_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Wishlist - Online Marketplace</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .wishlist-item { border: 1px solid #ccc; padding: 15px; margin-bottom: 10px; border-radius: 5px; display: flex; gap: 15px; }
        .wishlist-item-image { flex-shrink: 0; }
        .wishlist-item-content { flex-grow: 1; }
        .wishlist-header { background-color: #f5f5f5; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        .price { font-weight: bold; color: #2c5aa0; }
        .btn { padding: 8px 15px; margin: 5px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-cart { background-color: #4CAF50; color: white; }
        .btn-remove { background-color: #f44336; color: white; }
        .btn-primary { background-color: #2196F3; color: white; }
        .empty-wishlist { text-align: center; padding: 50px; color: #666; }
        .navigation { margin-bottom: 20px; }
        .navigation a { margin-right: 15px; text-decoration: none; color: #2196F3; }
        .item-meta { color: #666; font-size: 0.9em; margin-top: 10px; }
        .actions { margin-top: 15px; }
        .stats { background-color: #e8f4fd; padding: 15px; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="navigation">
        <a href="dashboard.php">← Back to Dashboard</a>
        <a href="products.php">Browse Products</a>
        <a href="cart.php">View Cart</a>
    </div>

    <div class="wishlist-header">
        <h1>My Wishlist</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <p>Save your favorite items for later purchase.</p>
    </div>

    <?php if ($result->num_rows == 0): ?>
        <div class="empty-wishlist">
            <h2>Your wishlist is empty</h2>
            <p>Browse our products and add items to your wishlist by clicking the "Add to Wishlist" button.</p>
            <a href="products.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="wishlist-item">
                <div class="wishlist-item-image">
                    <img src='image.php?id=<?php echo $row['product_id']; ?>' width='100' height='100' style='object-fit: cover; border: 1px solid #ddd; border-radius: 5px;' alt='Product Image'>
                </div>
                <div class="wishlist-item-content">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <p><strong>Seller:</strong> <?php echo htmlspecialchars($row['seller_name']); ?></p>
                    <p class="price">Price: ₹<?php echo number_format($row['price'], 2); ?></p>
                    
                    <div class="item-meta">
                        <p>Added to wishlist: <?php echo date('M j, Y g:i A', strtotime($row['added_at'])); ?></p>
                    </div>
                    
                    <div class="actions">
                    <form method="POST" action="" style="display: inline-block;">
                        <input type="hidden" name="wishlist_item_id" value="<?php echo $row['wishlist_id']; ?>">
                        <button type="submit" name="action" value="add_to_cart" class="btn btn-cart">
                            Add to Cart
                        </button>
                    </form>
                    
                    <form method="POST" action="" style="display: inline-block;">
                        <input type="hidden" name="wishlist_item_id" value="<?php echo $row['wishlist_id']; ?>">
                        <button type="submit" name="action" value="remove" class="btn btn-remove" 
                                onclick="return confirm('Are you sure you want to remove this item from your wishlist?')">
                            Remove from Wishlist
                        </button>
                    </form>
                    
                    <a href="products.php?search=<?php echo urlencode($row['name']); ?>" class="btn btn-primary">
                        View Similar Products
                    </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>

        <div class="stats">
            <h3>Wishlist Summary</h3>
            <p><strong>Total items:</strong> <?php echo $result->num_rows; ?></p>
            <p><strong>Total estimated value:</strong> ₹<?php 
                // Calculate total value
                $result->data_seek(0); // Reset result pointer
                $total_value = 0;
                while ($row = $result->fetch_assoc()) {
                    $total_value += $row['price'];
                }
                echo number_format($total_value, 2);
            ?></p>
            
            <div style="margin-top: 15px;">
                <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                <form method="POST" action="" style="display: inline-block;">
                    <button type="button" class="btn btn-cart" onclick="addAllToCart()">
                        Add All to Cart
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function addAllToCart() {
            if (confirm('Add all wishlist items to your cart?')) {
                // Get all wishlist item forms and submit them
                const forms = document.querySelectorAll('form');
                let addToCartForms = [];
                
                forms.forEach(form => {
                    const actionInput = form.querySelector('input[name="action"][value="add_to_cart"]');
                    if (actionInput) {
                        addToCartForms.push(form);
                    }
                });
                
                if (addToCartForms.length > 0) {
                    // Submit the first form (others will be handled on page reload)
                    addToCartForms[0].submit();
                } else {
                    alert('No items to add to cart.');
                }
            }
        }
    </script>

</body>
</html>