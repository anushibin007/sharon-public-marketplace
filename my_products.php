<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header('Location: login.php');
    exit();
}

$seller_id = $_SESSION['user_id'];

// Handle product deletion
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    
    // Verify the product belongs to this seller
    $check_sql = "SELECT id FROM products WHERE id = $product_id AND seller_id = $seller_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $delete_sql = "DELETE FROM products WHERE id = $product_id AND seller_id = $seller_id";
        if ($conn->query($delete_sql)) {
            $success_message = "Product deleted successfully!";
        } else {
            $error_message = "Error deleting product: " . $conn->error;
        }
    } else {
        $error_message = "Product not found or you don't have permission to delete it.";
    }
}

// Get seller's products
$sql = "SELECT * FROM products WHERE seller_id = $seller_id ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Products - Online Marketplace</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #f5f7fa;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .navigation {
            margin: 20px 0;
            text-align: center;
        }
        .navigation a {
            color: #667eea;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }
        .navigation a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .products-header {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        .products-header h2 {
            color: #333;
            margin: 0 0 15px 0;
        }
        .add-product-btn {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: transform 0.2s;
        }
        .add-product-btn:hover {
            transform: translateY(-2px);
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        .product-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .product-image {
            width: 100%;
            height: 200px;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .product-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .product-content {
            padding: 20px;
        }
        .product-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin: 0 0 10px 0;
            line-height: 1.4;
        }
        .product-description {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
            margin: 0 0 15px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .product-price {
            font-size: 20px;
            font-weight: bold;
            color: #2c5aa0;
            margin: 0 0 15px 0;
        }
        .product-meta {
            color: #666;
            font-size: 12px;
            margin: 0 0 20px 0;
        }
        .product-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            flex: 1;
            padding: 10px;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-1px);
        }
        .btn-edit {
            background: linear-gradient(135deg, #2196f3 0%, #42a5f5 100%);
            color: white;
        }
        .btn-delete {
            background: linear-gradient(135deg, #f44336 0%, #ef5350 100%);
            color: white;
        }
        .no-products {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .no-products h3 {
            margin: 0 0 15px 0;
            color: #999;
            font-size: 24px;
        }
        .success-message {
            background-color: #e8f5e8;
            color: #2e7d32;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            border-left: 4px solid #4caf50;
            text-align: center;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            border-left: 4px solid #c62828;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>My Products</h1>
    </div>

    <div class="navigation">
        <a href="dashboard.php">← Back to Dashboard</a>
        <a href="add_product.php">Add New Product</a>
        <a href="products.php">Browse All Products</a>
    </div>

    <div class="container">
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="products-header">
            <h2>Manage Your Product Catalog</h2>
            <p>View, edit, and manage all your listed products</p>
            <a href="add_product.php" class="add-product-btn">+ Add New Product</a>
        </div>

        <?php if ($result->num_rows == 0): ?>
            <div class="no-products">
                <h3>📦 No products yet</h3>
                <p>You haven't listed any products yet. Start by adding your first product to the marketplace!</p>
                <br>
                <a href="add_product.php" class="add-product-btn">Add Your First Product</a>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="image.php?id=<?php echo $row['id']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        </div>
                        <div class="product-content">
                            <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($row['description']); ?></p>
                            <div class="product-price">₹<?php echo number_format($row['price'], 2); ?></div>
                            <div class="product-meta">
                                Listed on: <?php echo date('M j, Y', strtotime($row['created_at'])); ?>
                            </div>
                            
                            <div class="product-actions">
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">
                                    ✏️ Edit
                                </a>
                                <a href="?delete=1&id=<?php echo $row['id']; ?>" class="btn btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                    🗑️ Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Show confirmation for delete actions
        document.querySelectorAll('.btn-delete').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this product? This action cannot be undone and will remove it from all customer carts and wishlists.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>