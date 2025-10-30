<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header('Location: login.php');
    exit();
}

$message = "";
$message_type = "";

if (isset($_GET['product_id'])) {
    $buyer_id = $_SESSION['user_id'];
    $product_id = intval($_GET['product_id']);

    // Check if product already in wishlist
    $sql = "SELECT * FROM wishlist WHERE buyer_id = $buyer_id AND product_id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $message = "This product is already in your wishlist!";
        $message_type = "info";
    } else {
        // Add to wishlist
        $sql = "INSERT INTO wishlist (buyer_id, product_id) VALUES ($buyer_id, $product_id)";
        if ($conn->query($sql) === TRUE) {
            $message = "Product added to your wishlist successfully!";
            $message_type = "success";
        } else {
            $message = "Error adding to wishlist: " . $conn->error;
            $message_type = "error";
        }
    }
} else {
    $message = "Invalid product selected.";
    $message_type = "error";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add to Wishlist - Online Marketplace</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #f5f7fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .message-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
            margin: 20px;
        }
        .message-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        .success-icon {
            color: #4caf50;
        }
        .error-icon {
            color: #f44336;
        }
        .info-icon {
            color: #ff9800;
        }
        .message-title {
            font-size: 24px;
            margin: 0 0 15px 0;
            color: #333;
        }
        .message-text {
            font-size: 16px;
            color: #666;
            margin: 0 0 30px 0;
            line-height: 1.5;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: transform 0.2s;
            display: inline-block;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-wishlist {
            background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%);
            color: white;
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #adb5bd 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="message-container">
        <div class="message-icon <?php 
            echo $message_type == 'success' ? 'success-icon' : 
                ($message_type == 'info' ? 'info-icon' : 'error-icon'); 
        ?>">
            <?php 
                echo $message_type == 'success' ? '💝' : 
                    ($message_type == 'info' ? '⚠️' : '❌'); 
            ?>
        </div>
        
        <h1 class="message-title">
            <?php 
                echo $message_type == 'success' ? 'Added to Wishlist!' : 
                    ($message_type == 'info' ? 'Already in Wishlist' : 'Oops!'); 
            ?>
        </h1>
        
        <p class="message-text"><?php echo $message; ?></p>
        
        <div class="action-buttons">
            <?php if ($message_type == 'success' || $message_type == 'info'): ?>
                <a href="wishlist.php" class="btn btn-wishlist">💝 View Wishlist</a>
                <a href="products.php" class="btn btn-primary">🛍️ Continue Shopping</a>
            <?php else: ?>
                <a href="products.php" class="btn btn-primary">🛍️ Browse Products</a>
            <?php endif; ?>
            <a href="dashboard.php" class="btn btn-secondary">🏠 Dashboard</a>
        </div>
    </div>
</body>
</html>