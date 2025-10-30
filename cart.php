<?php
include 'config.php';

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header('Location: login.php');
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Handle quantity updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $cart_item_id = intval($_POST['cart_item_id']);
    
    if ($_POST['action'] === 'update') {
        $new_quantity = intval($_POST['quantity']);
        if ($new_quantity > 0) {
            $sql = "UPDATE cart SET quantity = $new_quantity WHERE id = $cart_item_id AND buyer_id = $buyer_id";
            $conn->query($sql);
        }
    } elseif ($_POST['action'] === 'remove') {
        $sql = "DELETE FROM cart WHERE id = $cart_item_id AND buyer_id = $buyer_id";
        $conn->query($sql);
    }
    
    // Redirect to avoid form resubmission
    header('Location: cart.php');
    exit();
}

// Get cart items with product details
$sql = "SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.description, p.price, u.username as seller_name 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        JOIN users u ON p.seller_id = u.id 
        WHERE c.buyer_id = $buyer_id 
        ORDER BY c.added_at DESC";

$result = $conn->query($sql);
$total_amount = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Cart - Online Marketplace</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .cart-item { border: 1px solid #ccc; padding: 15px; margin-bottom: 10px; border-radius: 5px; display: flex; gap: 15px; }
        .cart-item-image { flex-shrink: 0; }
        .cart-item-content { flex-grow: 1; }
        .cart-header { background-color: #f5f5f5; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        .quantity-input { width: 60px; padding: 5px; }
        .price { font-weight: bold; color: #2c5aa0; }
        .total { font-size: 18px; font-weight: bold; background-color: #e8f4fd; padding: 15px; border-radius: 5px; }
        .btn { padding: 8px 15px; margin: 5px; border: none; border-radius: 3px; cursor: pointer; }
        .btn-update { background-color: #4CAF50; color: white; }
        .btn-remove { background-color: #f44336; color: white; }
        .btn-primary { background-color: #2196F3; color: white; }
        .empty-cart { text-align: center; padding: 50px; color: #666; }
        .navigation { margin-bottom: 20px; }
        .navigation a { margin-right: 15px; text-decoration: none; color: #2196F3; }
    </style>
</head>
<body>
    <div class="navigation">
        <a href="dashboard.php">← Back to Dashboard</a>
        <a href="products.php">Continue Shopping</a>
    </div>

    <div class="cart-header">
        <h1>My Shopping Cart</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    </div>

    <?php if ($result->num_rows == 0): ?>
        <div class="empty-cart">
            <h2>Your cart is empty</h2>
            <p>Browse our products and add items to your cart.</p>
            <a href="products.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <?php while ($row = $result->fetch_assoc()): 
            $item_total = $row['price'] * $row['quantity'];
            $total_amount += $item_total;
        ?>
            <div class="cart-item">
                <div class="cart-item-image">
                    <img src='image.php?id=<?php echo $row['product_id']; ?>' width='100' height='100' style='object-fit: cover; border: 1px solid #ddd; border-radius: 5px;' alt='Product Image'>
                </div>
                <div class="cart-item-content">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <p><strong>Seller:</strong> <?php echo htmlspecialchars($row['seller_name']); ?></p>
                    <p class="price">Price: ₹<?php echo number_format($row['price'], 2); ?> each</p>
                    
                    <form method="POST" action="" style="display: inline-block;">
                        <input type="hidden" name="cart_item_id" value="<?php echo $row['cart_id']; ?>">
                        <label for="quantity_<?php echo $row['cart_id']; ?>">Quantity:</label>
                        <input type="number" id="quantity_<?php echo $row['cart_id']; ?>" name="quantity" 
                               value="<?php echo $row['quantity']; ?>" min="1" class="quantity-input">
                        <button type="submit" name="action" value="update" class="btn btn-update">Update</button>
                    </form>
                    
                    <form method="POST" action="" style="display: inline-block;">
                        <input type="hidden" name="cart_item_id" value="<?php echo $row['cart_id']; ?>">
                        <button type="submit" name="action" value="remove" class="btn btn-remove" 
                                onclick="return confirm('Are you sure you want to remove this item?')">Remove</button>
                    </form>
                    
                    <p class="price">Subtotal: ₹<?php echo number_format($item_total, 2); ?></p>
                </div>
            </div>
        <?php endwhile; ?>

        <div class="total">
            <h2>Total Amount: ₹<?php echo number_format($total_amount, 2); ?></h2>
            <p>Items in cart: <?php echo $result->num_rows; ?></p>
            
            <div style="margin-top: 15px;">
                <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                <button class="btn btn-primary" onclick="alert('Checkout functionality coming soon!')">
                    Proceed to Checkout
                </button>
            </div>
        </div>
    <?php endif; ?>

</body>
</html>