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
            echo "Error: Only JPEG, PNG, GIF, and WebP images are allowed.";
            exit();
        }
        
        // Validate image size (max 5MB)
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            echo "Error: Image size must be less than 5MB.";
            exit();
        }
        
        // Prepare statement to handle BLOB data
        $stmt = $conn->prepare("INSERT INTO products (seller_id, name, description, price, image, image_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdss", $seller_id, $name, $desc, $price, $image_data, $image_type);
        
        if ($stmt->execute()) {
            echo "Product added successfully with image. <a href='dashboard.php'>Back to Dashboard</a>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // No image uploaded
        $sql = "INSERT INTO products (seller_id, name, description, price) VALUES ($seller_id, '$name', '$desc', $price)";
        
        if ($conn->query($sql) === TRUE) {
            echo "Product added successfully without image. <a href='dashboard.php'>Back to Dashboard</a>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>
<form method="POST" action="" enctype="multipart/form-data">
    Product Name: <input type="text" name="name" required><br><br>
    Description: <textarea name="description" rows="4" cols="50" required></textarea><br><br>
    Price: <input type="number" step="0.01" name="price" required><br><br>
    Product Image: <input type="file" name="image" accept="image/*"><br>
    <small>Optional. Supported formats: JPEG, PNG, GIF, WebP. Max size: 5MB</small><br><br>
    <button type="submit">Add Product</button>
</form>