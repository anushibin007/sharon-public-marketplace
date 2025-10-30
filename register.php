<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; // buyer or seller
    $phone = $conn->real_escape_string($_POST['phone']);

    $sql = "INSERT INTO users (username, password, role, phone) VALUES ('$username', '$password', '$role', '$phone')";

    if ($conn->query($sql) === TRUE) {
        $success_message = "Registration successful!";
    } else {
        $error_message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Online Marketplace</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        .register-header {
            margin-bottom: 30px;
        }
        .register-header h1 {
            color: #333;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .register-header p {
            color: #666;
            margin: 0;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .success-message {
            background-color: #e8f5e8;
            color: #2e7d32;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #4caf50;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #c62828;
        }
        .login-link {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
        }
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .role-info {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin-top: 5px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Join Our Marketplace</h1>
            <p>Create your account to start buying or selling</p>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message">
                <?php echo $success_message; ?>
                <br><a href="login.php" style="color: #2e7d32; font-weight: bold;">Login here</a>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            
            <div class="form-group">
                <label for="role">Account Type</label>
                <select id="role" name="role" required>
                    <option value="">Select your role</option>
                    <option value="buyer">Buyer - I want to purchase products</option>
                    <option value="seller">Seller - I want to sell products</option>
                </select>
                <div class="role-info">
                    Choose "Buyer" to shop for products, or "Seller" to list and sell your own products.
                </div>
            </div>
            
            <button type="submit" class="btn">Create Account</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Sign in here</a>
        </div>
    </div>
</body>
</html>