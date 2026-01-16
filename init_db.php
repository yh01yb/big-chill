<?php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>DBG - Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        .success { color: green; }
        .error { color: red; }
        .box { border: 1px solid #ccc; padding: 20px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>DBG - Database Setup</h1>
    <?php
    
    $DB_HOST = 'localhost';
    $DB_USER = 'root';
    $DB_PASS = '';
    $DB_NAME = 'dbg_laundry';
    
    
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
    
    
    if ($conn->connect_error) {
        die("<div class='error'>Connection failed: " . $conn->connect_error . "</div>");
    }
    
    echo "<div class='box'>✓ Connected to MySQL server successfully</div>";
    
   
    $sql = "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='box'>✓ Database '$DB_NAME' created successfully</div>";
    } else {
        echo "<div class='error'>Error creating database: " . $conn->error . "</div>";
    }
    
    
    $conn->select_db($DB_NAME);
    
    
    $tables = [
        "users" => "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            full_name VARCHAR(200) NOT NULL,
            phone VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "customers" => "CREATE TABLE IF NOT EXISTS customers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            phone VARCHAR(30),
            address TEXT,
            email VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "orders" => "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('Pending','Washing','Ironing','Ready','Delivered','Cancelled') DEFAULT 'Pending',
            total_amount DECIMAL(10,2) DEFAULT 0,
            special_instructions TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
        )",
        
        "order_items" => "CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            cloth_type VARCHAR(150) NOT NULL,
            service_type VARCHAR(100) NOT NULL,
            quantity INT DEFAULT 1,
            price DECIMAL(10,2) DEFAULT 0,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        )",
        
        "payments" => "CREATE TABLE IF NOT EXISTS payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            payment_mode ENUM('Cash','Card','UPI','Net Banking') DEFAULT 'Cash',
            payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('Pending','Completed','Failed') DEFAULT 'Pending',
            transaction_id VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        )",
        
        "cloth_types" => "CREATE TABLE IF NOT EXISTS cloth_types (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cloth_name VARCHAR(150) NOT NULL UNIQUE,
            description TEXT,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "services" => "CREATE TABLE IF NOT EXISTS services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            service_name VARCHAR(100) NOT NULL UNIQUE,
            description TEXT,
            base_price DECIMAL(10,2) DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];
    
    
    foreach ($tables as $tableName => $sql) {
        if ($conn->query($sql) === TRUE) {
            echo "<div class='box'>✓ Table '$tableName' created successfully</div>";
        } else {
            echo "<div class='error'>Error creating table $tableName: " . $conn->error . "</div>";
        }
    }
    
    
    $defaultData = [
        "users" => "INSERT IGNORE INTO users (username, password, email, full_name, phone) VALUES 
                   ('admin', 'admin123', 'admin@dbg.com', 'System Administrator', '1234567890')",
        
        "cloth_types" => "INSERT IGNORE INTO cloth_types (cloth_name, description) VALUES 
                         ('Shirt', 'Formal and casual shirts'),
                         ('T-Shirt', 'Cotton t-shirts'),
                         ('Jeans', 'Denim pants'),
                         ('Trousers', 'Formal trousers'),
                         ('Traditionals', 'Traditional wear'),
                         ('Blanket', 'Single/Double bed'),
                         ('White Specific', 'Wash with Care'),
                         ('Bedsheet', 'Home'),
                         ('Window Curtains', 'Home')",
        
        "services" => "INSERT IGNORE INTO services (service_name, description, base_price) VALUES 
                      ('Wash', 'Basic washing', 20.00),
                      ('Iron', 'Ironing only', 15.00),
                      ('Wash+Iron', 'Wash and iron', 30.00),
                      ('Dry Clean', 'Dry cleaning', 50.00),
                      ('Stain Removal', 'Special treatment', 25.00)"
    ];
    
    
    foreach ($defaultData as $tableName => $sql) {
        if ($conn->query($sql) === TRUE) {
            $affected = $conn->affected_rows;
            echo "<div class='box'>✓ Default data inserted into '$tableName' ($affected rows)</div>";
        } else {
            echo "<div class='error'>Error inserting data into $tableName: " . $conn->error . "</div>";
        }
    }
    
   
    $indexes = [
        "CREATE INDEX idx_orders_customer_id ON orders(customer_id)",
        "CREATE INDEX idx_orders_status ON orders(status)",
        "CREATE INDEX idx_order_items_order_id ON order_items(order_id)",
        "CREATE INDEX idx_payments_order_id ON payments(order_id)"
    ];
    
    foreach ($indexes as $sql) {
        if ($conn->query($sql) === TRUE) {
            echo "<div class='box'>✓ Index created successfully</div>";
        }
    }
    
    $conn->close();
    ?>
    
    <div class=''>
        <h2></h2>
        <p></p>
        <p><strong></strong></p>
        <ol>
            <li> (init_db.php) for security</li>
            <li>Go to <a href="index.html">Login Page</a></li>
            <li>Use username: <strong>admin</strong>, password: <strong>admin123</strong></li>
        </ol>
    </div>
</body>
</html>