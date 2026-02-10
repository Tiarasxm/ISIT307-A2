# MotoCity - Motorbike Rental Management System

A complete OOP PHP + MySQL web application for managing motorbike rentals with two user types: Administrator and User.

##Project Overview

MotoCity is a motorbike rental management system that allows users to browse, search, rent, and return motorbikes from different locations in the city. Administrators can manage the motorbike inventory, handle rentals for users, and view comprehensive reports.

## Features

### User Features
- Register and login to the system
- Browse all available motorbikes
- Search motorbikes by code, location, or description (partial match, combinable fields)
- Rent available motorbikes with start date/time selection
- Return rented motorbikes with automatic cost calculation
- View active rentals (currently renting)
- View rental history (completed rentals)
- Receive on-screen notifications for rental confirmations and return costs

### Administrator Features
- All user features plus:
- Insert and edit motorbikes (code, location, description, cost per hour)
- Rent motorbikes for specific users
- Return motorbikes for any user
- List all motorbikes / available motorbikes / currently rented motorbikes
- Search motorbikes with advanced filters
- List all users / users currently renting motorbikes
- Search users by name, surname, phone, or email
- View all active and completed rentals system-wide

## Project Structure

```
ISIT307-A2/
├── assets/
│   └── css/
│       └── style.css              # Application stylesheet
├── classes/
│   ├── Database.php               # PDO database connection (singleton)
│   ├── User.php                   # User management and authentication
│   ├── Motorbike.php              # Motorbike CRUD and search operations
│   ├── Rental.php                 # Rental transactions and cost calculation
│   └── Auth.php                   # Session management and access control
├── includes/
│   ├── config.php                 # Configuration and database credentials
│   ├── header.php                 # HTML header template
│   ├── nav.php                    # Navigation menu (role-based)
│   ├── footer.php                 # HTML footer template
│   └── validation.php             # Server-side validation functions
├── index.php                      # Landing/welcome page
├── register.php                   # User registration
├── login.php                      # User login
├── logout.php                     # User logout
├── dashboard.php                  # Role-based dashboard
├── motorbikes_list.php            # List motorbikes (filtered by role)
├── motorbike_search.php           # Search motorbikes
├── motorbike_form.php             # Add/edit motorbikes (admin only)
├── rent.php                       # Rent motorbike
├── return.php                     # Return motorbike
├── rentals_current.php            # View active rentals
├── rentals_history.php            # View completed rentals
├── users_list.php                 # List users (admin only)
├── user_search.php                # Search users (admin only)
├── schema.sql                     # Database schema with seed data
├── report_template.md             # Assignment report template
└── README.md                      # This file
```

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB)
- Web server (Apache/Nginx) or PHP built-in server
- XAMPP/MAMP/WAMP (optional, for easy local setup)

### Step 1: Database Setup

1. Start your MySQL server
2. Open phpMyAdmin or MySQL command line
3. Import the database schema:
   ```sql
   mysql -u root -p < schema.sql
   ```
   Or in phpMyAdmin: Import > Choose `schema.sql` > Go

4. The database `motocity` will be created with three tables:
   - `users` - User accounts
   - `motorbikes` - Motorbike inventory
   - `rentals` - Rental transactions

### Step 2: Configure Database Credentials

1. Open `includes/config.php`
2. Update the database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'motocity');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Update with your MySQL password
   ```

3. (Optional) Update the base URL if not using default:
   ```php
   define('BASE_URL', 'http://localhost/motocity');
   ```

### Step 3: Run the Application

#### Option A: Using XAMPP/MAMP/WAMP
1. Copy the `ISIT307-A2` folder to your web server's document root:
   - XAMPP: `C:\xampp\htdocs\ISIT307-A2` (Windows) or `/Applications/XAMPP/htdocs/ISIT307-A2` (Mac)
   - MAMP: `/Applications/MAMP/htdocs/ISIT307-A2`
   - WAMP: `C:\wamp64\www\ISIT307-A2`

2. Start Apache and MySQL from your control panel

3. Open your browser and navigate to:
   ```
   http://localhost/ISIT307-A2
   ```

#### Option B: Using PHP Built-in Server
1. Open terminal/command prompt
2. Navigate to the ISIT307-A2 directory:
   ```bash
   cd /path/to/ISIT307-A2
   ```

3. Start the PHP server:
   ```bash
   php -S localhost:8000
   ```

4. Open your browser and navigate to:
   ```
   http://localhost:8000
   ```

## Default Login Credentials

### Administrator Account
- **Email:** admin@motocity.com
- **Password:** password123

### User Accounts
- **Email:** john.smith@example.com | **Password:** password123
- **Email:** sarah.johnson@example.com | **Password:** password123
- **Email:** michael.brown@example.com | **Password:** password123
- **Email:** emily.davis@example.com | **Password:** password123

## 💾 Database Schema

### Users Table
- `id` (INT, Primary Key, Auto Increment)
- `name` (VARCHAR)
- `surname` (VARCHAR)
- `phone` (VARCHAR)
- `email` (VARCHAR, Unique)
- `type` (ENUM: 'Administrator', 'User')
- `password` (VARCHAR, Hashed)

### Motorbikes Table
- `code` (VARCHAR, Primary Key)
- `rentingLocation` (VARCHAR)
- `description` (TEXT)
- `costPerHour` (DECIMAL)

### Rentals Table
- `rentalId` (INT, Primary Key, Auto Increment)
- `userId` (INT, Foreign Key → users.id)
- `motorbikeCode` (VARCHAR, Foreign Key → motorbikes.code)
- `startDateTime` (DATETIME)
- `endDateTime` (DATETIME, Nullable)
- `costPerHourAtStart` (DECIMAL)
- `status` (ENUM: 'ACTIVE', 'COMPLETED')

## Technical Details

### Password Hashing
- By default, uses PHP's `password_hash()` with PASSWORD_DEFAULT (bcrypt)
- Can be switched to MD5 for lab compatibility by changing `HASH_METHOD` in `config.php`

### Cost Calculation
- Total cost = (endDateTime - startDateTime) in hours × costPerHour
- Hours are calculated with decimal precision (e.g., 2.5 hours)
- Final cost is rounded to 2 decimal places

### Validation
- All inputs are validated server-side
- Required fields, email format, phone format, numeric values, datetime format
- Prevents renting already-rented motorbikes
- Role-based access control for admin pages

### Search Functionality
- Partial match using SQL LIKE with wildcards
- Multiple fields can be combined (AND logic)
- Case-insensitive search

##  Testing the Application

### User Flow
1. Register a new user account
2. Login with credentials
3. Browse available motorbikes
4. Search for specific motorbikes
5. Rent a motorbike (note the start time and cost per hour)
6. View active rentals
7. Return the motorbike (note the total cost)
8. View rental history

### Admin Flow
1. Login with admin credentials
2. Add new motorbikes
3. Edit existing motorbikes
4. View all motorbikes (all/available/rented filters)
5. Search motorbikes
6. Rent a motorbike for a specific user
7. Return a motorbike for any user
8. View all users
9. Search users
10. View users currently renting
11. View all active and completed rentals

## Notes

- Payment processing is NOT included (as per requirements)
- The application uses session-based authentication
- All database queries use prepared statements for security
- The UI is simple and clean, focusing on functionality
- Code is well-commented for educational purposes

## Troubleshooting

### Database Connection Error
- Check MySQL is running
- Verify database credentials in `config.php`
- Ensure `motocity` database exists

### Login Not Working
- Clear browser cookies/session
- Verify user exists in database
- Check password hashing method matches

### Pages Not Loading
- Check file permissions (755 for directories, 644 for files)
- Verify PHP version (7.4+)
- Check Apache/PHP error logs

##  Support

For issues or questions, please refer to the code comments or contact your instructor.

---

**MotoCity** - Motorbike Rental Management System  
Developed for ISIT307 Assignment
