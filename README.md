# VietNghe Keychain E-commerce Website

A PHP-based e-commerce website specialized in selling unique and customizable keychains. This project implements a full-featured online store with shopping cart functionality, user authentication, and product management.

## 🚀 Features

- User Authentication (Login/Register)
- Product
- Shopping Cart Management
- Stock Management
- Responsive Design
- Admin Dashboard
- Order Processing

## 🛠️ Technologies Used

- PHP (Backend)
- MySQL (Database)
- JavaScript/jQuery (Frontend Interactivity)
- HTML5/CSS3
- Apache Web Server

## 📋 Prerequisites

- XAMPP (or similar PHP development environment)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Web Server
- Web Browser (Chrome, Firefox, etc.)

## 💻 Installation

1. Clone the repository to your XAMPP's htdocs folder:
```bash
cd /xampp/htdocs
git clone https://github.com/lynamkhang/VietNghe-Keychain-E-commerce-Website.git
```

2. Import the database:
- Open phpMyAdmin
- Create a new database named `vietnghe_keychain`
- Import the `vietnghe_keychain.sql` file

3. Configure the database connection:
- Navigate to `config/database.php`
- Update the database credentials if necessary

4. Access the website:
```
http://localhost/vietnghe-keychain
```

## 📁 Project Structure

```
vietnghe-keychain/
├── config/           # Configuration files
├── controllers/      # Application controllers
├── models/          # Database models
├── views/           # View templates
├── public/          # Public assets
│   ├── css/         # Stylesheets
│   ├── js/          # JavaScript files
│   ├── images/      # Image assets
│   └── uploads/     # User uploads
├── index.php        # Application entry point
└── .htaccess       # Apache configuration
```

## 🔒 Security Features

- Password Hashing
- SQL Injection Prevention
- XSS Protection
- CSRF Protection
- Input Validation

## 🌟 Usage

### Customer Features
- Browse products by category
- Add items to cart
- Manage cart quantities
- Place orders
- View order history

### Admin Features
- Manage products
- Process orders
- Manage categories
- View sales reports
- Manage user accounts






