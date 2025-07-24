# Food Delivery System

A web-based food delivery system built with PHP, MySQL, and JavaScript.

## Features

- User registration and authentication
- Browse menu items by category
- Add items to cart
- Place orders
- View order history
- Responsive design

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

## Installation

1. Clone the repository to your web server's document root:
   ```bash
   git clone https://github.com/yourusername/fooddeliverysystem.git
   ```

2. Create a MySQL database and import the schema:
   ```bash
   mysql -u your_username -p your_database < database.sql
   ```

3. Configure the database connection:
   - Open `config/database.php`
   - Update the database credentials:
     ```php
     private $host = "localhost";
     private $db_name = "food_delivery";
     private $username = "your_username";
     private $password = "your_password";
     ```

4. Set up the web server:
   - For Apache, ensure mod_rewrite is enabled
   - Point the document root to the project directory
   - Ensure the web server has write permissions for the `images` directory

5. Access the application:
   - Open your web browser
   - Navigate to `http://localhost/fooddeliverysystem`

## Directory Structure

```
fooddeliverysystem/
├── api/                    # API endpoints
│   ├── dishes.php
│   ├── login.php
│   ├── orders.php
│   └── register.php
├── config/                 # Configuration files
│   └── database.php
├── images/                 # Food item images
├── css/                    # Stylesheets
│   └── styles.css
├── js/                     # JavaScript files
│   └── script.js
├── index.html             # Main page
├── login.html             # Login page
├── register.html          # Registration page
├── database.sql           # Database schema
└── README.md              # This file
```

## API Endpoints

### Authentication
- `POST /api/register.php` - Register a new user
- `POST /api/login.php` - User login

### Menu
- `GET /api/dishes.php` - Get all dishes

### Orders
- `POST /api/orders.php` - Create a new order

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 