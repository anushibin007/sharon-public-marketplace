<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $desc = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    
    $seller_id = $_SESSION['user_id'];
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_data = file_get_contents($_FILES['image']['tmp_name']);
        $image_type = $_FILES['image']['type'];
        
        // Validate image type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($image_type, $allowed_types)) {
            $error_message = "Only JPEG, PNG, GIF, and WebP images are allowed.";
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $error_message = "Image size must be less than 5MB.";
        } else {
            // Prepare statement to handle BLOB data
            $stmt = $conn->prepare("INSERT INTO products (seller_id, name, description, price, image, image_type) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issdss", $seller_id, $name, $desc, $price, $image_data, $image_type);
            
            if ($stmt->execute()) {
                $success_message = "Product added successfully with image!";
            } else {
                $error_message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        // No image uploaded
        $sql = "INSERT INTO products (seller_id, name, description, price) VALUES ($seller_id, '$name', '$desc', $price)";
        
        if ($conn->query($sql) === TRUE) {
            $success_message = "Product added successfully without image!";
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product - Online Marketplace</title>
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
            max-width: 600px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-header h2 {
            color: #333;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .form-header p {
            color: #666;
            margin: 0;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 14px;
        }
        .file-upload-area {
            border: 2px dashed #ddd;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            transition: border-color 0.3s;
            background-color: #fafafa;
        }
        .file-upload-area:hover {
            border-color: #667eea;
            background-color: #f0f2ff;
        }
        .file-upload-area input[type="file"] {
            margin-top: 10px;
        }
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s;
            font-weight: bold;
        }
        .btn:hover {
            transform: translateY(-2px);
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
        .upload-icon {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Add New Product</h1>
    </div>

    <div class="navigation">
        <a href="dashboard.php">← Back to Dashboard</a>
        <a href="products.php">Browse Products</a>
        <a href="my_products.php">My Products</a>
    </div>

    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h2>List Your Product</h2>
                <p>Fill in the details below to add your product to the marketplace</p>
            </div>
            
            <?php if (isset($success_message)): ?>
                <div class="success-message">
                    <?php echo $success_message; ?>
                    <br><br>
                    <a href="dashboard.php" style="color: #2e7d32; font-weight: bold;">Back to Dashboard</a> |
                    <a href="add_product.php" style="color: #2e7d32; font-weight: bold;">Add Another Product</a>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" required placeholder="Enter a descriptive product name">
                </div>
                
                <div class="form-group">
                    <label for="description">Product Description *</label>
                    <textarea id="description" name="description" required placeholder="Describe your product in detail..."></textarea>
                    <small>Provide detailed information about features, condition, dimensions, etc.</small>
                </div>
                
                <div class="form-group">
                    <label for="price">Price (₹) *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required placeholder="0.00">
                    <small>Enter the selling price in Indian Rupees</small>
                </div>
                
                <div class="form-group">
                    <label>Product Image</label>
                    <div class="file-upload-area">
                        <div class="upload-icon">📷</div>
                        <p><strong>Upload Product Image</strong></p>
                        <input type="file" name="image" accept="image/*">
                        <small>Optional. Supported formats: JPEG, PNG, GIF, WebP. Max size: 5MB</small>
                    </div>
                </div>
                
                <button type="submit" class="btn">Add Product to Marketplace</button>
            </form>
        </div>
    </div>
</body>
</html>