<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Online Marketplace</title>
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
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .welcome-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        .welcome-section h2 {
            color: #333;
            margin: 0 0 10px 0;
        }
        .user-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 15px;
        }
        .role-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin-left: 10px;
        }
        .role-seller {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .role-buyer {
            background-color: #e8f5e8;
            color: #388e3c;
        }
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .action-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 2px solid transparent;
        }
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .action-card.seller-card {
            border-color: #e3f2fd;
        }
        .action-card.buyer-card {
            border-color: #e8f5e8;
        }
        .action-card h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 20px;
        }
        .action-card p {
            color: #666;
            margin: 0 0 20px 0;
            line-height: 1.5;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn-seller {
            background: linear-gradient(135deg, #1976d2 0%, #42a5f5 100%);
        }
        .btn-buyer {
            background: linear-gradient(135deg, #388e3c 0%, #66bb6a 100%);
        }
        .logout-section {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #ddd;
        }
        .btn-logout {
            background: linear-gradient(135deg, #d32f2f 0%, #f44336 100%);
            padding: 10px 20px;
            font-size: 14px;
        }
        .icon {
            font-size: 40px;
            margin-bottom: 15px;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Online Marketplace</h1>
        <p>Your one-stop shop for everything</p>
    </div>

    <div class="container">
        <div class="welcome-section">
            <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <div class="user-info">
                <strong>Account Type:</strong>
                <span class="role-badge role-<?php echo $_SESSION['role']; ?>">
                    <?php echo ucfirst($_SESSION['role']); ?>
                </span>
            </div>
        </div>

        <div class="actions-grid">
            <?php if ($_SESSION['role'] == 'seller'): ?>
                <div class="action-card seller-card">
                    <div class="icon">📦</div>
                    <h3>Add New Product</h3>
                    <p>List a new product in the marketplace. Add images, descriptions, and pricing to attract buyers.</p>
                    <a href="add_product.php" class="btn btn-seller">Add Product</a>
                </div>
                
                <div class="action-card">
                    <div class="icon">👥</div>
                    <h3>Browse Marketplace</h3>
                    <p>Explore what other sellers are offering. Get inspiration for your own products.</p>
                    <a href="products.php" class="btn">Browse Products</a>
                </div>
                
            <?php else: ?>
                <div class="action-card buyer-card">
                    <div class="icon">🛍️</div>
                    <h3>Browse Products</h3>
                    <p>Discover amazing products from various sellers. Use search to find exactly what you need.</p>
                    <a href="products.php" class="btn btn-buyer">Start Shopping</a>
                </div>
                
                <div class="action-card buyer-card">
                    <div class="icon">🛒</div>
                    <h3>My Cart</h3>
                    <p>Review items in your shopping cart. Update quantities or proceed to checkout.</p>
                    <a href="cart.php" class="btn btn-buyer">View Cart</a>
                </div>
                
                <div class="action-card buyer-card">
                    <div class="icon">💝</div>
                    <h3>My Wishlist</h3>
                    <p>View your saved items. Move them to cart when you're ready to purchase.</p>
                    <a href="wishlist.php" class="btn btn-buyer">View Wishlist</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="logout-section">
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </div>
    </div>
</body>
</html>