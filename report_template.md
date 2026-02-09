# ISIT307 Assignment Report Template
# MotoCity - Motorbike Rental Management System

---

## Student Information

**Name:** [Your Full Name]  
**Student Number:** [Your Student Number]  
**Email:** [Your Email Address]  
**Assignment:** ISIT307 - Assignment 2  
**Date:** [Submission Date]

---

## 1. Requirements and Remarks

### 1.1 Project Overview
MotoCity is a web-based motorbike rental management system developed using vanilla PHP and MySQL. The system supports two user types (Administrator and User) and provides comprehensive functionality for managing motorbike rentals.

### 1.2 Requirements Fulfilled

#### Design Requirements
- ✅ Registering/Login interface for users
- ✅ Insert/Edit motorbikes interface (for renting)
- ✅ Rent a motorbike interface
- ✅ Return rented motorbike interface
- ✅ Search for a motorbike to rent interface
- ✅ List motorbikes for renting interface
- ✅ Search Users interface
- ✅ List Users interface

#### Functionalities
- ✅ Both user types can register into the app
- ✅ Both user types can login into the app
- ✅ Both user types can logout
- ✅ Both user types can search for a motorbike
- ✅ Administrator can insert/edit motorbikes (for renting) into the app
- ✅ Administrator can rent/return a motorbike for a particular user
- ✅ Administrator can list: all motorbikes; currently available motorbikes; currently rented motorbikes
- ✅ Administrator can list: all users; all users currently renting motorbikes
- ✅ Administrator can search user
- ✅ User can list: all currently available motorbikes; all motorbikes rented (and returned) in the past; all motorbikes currently renting
- ✅ User can rent a motorbike (with notice for the cost per hour of the renting)
- ✅ User can return rented motorbike (with notice for total cost that needs to be paid)

#### Other Expectations
- ✅ Classes and database with tables created based on requirements
- ✅ Good interfaces and navigation through given options
- ✅ Server-side validation for all inputs
- ✅ Payment processing NOT included (as specified)

### 1.3 Technical Specifications
- **Language:** PHP 7.4+ (vanilla, no frameworks)
- **Database:** MySQL 5.7+ with PDO
- **Architecture:** Object-Oriented Programming (OOP)
- **Security:** Prepared statements, password hashing, session-based authentication
- **Validation:** Server-side validation for all user inputs
- **Search:** Partial match with combinable fields using SQL LIKE

### 1.4 Setup Instructions
Please refer to the README.md file for detailed installation and setup instructions.

**Quick Setup:**
1. Import `schema.sql` into MySQL
2. Configure database credentials in `includes/config.php`
3. Run with XAMPP/MAMP or PHP built-in server
4. Access via browser at `http://localhost/motocity`

**Default Login Credentials:**
- Admin: admin@motocity.com / password123
- User: john.smith@example.com / password123

---

## 2. Website Files and Purpose

### 2.1 Main Pages

| File Name | Purpose |
|-----------|---------|
| `index.php` | Landing/welcome page with application overview |
| `register.php` | User registration page for both Administrator and User types |
| `login.php` | User authentication page |
| `logout.php` | Session termination and logout |
| `dashboard.php` | Role-based dashboard showing statistics and quick actions |
| `motorbikes_list.php` | List motorbikes with filters (all/available/rented for admin; available only for users) |
| `motorbike_search.php` | Search motorbikes by code, location, or description with partial match |
| `motorbike_form.php` | Add new motorbike or edit existing motorbike (admin only) |
| `rent.php` | Rent a motorbike (user rents for self; admin rents for specific user) |
| `return.php` | Return a rented motorbike with automatic cost calculation |
| `rentals_current.php` | View active rentals (user sees own; admin sees all) |
| `rentals_history.php` | View completed rentals with total costs |
| `users_list.php` | List all users or users currently renting (admin only) |
| `user_search.php` | Search users by name, surname, phone, or email (admin only) |

### 2.2 Classes

| Class Name | File Location | Purpose |
|------------|---------------|---------|
| `Database` | `classes/Database.php` | Singleton pattern PDO database connection manager |
| `User` | `classes/User.php` | User management: registration, authentication, CRUD operations, search |
| `Motorbike` | `classes/Motorbike.php` | Motorbike management: CRUD operations, availability checks, search |
| `Rental` | `classes/Rental.php` | Rental transactions: create/complete rentals, cost calculation, listing |
| `Auth` | `classes/Auth.php` | Session management: login checks, role verification, access control |

### 2.3 Include Files

| File Name | Purpose |
|-----------|---------|
| `includes/config.php` | Configuration file with database credentials and application settings |
| `includes/header.php` | HTML header template with meta tags and CSS link |
| `includes/nav.php` | Navigation menu with role-based menu items |
| `includes/footer.php` | HTML footer template |
| `includes/validation.php` | Server-side validation helper functions |

### 2.4 Assets

| File Name | Purpose |
|-----------|---------|
| `assets/css/style.css` | Application stylesheet with responsive design |

### 2.5 Database and Documentation

| File Name | Purpose |
|-----------|---------|
| `schema.sql` | Database schema with table definitions and seed data |
| `README.md` | Complete setup guide and documentation |

---

## 3. Database Tables

### 3.1 Users Table
**Purpose:** Stores user account information for both Administrator and User types

**Fields:**
- `id` (INT, Primary Key, Auto Increment) - Unique user identifier
- `name` (VARCHAR) - User's first name
- `surname` (VARCHAR) - User's last name
- `phone` (VARCHAR) - Contact phone number
- `email` (VARCHAR, Unique) - Email address (used for login)
- `type` (ENUM: 'Administrator', 'User') - User role/type
- `password` (VARCHAR) - Hashed password
- `created_at` (TIMESTAMP) - Account creation timestamp

**Relationships:**
- One-to-many with `rentals` table (one user can have multiple rentals)

### 3.2 Motorbikes Table
**Purpose:** Stores motorbike inventory information

**Fields:**
- `code` (VARCHAR, Primary Key) - Unique motorbike identifier
- `rentingLocation` (VARCHAR) - Location where motorbike is available
- `description` (TEXT) - Detailed description of the motorbike
- `costPerHour` (DECIMAL) - Rental cost per hour
- `created_at` (TIMESTAMP) - Record creation timestamp

**Relationships:**
- One-to-many with `rentals` table (one motorbike can have multiple rental records)

### 3.3 Rentals Table
**Purpose:** Stores rental transaction records

**Fields:**
- `rentalId` (INT, Primary Key, Auto Increment) - Unique rental identifier
- `userId` (INT, Foreign Key) - References user who rented the motorbike
- `motorbikeCode` (VARCHAR, Foreign Key) - References rented motorbike
- `startDateTime` (DATETIME) - Rental start date and time
- `endDateTime` (DATETIME, Nullable) - Rental end date and time (NULL for active rentals)
- `costPerHourAtStart` (DECIMAL) - Cost per hour at the time of rental (copied from motorbike)
- `status` (ENUM: 'ACTIVE', 'COMPLETED') - Rental status
- `created_at` (TIMESTAMP) - Record creation timestamp

**Relationships:**
- Many-to-one with `users` table (foreign key: userId)
- Many-to-one with `motorbikes` table (foreign key: motorbikeCode)

**Business Logic:**
- Active rentals have `status = 'ACTIVE'` and `endDateTime = NULL`
- Completed rentals have `status = 'COMPLETED'` and `endDateTime` set
- Total cost calculated as: (endDateTime - startDateTime) in hours × costPerHourAtStart

---

## 4. User Manual and Testing Checklist

This section provides step-by-step instructions for demonstrating each requirement. Take screenshots at each step for your report.

### 4.1 User Registration and Authentication

#### Test 1: Register New User
**Steps:**
1. Navigate to the home page (`index.php`)
2. Click "Register" in the navigation menu
3. Fill in the registration form:
   - Name: [Your Name]
   - Surname: [Your Surname]
   - Phone: [Valid Phone]
   - Email: [Valid Email]
   - User Type: Select "User"
   - Password: [Minimum 6 characters]
   - Confirm Password: [Same as password]
4. Click "Register" button
5. Verify success message and redirect to login page

**Screenshot Location:** [Insert screenshot showing successful registration]

#### Test 2: User Login
**Steps:**
1. Navigate to login page (`login.php`)
2. Enter email: john.smith@example.com
3. Enter password: password123
4. Click "Login" button
5. Verify redirect to dashboard with user information displayed

**Screenshot Location:** [Insert screenshot showing successful login and dashboard]

#### Test 3: User Logout
**Steps:**
1. While logged in, click "Logout" in the navigation menu
2. Verify redirect to home page
3. Verify navigation menu shows "Login" and "Register" options

**Screenshot Location:** [Insert screenshot showing logout confirmation]

### 4.2 User Features

#### Test 4: List Available Motorbikes
**Steps:**
1. Login as regular user (john.smith@example.com)
2. Click "Available Motorbikes" in navigation
3. Verify list shows only available motorbikes (not currently rented)
4. Verify table displays: Code, Location, Description, Cost per Hour

**Screenshot Location:** [Insert screenshot showing available motorbikes list]

#### Test 5: Search Motorbikes
**Steps:**
1. Login as regular user
2. Click "Search Motorbike" in navigation
3. Enter search criteria (e.g., Code: "MB", Location: "Downtown")
4. Click "Search" button
5. Verify results show motorbikes matching the criteria
6. Test partial match by entering only part of a word

**Screenshot Location:** [Insert screenshot showing search results]

#### Test 6: Rent a Motorbike
**Steps:**
1. Login as regular user
2. Click "Rent Motorbike" in navigation
3. Select a motorbike from the dropdown
4. Verify datetime field is pre-filled with current date/time
5. Click "Rent Motorbike" button
6. Verify success notification displays:
   - Motorbike Code
   - Start Date/Time
   - Cost per Hour

**Screenshot Location:** [Insert screenshot showing rental success notification]

#### Test 7: View Active Rentals
**Steps:**
1. Login as regular user (who has active rentals)
2. Click "My Active Rentals" in navigation
3. Verify list shows currently rented motorbikes
4. Verify table displays: Rental ID, Motorbike Code, Description, Location, Start Date/Time, Cost/Hour, Status

**Screenshot Location:** [Insert screenshot showing active rentals]

#### Test 8: Return a Motorbike
**Steps:**
1. Login as regular user (who has active rentals)
2. Click "Return Motorbike" in navigation
3. Select a rental from the dropdown
4. Verify datetime field is pre-filled with current date/time
5. Click "Return Motorbike" button
6. Verify success notification displays:
   - Motorbike Code
   - Start Date/Time
   - End Date/Time
   - Cost per Hour
   - **Total Cost to Pay** (calculated automatically)

**Screenshot Location:** [Insert screenshot showing return success notification with total cost]

#### Test 9: View Rental History
**Steps:**
1. Login as regular user
2. Click "Rental History" in navigation
3. Verify list shows completed rentals
4. Verify table displays: Rental ID, Motorbike Code, Start/End Date/Time, Cost/Hour, Total Cost, Status

**Screenshot Location:** [Insert screenshot showing rental history]

### 4.3 Administrator Features

#### Test 10: Admin Login
**Steps:**
1. Logout if currently logged in
2. Navigate to login page
3. Enter email: admin@motocity.com
4. Enter password: password123
5. Click "Login" button
6. Verify admin dashboard displays with system overview statistics

**Screenshot Location:** [Insert screenshot showing admin dashboard]

#### Test 11: Add New Motorbike
**Steps:**
1. Login as administrator
2. Click "Add Motorbike" in navigation
3. Fill in the form:
   - Code: [Unique code, e.g., MB009]
   - Renting Location: [Location name]
   - Description: [Detailed description]
   - Cost per Hour: [Positive number]
4. Click "Add Motorbike" button
5. Verify success message and motorbike appears in list

**Screenshot Location:** [Insert screenshot showing add motorbike form and success]

#### Test 12: Edit Motorbike
**Steps:**
1. Login as administrator
2. Navigate to "Motorbikes" list
3. Click "Edit" link for a motorbike
4. Modify fields (location, description, or cost)
5. Click "Update Motorbike" button
6. Verify success message and changes are saved

**Screenshot Location:** [Insert screenshot showing edit motorbike form]

#### Test 13: List All Motorbikes (with filters)
**Steps:**
1. Login as administrator
2. Click "Motorbikes" in navigation
3. Test filter buttons:
   - Click "All Motorbikes" - verify shows all motorbikes
   - Click "Available" - verify shows only available motorbikes
   - Click "Currently Rented" - verify shows only rented motorbikes

**Screenshot Location:** [Insert screenshots showing each filter view]

#### Test 14: Rent Motorbike for User
**Steps:**
1. Login as administrator
2. Click "Rent Motorbike" in navigation
3. Select a user from the dropdown
4. Select an available motorbike
5. Set start date/time
6. Click "Rent Motorbike" button
7. Verify success notification displays rental details

**Screenshot Location:** [Insert screenshot showing admin renting for user]

#### Test 15: Return Motorbike for User
**Steps:**
1. Login as administrator
2. Click "Return Motorbike" in navigation
3. Select an active rental (shows user information)
4. Set end date/time
5. Click "Return Motorbike" button
6. Verify success notification displays total cost

**Screenshot Location:** [Insert screenshot showing admin returning for user]

#### Test 16: List All Users
**Steps:**
1. Login as administrator
2. Click "Users" in navigation
3. Test filter buttons:
   - Click "All Users" - verify shows all registered users
   - Click "Currently Renting" - verify shows only users with active rentals
4. Verify table displays: ID, Name, Surname, Phone, Email, Type

**Screenshot Location:** [Insert screenshots showing user lists]

#### Test 17: Search Users
**Steps:**
1. Login as administrator
2. Click "Search User" in navigation
3. Enter search criteria (e.g., Name: "John", Email: "@example.com")
4. Click "Search" button
5. Verify results show users matching the criteria
6. Test partial match functionality

**Screenshot Location:** [Insert screenshot showing user search results]

### 4.4 Validation Testing

#### Test 18: Input Validation
**Steps:**
1. Test registration with invalid inputs:
   - Empty required fields
   - Invalid email format
   - Invalid phone format
   - Password less than 6 characters
   - Mismatched passwords
2. Test motorbike form with invalid inputs:
   - Empty required fields
   - Negative or zero cost per hour
3. Test rental with invalid inputs:
   - No motorbike selected
   - Invalid datetime format
4. Verify appropriate error messages are displayed for each validation failure

**Screenshot Location:** [Insert screenshot showing validation error messages]

#### Test 19: Business Logic Validation
**Steps:**
1. Attempt to rent an already-rented motorbike
2. Verify error message: "Motorbike is already rented"
3. Attempt to return with end time before start time
4. Verify error message: "End date/time must be after start date/time"

**Screenshot Location:** [Insert screenshot showing business logic validation]

### 4.5 Access Control Testing

#### Test 20: Role-Based Access Control
**Steps:**
1. Login as regular user
2. Attempt to access admin-only pages by typing URL directly:
   - `/motorbike_form.php`
   - `/users_list.php`
   - `/user_search.php`
3. Verify redirect to dashboard with "Access denied" message
4. Verify navigation menu shows only user-appropriate options

**Screenshot Location:** [Insert screenshot showing access denied message]

### 4.6 Search Functionality Testing

#### Test 21: Combinable Search Fields
**Steps:**
1. Test motorbike search with multiple fields:
   - Code: "MB" + Location: "Downtown"
   - Verify results match both criteria (AND logic)
2. Test user search with multiple fields:
   - Name: "John" + Email: "@example"
   - Verify results match both criteria
3. Test partial match:
   - Search "Hond" in description - should find "Honda"
   - Search "555" in phone - should find phones containing "555"

**Screenshot Location:** [Insert screenshot showing combined search results]

### 4.7 Cost Calculation Testing

#### Test 22: Rental Cost Calculation
**Steps:**
1. Create a rental with known start time (e.g., 10:00 AM)
2. Return the rental with known end time (e.g., 2:30 PM = 4.5 hours)
3. Verify total cost calculation:
   - Example: 4.5 hours × $15.00/hour = $67.50
4. Verify cost is displayed with 2 decimal places

**Screenshot Location:** [Insert screenshot showing cost calculation]

---

## 5. Code Quality and Best Practices

### 5.1 OOP Implementation
- ✅ Small, focused classes with single responsibilities
- ✅ Private fields with public getters/setters
- ✅ Constructor methods for initialization
- ✅ Clear method names describing functionality

### 5.2 Security Measures
- ✅ Prepared statements for all database queries (prevents SQL injection)
- ✅ Password hashing using `password_hash()` (bcrypt)
- ✅ Input sanitization using `htmlspecialchars()`
- ✅ Session-based authentication
- ✅ Role-based access control

### 5.3 Code Organization
- ✅ Separation of concerns (classes, includes, pages)
- ✅ Consistent naming conventions
- ✅ Comprehensive code comments
- ✅ Reusable validation functions
- ✅ DRY principle (Don't Repeat Yourself)

### 5.4 User Experience
- ✅ Clean and intuitive interface
- ✅ Role-based navigation menus
- ✅ Clear success and error messages
- ✅ Responsive design
- ✅ Pre-filled datetime fields with current time
- ✅ On-screen notifications for important actions

---

## 6. Challenges and Solutions

### Challenge 1: [Describe any challenges you faced]
**Solution:** [Describe how you solved it]

### Challenge 2: [Describe any challenges you faced]
**Solution:** [Describe how you solved it]

---

## 7. Conclusion

This project successfully implements a complete motorbike rental management system meeting all specified requirements. The application demonstrates:

- Proper OOP principles with well-structured classes
- Secure database operations using prepared statements
- Comprehensive server-side validation
- Role-based access control
- User-friendly interface with clear navigation
- Complete CRUD operations for all entities
- Advanced search functionality with partial matching
- Accurate cost calculation for rentals

The system is ready for deployment and can be easily extended with additional features such as payment processing, email notifications, or reporting dashboards.

---

## 8. References

[List any references, tutorials, or resources you used]

- PHP Official Documentation: https://www.php.net/docs.php
- MySQL Documentation: https://dev.mysql.com/doc/
- [Add other references as needed]

---

**End of Report**

---

## Appendix: Screenshot Checklist

Use this checklist to ensure you have all required screenshots:

- [ ] Home page
- [ ] Registration page (filled form)
- [ ] Registration success message
- [ ] Login page
- [ ] User dashboard
- [ ] Admin dashboard
- [ ] Available motorbikes list
- [ ] Motorbike search form and results
- [ ] Rent motorbike form
- [ ] Rent success notification (showing start time and cost per hour)
- [ ] Active rentals list
- [ ] Return motorbike form
- [ ] Return success notification (showing total cost)
- [ ] Rental history list
- [ ] Add motorbike form (admin)
- [ ] Edit motorbike form (admin)
- [ ] All motorbikes list (admin)
- [ ] Available motorbikes filter (admin)
- [ ] Rented motorbikes filter (admin)
- [ ] Rent for user form (admin)
- [ ] Return for user form (admin)
- [ ] All users list (admin)
- [ ] Users currently renting list (admin)
- [ ] User search form and results (admin)
- [ ] Validation error messages
- [ ] Access denied message (non-admin trying to access admin page)
- [ ] Combined search results (multiple fields)
- [ ] Cost calculation example

---

**Note:** Replace all placeholder text in square brackets [like this] with your actual information and screenshots.
