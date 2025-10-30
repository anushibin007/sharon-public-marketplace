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

$sql .= " ORDER BY products.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products - Online Marketplace</title>
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
            background: white;
            padding: 15px 0;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
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
        .search-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        .search-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
            gap: 10px;
        }
        .search-input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .search-input:focus {
            outline: none;
            border-color: #667eea;
        }
        .search-btn {
            padding: 12px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: transform 0.2s;
        }
        .search-btn:hover {
            transform: translateY(-2px);
        }
        .products-header {
            margin-bottom: 25px;
            text-align: center;
        }
        .products-header h2 {
            color: #333;
            margin: 0 0 10px 0;
        }
        .products-count {
            color: #666;
            font-size: 14px;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
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
            font-size: 24px;
            font-weight: bold;
            color: #2c5aa0;
            margin: 0 0 10px 0;
        }
        .product-seller {
            color: #666;
            font-size: 14px;
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
        .btn-cart {
            background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
            color: white;
        }
        .btn-wishlist {
            background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%);
            color: white;
        }
        .no-products {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .no-products h3 {
            margin: 0 0 10px 0;
            color: #999;
        }
        .search-highlight {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Product Marketplace</h1>
    </div>

    <div class="navigation">
        <a href="dashboard.php">← Dashboard</a>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'buyer'): ?>
            <a href="cart.php">My Cart</a>
            <a href="wishlist.php">My Wishlist</a>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="search-section">
            <h2 style="margin: 0 0 20px 0; color: #333;">Find Your Perfect Product</h2>
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" class="search-input" 
                       placeholder="Search for products..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="search-btn">🔍 Search</button>
            </form>
        </div>

        <?php if ($search): ?>
            <div class="search-highlight">
                <strong>Search Results for:</strong> "<?php echo htmlspecialchars($search); ?>"
                <a href="products.php" style="color: #667eea; margin-left: 15px; text-decoration: none;">× Clear Search</a>
            </div>
        <?php endif; ?>

        <div class="products-header">
            <h2><?php echo $search ? 'Search Results' : 'All Products'; ?></h2>
            <div class="products-count">
                <?php echo $result->num_rows; ?> product(s) found
            </div>
        </div>

        <?php if ($result->num_rows == 0): ?>
            <div class="no-products">
                <h3>No products found</h3>
                <p>
                    <?php if ($search): ?>
                        Try adjusting your search terms or <a href="products.php" style="color: #667eea;">browse all products</a>
                    <?php else: ?>
                        No products have been listed yet. Check back later!
                    <?php endif; ?>
                </p>
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
                            <div class="product-seller">
                                <strong>Seller:</strong> <?php echo htmlspecialchars($row['seller_name']); ?>
                            </div>
                            
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'buyer'): ?>
                                <div class="product-actions">
                                    <a href="add_to_cart.php?product_id=<?php echo $row['id']; ?>" class="btn btn-cart">
                                        🛒 Add to Cart
                                    </a>
                                    <a href="add_to_wishlist.php?product_id=<?php echo $row['id']; ?>" class="btn btn-wishlist">
                                        💝 Wishlist
                                    </a>
                                </div>
                            <?php else: ?>
                                <div style="text-align: center; padding: 10px; background-color: #f8f9fa; border-radius: 5px; color: #666;">
                                    <a href="login.php" style="color: #667eea; text-decoration: none; font-weight: bold;">Login to purchase</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>