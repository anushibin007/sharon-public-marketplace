<?php
include 'config.php';

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    
    $sql = "SELECT image, image_type FROM products WHERE id = $product_id AND image IS NOT NULL";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Set the appropriate content type
        header('Content-Type: ' . $row['image_type']);
        header('Content-Length: ' . strlen($row['image']));
        header('Cache-Control: public, max-age=86400'); // Cache for 1 day
        
        // Output the image data
        echo $row['image'];
    } else {
        // Return a default "no image" placeholder
        header('Content-Type: image/svg+xml');
        echo '<svg width="150" height="150" xmlns="http://www.w3.org/2000/svg">
                <rect width="150" height="150" fill="#f0f0f0" stroke="#ccc"/>
                <text x="75" y="75" text-anchor="middle" dy=".3em" fill="#666" font-family="Arial" font-size="12">No Image</text>
              </svg>';
    }
} else {
    header('HTTP/1.0 404 Not Found');
    echo 'Image not found';
}
?>