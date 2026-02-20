# MotoCity - Motorbike Rental System

A dynamic web application for renting motorbikes built with Object-Oriented PHP and MySQL.

## 📋 Project Overview

MotoCity is a motorbike rental management system that allows users to rent motorbikes from different locations in the city. The system supports two types of users: **Administrators** and **Users**, each with specific functionalities.

## ✨ Features

### For Users:
- ✅ Register and login to the system
- ✅ List all available motorbikes
- ✅ Search for motorbikes (by code, location, description)
- ✅ Rent available motorbikes with start time and cost notification
- ✅ Return rented motorbikes with total cost calculation
- ✅ View rental history (completed rentals)
- ✅ View currently active rentals

### For Administrators:
- ✅ Register and login to the system
- ✅ Add and edit motorbikes
- ✅ Rent motorbikes for users
- ✅ Return motorbikes for users
- ✅ List all motorbikes (all, available, or currently rented)
- ✅ Search motorbikes by multiple criteria
- ✅ List all users
- ✅ Search users by name, surname, phone, or email
- ✅ List users currently renting motorbikes

## 🏗️ Technical Architecture

### Object-Oriented PHP Classes:
- **Database** - Singleton pattern for database connection
- **User** - User management (registration, login, search)
- **Motorbike** - Motorbike CRUD operations
- **Rental** - Rental operations (create, return, cost calculation)
- **Auth** - Authentication and authorization helper

### Database Tables:
- **users** - ID, Name, Surname, Phone, Email, Type, Password
- **motorbikes** - Code, RentingLocation, Description, CostPerHour
- **rentals** - RentalId, UserId, MotorbikeCode, StartDateTime, EndDateTime, CostPerHourAtStart, Status

## 🚀 Setup Instructions

### 1. Start XAMPP
- Start Apache and MySQL services

### 2. Create Database
Visit: `http://localhost/ISIT307-A2/create_database.php`

This will:
- Create the `motocity` database
- Create all required tables
- Insert sample data (users, motorbikes, rentals)

### 3. Access the Application
URL: `http://localhost/ISIT307-A2/`

## 🔐 Login Credentials

### Administrator:
- **Email:** `admin@motocity.com`
- **Password:** `password123`

### Sample Users:
- **Email:** `weiming.lim@example.com` | **Password:** `password123`
- **Email:** `meiling.tan@example.com` | **Password:** `password123`
- **Email:** `raj.kumar@example.com` | **Password:** `password123`

## 📁 Project Structure

```
ISIT307-A2/
├── classes/                    # OOP Classes
│   ├── Database.php            # Database connection (Singleton pattern)
│   ├── User.php                # User management and authentication
│   ├── Motorbike.php           # Motorbike CRUD operations
│   ├── Rental.php              # Rental operations and cost calculation
│   └── Auth.php                # Authentication and authorization helper
├── includes/                   # Reusable components
│   ├── header.php              # Page header with branding
│   ├── nav.php                 # Dynamic navigation menu (role-based)
│   └── footer.php              # Page footer
├── assets/                     # Static resources
│   └── css/
│       └── style.css           # Application styles and responsive design
├── index.php                   # Entry point (redirects to login)
├── login.php                   # User authentication page
├── register.php                # New user registration
├── dashboard.php               # Main dashboard (role-based content)
├── logout.php                  # Session termination
├── motorbikes_list.php         # List/search motorbikes (with filters for admin)
├── motorbike_form.php          # Add/edit/delete motorbikes (admin only)
├── rent.php                    # Rent motorbikes (with user selection for admin)
├── return.php                  # Return motorbikes and view active rentals
├── rentals_history.php         # View completed rentals (user only)
├── users_list.php              # List/search users (admin only)
├── create_database.php         # Automated database setup script
├── motocity.sql                # SQL database dump for manual import
├── README.md                   # Project documentation
├── PROJECT_REPORT.md           # Comprehensive project report with user manual
└── .gitignore                  # Git ignore rules
```

## 💡 Key Features

### Cost Calculation
- Accurate calculation including hours, minutes, and seconds
- Minimum charge: 1 hour (even for shorter rentals)
- Real-time cost display on return

### Search Functionality
- Partial search terms supported
- Search by multiple fields simultaneously
- Case-insensitive search

### Security
- Password hashing with `password_hash()`
- Session-based authentication
- Role-based access control (Admin/User)
- SQL injection prevention with prepared statements

### User Experience
- Clean, modern UI with consistent styling
- Responsive design
- Clear navigation based on user role
- Success/error message notifications
- Singapore phone number format (+65)

## 🛠️ Technologies Used

- **Backend:** PHP 7.4+ (Object-Oriented)
- **Database:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3
- **Server:** Apache (XAMPP)
- **Architecture:** MVC-inspired with OOP classes

## 📝 Database Configuration

Default settings (in `classes/Database.php`):
```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "motocity";
```

## 🎯 Assignment Requirements Met

✅ Object-Oriented PHP with classes  
✅ MySQL Database with proper schema  
✅ User registration and login (both types)  
✅ Motorbike management (CRUD operations)  
✅ Rental operations (rent/return)  
✅ Search functionality (motorbikes and users)  
✅ Cost calculation with notifications  
✅ Rental history tracking  
✅ Role-based access control  
✅ Input validation  
✅ Good UI/UX design  

## 📧 Contact

For questions or issues, please contact the development team.

## 📄 License

This project is developed for educational purposes as part of ISIT307 assignment.
