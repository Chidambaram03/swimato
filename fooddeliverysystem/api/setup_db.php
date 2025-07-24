<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS food_delivery");
    $pdo->exec("USE food_delivery");
    
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        address TEXT NOT NULL,
        phone VARCHAR(15) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create dishes table
    $pdo->exec("CREATE TABLE IF NOT EXISTS dishes (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        category ENUM('South Indian', 'North Indian', 'Chinese', 'Korean') NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        is_available BOOLEAN DEFAULT TRUE
    )");
    
    // Clear existing dishes to avoid duplicates
    $pdo->exec("TRUNCATE TABLE dishes");
    
    // Create orders table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        payment_method ENUM('cod', 'online') NOT NULL,
        status ENUM('pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    
    // Create order_items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        dish_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (dish_id) REFERENCES dishes(id)
    )");
    
    // Insert sample dishes
    $pdo->exec("INSERT INTO dishes (name, description, price, category, image_path) VALUES
        ('Idli', 'Steamed rice cakes served with sambar and chutney', 50.00, 'South Indian', 'images/idly.jfif'),
        ('Dosa', 'Crispy crepe made from rice and lentil batter', 80.00, 'South Indian', 'images/dosa.jfif'),
        ('Vada', 'Crispy fried lentil donuts', 40.00, 'South Indian', 'images/vada.jfif'),
        ('Paneer Butter Masala', 'Cottage cheese in rich tomato gravy', 200.00, 'North Indian', 'images/paneer.jfif'),
        ('Biryani', 'Fragrant rice dish with spices and meat/vegetables', 180.00, 'North Indian', 'images/biriyani.jfif'),
        ('Rajma Chawal', 'Red kidney beans curry with rice', 120.00, 'North Indian', 'images/rajma.jfif'),
        ('Noodles', 'Stir-fried noodles with vegetables', 100.00, 'Chinese', 'images/noodles.jfif'),
        ('Manchurian', 'Vegetable dumplings in spicy sauce', 150.00, 'Chinese', 'images/manchurian.jfif'),
        ('Fried Rice', 'Stir-fried rice with vegetables', 120.00, 'Chinese', 'images/friedrice.jfif'),
        ('Bibimbap', 'Korean rice bowl with vegetables, meat, and gochujang sauce', 250.00, 'Korean', 'images/Bibimbap.jfif'),
        ('Kimchi Fried Rice', 'Fried rice with fermented kimchi and vegetables', 180.00, 'Korean', 'images/kimchi.jfif'),
        ('Bulgogi', 'Marinated beef slices grilled to perfection', 300.00, 'Korean', 'images/bulgogi.jfif'),
        ('Ramen', 'Japanese-style noodle soup with rich broth and toppings', 220.00, 'Korean', 'images/ramen.jfif')
    ");
    
    echo json_encode(array(
        "status" => "success",
        "message" => "Database and tables created successfully"
    ));
    
} catch (PDOException $e) {
    http_response_code(503);
    echo json_encode(array(
        "status" => "error",
        "message" => "Database error",
        "error" => $e->getMessage()
    ));
}
?> 