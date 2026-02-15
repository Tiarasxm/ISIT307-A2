# MotoCity - Simple Tutorial Version

This is a simplified version of MotoCity using basic mysqli connections (no OOP classes).

## Setup Instructions

1. **Start XAMPP** (Apache + MySQL)

2. **Create Database**:
   - Visit: `http://localhost/ISIT307-A2-Simple/create_database.php`
   - This will create the database, tables, and insert sample data

3. **Access the App**:
   - URL: `http://localhost/ISIT307-A2-Simple/`

## Login Credentials

- **Admin**: `admin@motocity.com` / `password123`
- **User**: `weiming.lim@example.com` / `password123`

## Files Structure

- `db_connect.php` - Database connection
- `create_database.php` - Database setup script
- `index.php` - Home page
- `login.php` - Login page
- `register.php` - Registration page
- `dashboard.php` - User dashboard
- `motorbikes_list.php` - List/search motorbikes
- `logout.php` - Logout script

## Database Connection

All files use simple mysqli connection:
```php
include 'db_connect.php';
// $conn is now available
```

## Note

This version uses:
- Simple mysqli (no PDO)
- No OOP classes
- Direct SQL queries
- Tutorial-style code matching add.php, create.php, list.php pattern
