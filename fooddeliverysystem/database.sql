-- Create database
DROP DATABASE IF EXISTS food_delivery;
CREATE DATABASE food_delivery;
USE food_delivery;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dishes table
CREATE TABLE dishes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category ENUM('South Indian', 'North Indian', 'Chinese', 'Korean') NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_available BOOLEAN DEFAULT TRUE
);

-- Orders table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cod', 'online') NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    dish_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (dish_id) REFERENCES dishes(id)
);

-- Insert sample dishes
INSERT INTO dishes (name, description, price, category, image_path) VALUES
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
('Ramen', 'Japanese-style noodle soup with rich broth and toppings', 220.00, 'Korean', 'images/ramen.jfif'); 