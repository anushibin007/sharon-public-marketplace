<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header('Location: login.php');
    exit();
}

$seller_id = $_SESSION['user_id'];
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verify the product belongs to this seller
$check_sql = "SELECT * FROM products WHERE id = $product_id AND seller_id = $seller_id";
$product_result = $conn->query($check_sql);

if ($product_result->num_rows == 0) {
    header('Location: my_products.php');
    exit();
}

$product = $product_result->fetch_assoc();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    
    if (empty($name) || empty($description) || $price <= 0) {
        $error_message = "Please fill in all fields with valid values.";
    } else {
        // Check if a new image was uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['image']['type'];
            
            if (!in_array($file_type, $allowed_types)) {
                $error_message = "Invalid image type. Please upload a JPEG, PNG, GIF, or WebP image.";
            } else {
                $image_data = file_get_contents($_FILES['image']['tmp_name']);
                
                // Update with new image
                $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ?, image_type = ? WHERE id = ? AND seller_id = ?");
                $stmt->bind_param("ssdssii", $name, $description, $price, $image_data, $file_type, $product_id, $seller_id);
                
                if ($stmt->execute()) {
                    $success_message = "Product updated successfully with new image!";
                    // Refresh product data
                    $product_result = $conn->query($check_sql);
                    $product = $product_result->fetch_assoc();
                } else {
                    $error_message = "Error updating product: " . $conn->error;
                }
                $stmt->close();
            }
        } else {
            // Update without changing image
            $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ? WHERE id = ? AND seller_id = ?");
            $stmt->bind_param("ssdii", $name, $description, $price, $product_id, $seller_id);
            
            if ($stmt->execute()) {
                $success_message = "Product updated successfully!";
                // Refresh product data
                $product_result = $conn->query($check_sql);
                $product = $product_result->fetch_assoc();
            } else {
                $error_message = "Error updating product: " . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product - Online Marketplace</title>
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
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .form-title {
            color: #333;
            margin: 0 0 25px 0;
            text-align: center;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        .file-input {
            width: 100%;
            padding: 12px;
            border: 2px dashed #e0e0e0;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            background-color: #fafafa;
            transition: all 0.3s;
        }
        .file-input:hover {
            border-color: #667eea;
            background-color: #f0f2ff;
        }
        .current-image {
            margin: 15px 0;
            text-align: center;
        }
        .current-image img {
            max-width: 200px;
            max-height: 200px;
            object-fit: contain;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .current-image-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .submit-btn:hover {
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
        .help-text {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .cancel-btn {
            flex: 1;
            padding: 15px;
            background: #f5f5f5;
            color: #666;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.2s;
        }
        .cancel-btn:hover {
            background: #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Edit Product</h1>
    </div>

    <div class="navigation">
        <a href="my_products.php">← Back to My Products</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="add_product.php">Add New Product</a>
    </div>

    <div class="container">
        <div class="form-container">
            <h2 class="form-title">✏️ Edit Product Details</h2>
            
            <?php if ($success_message): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Product Description *</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                    <div class="help-text">Provide a detailed description of your product</div>
                </div>

                <div class="form-group">
                    <label for="price">Price (₹) *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
                    <div class="help-text">Enter the price in Indian Rupees</div>
                </div>

                <div class="form-group">
                    <label>Current Product Image</label>
                    <div class="current-image">
                        <div class="current-image-label">Current image:</div>
                        <img src="image.php?id=<?php echo $product['id']; ?>" alt="Current product image">
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Update Product Image (Optional)</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="image" name="image" accept="image/*" class="file-input">
                    </div>
                    <div class="help-text">Leave empty to keep the current image. Supported formats: JPEG, PNG, GIF, WebP</div>
                </div>

                <div class="form-actions">
                    <a href="my_products.php" class="cancel-btn">Cancel</a>
                    <button type="submit" class="submit-btn">💾 Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // File input styling
        document.getElementById('image').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Choose image file...';
            const fileInput = e.target;
            
            if (e.target.files[0]) {
                fileInput.style.backgroundColor = '#e8f5e8';
                fileInput.style.borderColor = '#4caf50';
            } else {
                fileInput.style.backgroundColor = '#fafafa';
                fileInput.style.borderColor = '#e0e0e0';
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const description = document.getElementById('description').value.trim();
            const price = parseFloat(document.getElementById('price').value);

            if (!name || !description || price <= 0) {
                e.preventDefault();
                alert('Please fill in all required fields with valid values.');
                return false;
            }
        });
    </script>
</body>
</html>