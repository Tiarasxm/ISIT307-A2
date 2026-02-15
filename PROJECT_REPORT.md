# MotoCity Project Report
## ISIT307 Assignment 2 - Dynamic Web Application

**Student Name:** [Your Name]  
**Student ID:** [Your ID]  
**Date:** February 15, 2026  
**GitHub Repository:** https://github.com/Tiarasxm/ISIT307-A2

---

## 1. Project Overview

### 1.1 Introduction

MotoCity is a dynamic web application designed for managing motorbike rentals across different locations in a city. The system provides a comprehensive platform for users to rent motorbikes and for administrators to manage the entire rental operation.

### 1.2 Purpose

The application serves two primary user groups:
- **Regular Users**: Can browse, search, rent, and return motorbikes
- **Administrators**: Have full control over motorbikes, users, and rental operations

### 1.3 Technology Stack

- **Backend**: PHP 7.4+ with Object-Oriented Programming
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3
- **Server**: Apache (XAMPP)
- **Architecture**: MVC-inspired with OOP classes

### 1.4 Key Features

- User registration and authentication (both Administrator and User types)
- Motorbike management (CRUD operations)
- Rental system with real-time cost calculation
- Advanced search functionality for motorbikes and users
- Rental history tracking
- Role-based access control
- Secure password hashing
- Input validation and SQL injection prevention

---

## 2. Setup and Running the Website

### 2.1 Prerequisites

- XAMPP (or similar LAMP/WAMP stack)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser (Chrome, Firefox, Safari, or Edge)

### 2.2 Installation Steps

#### Step 1: Install XAMPP
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP on your system
3. Start Apache and MySQL services from XAMPP Control Panel

#### Step 2: Deploy Application
1. Copy the `ISIT307-A2` folder to XAMPP's `htdocs` directory
   - **Mac Path**: `/Applications/XAMPP/xamppfiles/htdocs/ISIT307-A2/`
   - **Windows Path**: `C:\xampp\htdocs\ISIT307-A2\`

#### Step 3: Create Database
1. Open your web browser
2. Navigate to: `http://localhost/ISIT307-A2/create_database.php`
3. The script will automatically:
   - Create the `motocity` database
   - Create all required tables (users, motorbikes, rentals)
   - Insert sample data for testing

#### Step 4: Access the Application
1. Navigate to: `http://localhost/ISIT307-A2/`
2. You will be redirected to the login page
3. Use the credentials below to login

### 2.3 Login Credentials

#### Administrator Account:
- **Email**: `admin@motocity.com`
- **Password**: `password123`

#### Sample User Accounts:
- **User 1**: `weiming.lim@example.com` / `password123`
- **User 2**: `meiling.tan@example.com` / `password123`
- **User 3**: `raj.kumar@example.com` / `password123`
- **User 4**: `sarah.chen@example.com` / `password123`

### 2.4 Database Configuration

If you need to modify database settings, edit `classes/Database.php`:

```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "motocity";
```

---

## 3. Project File Structure

### 3.1 Directory Structure

```
ISIT307-A2/
├── classes/              # OOP Classes
│   ├── Database.php
│   ├── User.php
│   ├── Motorbike.php
│   ├── Rental.php
│   └── Auth.php
├── includes/             # Reusable components
│   ├── header.php
│   ├── nav.php
│   └── footer.php
├── assets/               # Static resources
│   └── css/
│       └── style.css
├── *.php                 # Application pages
├── create_database.php   # Database setup
└── README.md            # Documentation
```

### 3.2 OOP Classes (classes/)

#### 3.2.1 Database.php
**Purpose**: Manages database connection using Singleton pattern

**Key Methods**:
- `getInstance()` - Returns single database instance
- `getConnection()` - Returns mysqli connection object

**Design Pattern**: Singleton - ensures only one database connection exists throughout the application

**Description**: This class implements the Singleton design pattern to manage the MySQL database connection. It prevents multiple connections from being created and provides a global point of access to the database connection.

---

#### 3.2.2 User.php
**Purpose**: Handles all user-related operations

**Key Methods**:
- `register()` - Registers new user with hashed password
- `login($email, $password)` - Authenticates user credentials
- `getUserById($id)` - Retrieves user by ID
- `getAllUsers()` - Returns all users (admin only)
- `searchUsers($name, $surname, $phone, $email)` - Searches users by multiple criteria
- `getUsersCurrentlyRenting()` - Returns users with active rentals

**Properties**: 
- `id` - User ID
- `name` - First name
- `surname` - Last name
- `phone` - Phone number
- `email` - Email address
- `type` - User type (Administrator/User)
- `password` - Hashed password

**Description**: Manages all user operations including registration with password hashing, authentication, and user search functionality. Supports both Administrator and User types.

---

#### 3.2.3 Motorbike.php
**Purpose**: Manages motorbike inventory and operations

**Key Methods**:
- `create()` - Adds new motorbike to system
- `update()` - Updates motorbike details
- `getByCode($code)` - Retrieves motorbike by code
- `getAllMotorbikes()` - Returns all motorbikes
- `getAvailableMotorbikes()` - Returns only available motorbikes
- `getRentedMotorbikes()` - Returns currently rented motorbikes
- `searchMotorbikes($code, $location, $description)` - Multi-criteria search
- `isAvailable($code)` - Checks if motorbike is available for rent

**Properties**: 
- `code` - Unique motorbike identifier
- `rentingLocation` - Pick-up/drop-off location
- `description` - Motorbike details
- `costPerHour` - Rental rate per hour

**Description**: Handles all motorbike-related operations including CRUD operations, availability checking, and search functionality with support for partial term matching.

---

#### 3.2.4 Rental.php
**Purpose**: Handles rental operations and cost calculations

**Key Methods**:
- `createRental()` - Creates new rental record
- `returnRental($rentalId)` - Marks rental as completed
- `getRentalById($rentalId)` - Retrieves rental details
- `getActiveRentalsByUser($userId)` - Returns user's active rentals
- `getCompletedRentalsByUser($userId)` - Returns user's rental history
- `getAllActiveRentals()` - Returns all active rentals (admin)
- `calculateTotalCost($start, $end, $costPerHour)` - Calculates rental cost with minimum 1-hour charge

**Properties**: 
- `rentalId` - Unique rental ID
- `userId` - User who rented
- `motorbikeCode` - Motorbike code
- `startDateTime` - Rental start time
- `endDateTime` - Rental end time
- `costPerHourAtStart` - Cost at rental start
- `status` - ACTIVE or COMPLETED

**Description**: Manages the entire rental lifecycle from creation to return, including accurate cost calculation that accounts for hours, minutes, and seconds with a minimum 1-hour charge policy.

---

#### 3.2.5 Auth.php
**Purpose**: Authentication and authorization helper

**Key Methods**:
- `isLoggedIn()` - Checks if user is logged in
- `isAdmin()` - Checks if user is administrator
- `requireLogin()` - Redirects to login if not authenticated
- `requireAdmin()` - Redirects if not admin
- `redirectIfLoggedIn()` - Redirects logged-in users to dashboard
- `setUserSession()` - Sets session variables after login
- `getCurrentUserId()` - Returns current user's ID
- `getCurrentUserName()` - Returns current user's name

**Description**: Provides authentication and authorization utilities used throughout the application to protect pages and check user permissions.

---

### 3.3 Include Files (includes/)

#### 3.3.1 header.php
**Purpose**: Common page header with site branding  
**Content**: Displays "MotoCity" logo/title in styled header bar  
**Usage**: Included at the top of all pages for consistent branding

#### 3.3.2 nav.php
**Purpose**: Dynamic navigation menu  
**Features**:
- Different menus for Admin vs User roles
- Active page highlighting
- Logout link with username display
- Responsive design

#### 3.3.3 footer.php
**Purpose**: Common page footer  
**Content**: Placeholder for future footer content

---

### 3.4 Application Pages

#### 3.4.1 index.php
**Purpose**: Application entry point  
**Function**: Redirects visitors to login.php  
**Code**: Simple redirect script

#### 3.4.2 login.php
**Purpose**: User authentication page  
**Features**:
- Email and password input fields
- Session creation on successful login
- Error message display for failed attempts
- Link to registration page
- Uses User class for authentication
- Styled header with Login/Register buttons

#### 3.4.3 register.php
**Purpose**: New user registration  
**Features**:
- Form fields: name, surname, phone, email, type, password, confirm password
- Phone number locked to +65 prefix (Singapore format)
- Password confirmation validation
- Input validation (email format, password length, etc.)
- Redirects to login after successful registration
- Prevents duplicate email registration

#### 3.4.4 dashboard.php
**Purpose**: User landing page after login  
**Features**:
- Welcome message with user details
- Statistics cards with different data for Admin vs User
- **Admin View**: Total/Available/Rented motorbikes, Active rentals
- **User View**: Available motorbikes, Active rentals, Completed rentals
- Clickable cards linking to relevant pages
- Role-based content display

#### 3.4.5 motorbikes_list.php
**Purpose**: List and search motorbikes  
**Features**:
- Search form (code, location, description)
- Filter options for Admin (All/Available/Rented)
- Table display with motorbike details
- Edit button for administrators
- Partial search term support
- Case-insensitive search

#### 3.4.6 motorbike_form.php
**Purpose**: Add or edit motorbikes (Admin only)  
**Features**:
- Form fields: code, location, description, cost per hour
- Create new or update existing motorbike
- Code field readonly when editing
- Input validation
- Success/error message display

#### 3.4.7 rent.php
**Purpose**: Rent a motorbike  
**Features**:
- Dropdown list of available motorbikes
- Admin can select user to rent for
- Displays start time notification
- Displays cost per hour notification
- Creates rental record with current timestamp
- Prevents renting unavailable motorbikes

#### 3.4.8 return.php
**Purpose**: Return rented motorbike  
**Features**:
- Table of active rentals
- Radio button selection
- Calculates and displays total cost
- Shows start time, end time, and duration
- Minimum 1-hour charge applied
- Admin sees all users' rentals
- Users see only their own rentals

#### 3.4.9 rentals_history.php
**Purpose**: View completed rentals (User only)  
**Features**:
- Table of past rentals
- Shows all rental details including total cost
- Sorted by most recent first
- Displays motorbike details and rental duration

#### 3.4.10 users_list.php
**Purpose**: List and search users (Admin only)  
**Features**:
- Search form (name, surname, phone, email)
- Filter: All users or Currently renting
- Table display with user details and type badges
- Partial search term support
- Multi-field search capability

#### 3.4.11 logout.php
**Purpose**: User logout  
**Function**: Destroys session and redirects to index page

#### 3.4.12 create_database.php
**Purpose**: Database initialization script  
**Features**:
- Creates motocity database
- Creates all tables with proper schema
- Inserts sample data (users, motorbikes, rentals)
- One-time setup script
- Displays success confirmation

---

### 3.5 Assets

#### 3.5.1 assets/css/style.css
**Purpose**: Application styling  
**Features**:
- CSS variables for consistent colors
- Responsive design
- Form styling
- Table styling
- Card components
- Button styles (primary, secondary, small)
- Navigation styling
- Message boxes (success/error/info)
- Badge components
- Grid layouts

---

### 3.6 Database Tables

#### 3.6.1 users Table
**Purpose**: Stores user account information

| Column   | Type         | Constraints | Description                          |
|----------|--------------|-------------|--------------------------------------|
| id       | INT          | PRIMARY KEY, AUTO_INCREMENT | Unique user identifier |
| name     | VARCHAR(100) | NOT NULL    | User's first name                    |
| surname  | VARCHAR(100) | NOT NULL    | User's last name                     |
| phone    | VARCHAR(20)  | NOT NULL    | Phone number (format: +65XXXXXXXX)   |
| email    | VARCHAR(100) | NOT NULL, UNIQUE | Email address (used for login) |
| type     | ENUM('Administrator', 'User') | NOT NULL | User role type |
| password | VARCHAR(255) | NOT NULL    | Hashed password using password_hash() |

**Indexes**: 
- PRIMARY KEY (id)
- UNIQUE (email)

**Sample Data**:
- 1 Administrator account
- 4 User accounts
- All passwords hashed with password_hash()

---

#### 3.6.2 motorbikes Table
**Purpose**: Stores motorbike inventory

| Column          | Type          | Constraints | Description                        |
|-----------------|---------------|-------------|------------------------------------|
| code            | VARCHAR(20)   | PRIMARY KEY | Unique motorbike identifier        |
| rentingLocation | VARCHAR(200)  | NOT NULL    | Pick-up/drop-off location          |
| description     | TEXT          | NOT NULL    | Motorbike details and specifications |
| costPerHour     | DECIMAL(10,2) | NOT NULL    | Rental cost per hour               |

**Indexes**: 
- PRIMARY KEY (code)

**Sample Data**:
- 8 motorbikes with various locations
- Cost range: $8.00 - $20.00 per hour
- Singapore locations (Orchard, Marina Bay, Jurong, etc.)

---

#### 3.6.3 rentals Table
**Purpose**: Tracks all rental transactions

| Column              | Type          | Constraints | Description                           |
|---------------------|---------------|-------------|---------------------------------------|
| rentalId            | INT           | PRIMARY KEY, AUTO_INCREMENT | Unique rental identifier |
| userId              | INT           | FOREIGN KEY | References users(id)                  |
| motorbikeCode       | VARCHAR(20)   | FOREIGN KEY | References motorbikes(code)           |
| startDateTime       | DATETIME      | NOT NULL    | Rental start timestamp                |
| endDateTime         | DATETIME      | NULL        | Rental end timestamp (NULL if active) |
| costPerHourAtStart  | DECIMAL(10,2) | NOT NULL    | Cost per hour at rental start         |
| status              | ENUM('ACTIVE', 'COMPLETED') | NOT NULL | Rental status |

**Indexes**: 
- PRIMARY KEY (rentalId)
- FOREIGN KEY (userId) REFERENCES users(id)
- FOREIGN KEY (motorbikeCode) REFERENCES motorbikes(code)

**Relationships**:
- One user can have multiple rentals
- One motorbike can have multiple rentals (but only one ACTIVE at a time)
- Rental history preserved for completed rentals

**Sample Data**:
- Mix of ACTIVE and COMPLETED rentals
- Realistic timestamps and durations

---

## 4. User Manual

### 4.1 Getting Started

#### 4.1.1 Registration Process

**Steps**:
1. Navigate to `http://localhost/ISIT307-A2/`
2. Click "Register" button in the header
3. Fill in the registration form:
   - **Name**: Your first name (e.g., "John")
   - **Surname**: Your last name (e.g., "Doe")
   - **Phone**: 8-digit number (automatically prefixed with +65)
   - **Email**: Valid email address (e.g., "john.doe@example.com")
   - **User Type**: Select "User" or "Administrator"
   - **Password**: Minimum 6 characters
   - **Confirm Password**: Must match password
4. Click "Register" button
5. Upon success, redirected to login page with success message

**Validation**:
- All fields are required
- Email must be valid format and unique
- Phone must be exactly 8 digits
- Password must be at least 6 characters
- Passwords must match

**Screenshot**: [Insert screenshot of registration page]

---

#### 4.1.2 Login Process

**Steps**:
1. Enter your registered email address
2. Enter your password
3. Click "Login" button
4. Upon successful login, redirected to dashboard

**Error Handling**:
- Invalid credentials display error message
- Empty fields show validation errors

**Screenshot**: [Insert screenshot of login page]

---

### 4.2 User Features

#### 4.2.1 Dashboard (User View)

After logging in as a regular user, you'll see:

**Welcome Section**:
- Personalized greeting with your name
- Account type badge
- Email address

**Statistics Cards** (clickable):
1. **Available Motorbikes**: Shows count of rentable motorbikes
2. **My Active Rentals**: Shows count of your current rentals
3. **Completed Rentals**: Shows count of your rental history

**Navigation Menu**:
- Dashboard
- Motorbikes (list/search)
- Rent
- Return
- History
- Logout (Your Name)

**Screenshot**: [Insert screenshot of user dashboard]

---

#### 4.2.2 Browsing Available Motorbikes

**Steps**:
1. Click "Motorbikes" in navigation menu
2. View table of available motorbikes

**Table Columns**:
- **Code**: Unique identifier (e.g., MB001)
- **Location**: Pick-up location (e.g., Orchard MRT Station)
- **Description**: Motorbike details (e.g., Honda CB500X - Red)
- **Cost per Hour**: Rental rate (e.g., $15.00)

**Note**: Users only see available motorbikes (not currently rented)

**Screenshot**: [Insert screenshot of motorbikes list]

---

#### 4.2.3 Searching for Motorbikes

**Steps**:
1. On the Motorbikes page, locate the search form at the top
2. Enter search criteria (can use one or multiple fields):
   - **Code**: Partial or full motorbike code (e.g., "MB" or "MB001")
   - **Location**: Location name or partial match (e.g., "Orchard")
   - **Description**: Any text in description (e.g., "Honda")
3. Click "Search" button
4. Results display matching motorbikes
5. Click "Clear Search" to reset and show all

**Features**:
- Partial term matching (searching "MB" finds MB001, MB002, etc.)
- Case-insensitive search
- Can search multiple fields simultaneously
- Empty search shows all motorbikes

**Screenshot**: [Insert screenshot of search results]

---

#### 4.2.4 Renting a Motorbike

**Steps**:
1. Click "Rent" in navigation menu
2. Select a motorbike from the dropdown list
   - Format: Code - Description - $XX.XX/hr
3. Click "Start Rental" button
4. Success notification displays:
   - Motorbike code
   - Start time (current date and time)
   - Cost per hour

**Success Message Example**:
```
Rental started successfully!
Motorbike: MB001
Start Time: 2026-02-15 14:30:00
Cost per Hour: $15.00
```

**Requirements**:
- At least one motorbike must be available
- Cannot rent a motorbike that's already rented
- Rental starts immediately with current timestamp

**Screenshot**: [Insert screenshot of rent page with success notification]

---

#### 4.2.5 Returning a Motorbike

**Steps**:
1. Click "Return" in navigation menu
2. View table of your active rentals
3. Select the rental you want to return (radio button)
4. Click "Return Selected Motorbike" button
5. Success notification displays:
   - Motorbike code
   - Start time
   - End time (current date and time)
   - **Total Cost** (calculated)

**Cost Calculation**:
- Minimum charge: 1 hour (even if rented for 30 minutes)
- Accurate calculation for longer rentals
- Includes hours, minutes, and seconds
- Example: 2 hours 30 minutes at $15/hour = $37.50

**Success Message Example**:
```
Motorbike returned successfully!
Motorbike Code: MB001
Start Time: 2026-02-15 14:30:00
End Time: 2026-02-15 17:00:00
Total Cost: $37.50
```

**Screenshot**: [Insert screenshot of return page with cost calculation]

---

#### 4.2.6 Viewing Rental History

**Steps**:
1. Click "History" in navigation menu
2. View table of all your completed rentals

**Table Columns**:
- Motorbike Code
- Description
- Start Time
- End Time
- Cost per Hour
- Total Cost

**Features**:
- Sorted by most recent first
- Shows all past rentals
- Complete cost breakdown

**Screenshot**: [Insert screenshot of rental history]

---

### 4.3 Administrator Features

#### 4.3.1 Dashboard (Admin View)

After logging in as administrator, you'll see:

**Welcome Section**:
- Personalized greeting
- Administrator badge
- Email address

**Statistics Cards** (clickable):
1. **Total Motorbikes**: Total inventory count
2. **Available Motorbikes**: Ready to rent
3. **Rented Motorbikes**: Currently rented
4. **Active Rentals**: Total active rental transactions

**Navigation Menu**:
- Dashboard
- Motorbikes (list/search)
- Add Motorbike
- Rent (for users)
- Return (for users)
- Users (list/search)
- Logout (Admin Tan)

**Screenshot**: [Insert screenshot of admin dashboard]

---

#### 4.3.2 Adding a New Motorbike

**Steps**:
1. Click "Add Motorbike" in navigation menu
2. Fill in the form:
   - **Code**: Unique identifier (e.g., MB009) - required
   - **Renting Location**: Pick-up location (e.g., Sentosa Island) - required
   - **Description**: Motorbike details (e.g., Kawasaki Ninja 400 - Green) - required
   - **Cost per Hour**: Rental rate (e.g., 22.00) - required
3. Click "Add Motorbike" button
4. Success message confirms creation
5. Options to add another or view all motorbikes

**Validation**:
- Code must be unique
- All fields required
- Cost must be positive number

**Screenshot**: [Insert screenshot of add motorbike form]

---

#### 4.3.3 Editing a Motorbike

**Steps**:
1. Go to "Motorbikes" page
2. Click "Edit" button next to the motorbike you want to modify
3. Form loads with current data
4. Modify fields (Code cannot be changed)
5. Click "Update Motorbike" button
6. Success message confirms update

**Editable Fields**:
- Renting Location
- Description
- Cost per Hour

**Note**: Code is readonly to maintain referential integrity

**Screenshot**: [Insert screenshot of edit motorbike form]

---

#### 4.3.4 Viewing Motorbike Filters

On the Motorbikes page, administrators have filter buttons:

**Filter Options**:
1. **All Motorbikes**: Shows entire inventory (default)
2. **Available**: Shows only rentable motorbikes
3. **Currently Rented**: Shows motorbikes in use

**Usage**:
- Click filter button to change view
- Active filter highlighted in orange
- Useful for inventory management

**Screenshot**: [Insert screenshot of motorbikes with filters]

---

#### 4.3.5 Renting Motorbike for a User

**Steps**:
1. Click "Rent" in navigation menu
2. Select user from dropdown
   - Format: Name Surname (email@example.com)
3. Select motorbike from dropdown
   - Format: Code - Description - $XX.XX/hr
4. Click "Start Rental" button
5. Rental is created for the selected user
6. Success notification displays

**Use Case**: 
- Walk-in customer without account
- Phone rental booking
- Assisting users

**Screenshot**: [Insert screenshot of admin rent page]

---

#### 4.3.6 Returning Motorbike for a User

**Steps**:
1. Click "Return" in navigation menu
2. View table of ALL active rentals (all users)
3. Table shows:
   - Motorbike Code
   - Description
   - User (name and surname)
   - Start Time
   - Cost per Hour
4. Select rental to return (radio button)
5. Click "Return Selected Motorbike" button
6. Total cost calculated and displayed

**Features**:
- Admin sees all users' rentals
- Can return any active rental
- Cost calculated automatically

**Screenshot**: [Insert screenshot of admin return page]

---

#### 4.3.7 Viewing All Users

**Steps**:
1. Click "Users" in navigation menu
2. View table of all registered users

**Table Columns**:
- Name
- Surname
- Phone
- Email
- Type (badge: Administrator/User)

**Features**:
- Complete user directory
- Type badges color-coded
- Sorted alphabetically

**Screenshot**: [Insert screenshot of users list]

---

#### 4.3.8 Searching Users

**Steps**:
1. On Users page, use search form at top
2. Enter search criteria (can use multiple):
   - **Name**: First name search
   - **Surname**: Last name search
   - **Phone**: Phone number search
   - **Email**: Email search
3. Click "Search" button
4. Results show matching users
5. Click "Clear Search" to reset

**Features**:
- Partial matching on all fields
- Case-insensitive
- Multi-field search
- Example: Search "Lim" in surname finds all Lims

**Screenshot**: [Insert screenshot of user search]

---

#### 4.3.9 Viewing Users Currently Renting

**Steps**:
1. On Users page, click "Currently Renting" filter button
2. Shows only users with active rentals
3. Useful for tracking who has motorbikes

**Use Case**:
- Quick view of active customers
- Follow-up on rentals
- Inventory management

**Screenshot**: [Insert screenshot of users currently renting]

---

## 5. Assignment Requirements Fulfillment

### 5.1 Functional Requirements

#### Requirement 1: User Registration (Both Types)
✅ **Fulfilled**: register.php  
**Evidence**: Both Administrator and User can register via registration form with all required fields (name, surname, phone, email, type, password)  
**Screenshot**: [Registration page]

#### Requirement 2: User Login (Both Types)
✅ **Fulfilled**: login.php  
**Evidence**: Both types can login with email and password, session created on success  
**Screenshot**: [Login page]

#### Requirement 3: User Logout (Both Types)
✅ **Fulfilled**: logout.php  
**Evidence**: Logout link in navigation destroys session and redirects to login  
**Screenshot**: [Navigation with logout]

#### Requirement 4: List Available Motorbikes (User)
✅ **Fulfilled**: motorbikes_list.php  
**Evidence**: Users see only available motorbikes in table format with all details  
**Screenshot**: [User motorbikes list]

#### Requirement 5: Search Motorbikes (Both Types)
✅ **Fulfilled**: motorbikes_list.php search form  
**Evidence**: Search by code, location, description with partial term support  
**Screenshot**: [Search form and results]

#### Requirement 6: Rent Motorbikes (User)
✅ **Fulfilled**: rent.php  
**Evidence**: Dropdown of available motorbikes, displays start time and cost notification  
**Screenshot**: [Rent page with notification]

#### Requirement 7: Return Motorbikes (User)
✅ **Fulfilled**: return.php  
**Evidence**: Shows active rentals, calculates and displays total cost on return  
**Screenshot**: [Return page with cost]

#### Requirement 8: List Past Rentals (User)
✅ **Fulfilled**: rentals_history.php  
**Evidence**: Table of completed rentals with all details including costs  
**Screenshot**: [Rental history]

#### Requirement 9: List Currently Renting (User)
✅ **Fulfilled**: return.php  
**Evidence**: Shows user's active rentals ready for return  
**Screenshot**: [Active rentals view]

#### Requirement 10: Insert/Edit Motorbikes (Admin)
✅ **Fulfilled**: motorbike_form.php  
**Evidence**: Form to create new or edit existing motorbikes with all fields  
**Screenshot**: [Add/Edit motorbike form]

#### Requirement 11: Rent for User (Admin)
✅ **Fulfilled**: rent.php with user selection  
**Evidence**: Admin can select user and motorbike to create rental  
**Screenshot**: [Admin rent page]

#### Requirement 12: Return for User (Admin)
✅ **Fulfilled**: return.php showing all rentals  
**Evidence**: Admin sees all users' rentals and can return any  
**Screenshot**: [Admin return page]

#### Requirement 13: List All Motorbikes (Admin)
✅ **Fulfilled**: motorbikes_list.php with "All" filter  
**Evidence**: Shows entire inventory with filter button  
**Screenshot**: [All motorbikes view]

#### Requirement 14: List Available Motorbikes (Admin)
✅ **Fulfilled**: motorbikes_list.php with "Available" filter  
**Evidence**: Shows only rentable motorbikes  
**Screenshot**: [Available filter]

#### Requirement 15: List Rented Motorbikes (Admin)
✅ **Fulfilled**: motorbikes_list.php with "Currently Rented" filter  
**Evidence**: Shows motorbikes in use  
**Screenshot**: [Rented filter]

#### Requirement 16: Search Motorbikes (Admin)
✅ **Fulfilled**: Same search as users  
**Evidence**: Multi-field search with partial terms  
**Screenshot**: [Admin search]

#### Requirement 17: List All Users (Admin)
✅ **Fulfilled**: users_list.php  
**Evidence**: Table of all registered users with complete details  
**Screenshot**: [Users list]

#### Requirement 18: Search Users (Admin)
✅ **Fulfilled**: users_list.php search form  
**Evidence**: Search by name, surname, phone, email with partial matching  
**Screenshot**: [User search]

#### Requirement 19: List Users Currently Renting (Admin)
✅ **Fulfilled**: users_list.php with "Currently Renting" filter  
**Evidence**: Shows only users with active rentals  
**Screenshot**: [Users renting filter]

---

### 5.2 Technical Requirements

#### Requirement 20: Object-Oriented PHP
✅ **Fulfilled**: 5 OOP classes created  
**Evidence**: 
- Database.php (Singleton pattern)
- User.php (User management)
- Motorbike.php (Motorbike operations)
- Rental.php (Rental operations)
- Auth.php (Authentication helper)

#### Requirement 21: MySQL Database
✅ **Fulfilled**: 3 tables with proper schema  
**Evidence**:
- users table (id, name, surname, phone, email, type, password)
- motorbikes table (code, rentingLocation, description, costPerHour)
- rentals table (rentalId, userId, motorbikeCode, startDateTime, endDateTime, costPerHourAtStart, status)

#### Requirement 22: Input Validation
✅ **Fulfilled**: PHP validation on all forms  
**Evidence**:
- Email format validation
- Password length validation (minimum 6 characters)
- Required field checks
- Phone number format validation (8 digits)
- Unique email enforcement

#### Requirement 23: Search by Combination
✅ **Fulfilled**: Multi-field search forms  
**Evidence**: Can search by multiple criteria simultaneously, partial terms work on all fields

#### Requirement 24: Partial Search Terms
✅ **Fulfilled**: LIKE queries with wildcards  
**Evidence**: Searching "MB" finds MB001, MB002, etc.

#### Requirement 25: Rent Notification
✅ **Fulfilled**: rent.php success message  
**Evidence**: Displays start time and cost per hour when rental created

#### Requirement 26: Return Notification
✅ **Fulfilled**: return.php success message  
**Evidence**: Displays total cost calculated from duration and rate

#### Requirement 27: Good Interfaces
✅ **Fulfilled**: Clean, modern UI  
**Evidence**:
- Consistent styling with CSS variables
- Clear navigation based on role
- Responsive design
- User-friendly forms
- Success/error message boxes

---

### 5.3 Summary

**Total Requirements**: 27  
**Requirements Met**: 27 (100%)  
**Status**: ✅ All assignment requirements successfully implemented

---

## 6. Conclusion

### 6.1 Project Summary

The MotoCity application successfully fulfills all assignment requirements by implementing a comprehensive motorbike rental management system using Object-Oriented PHP and MySQL. The system provides distinct functionality for both regular users and administrators, with robust security, input validation, and an intuitive user interface.

### 6.2 Key Achievements

✅ **Complete OOP Architecture**: 5 well-designed classes with clear responsibilities  
✅ **All 27 Requirements Met**: 100% requirement fulfillment  
✅ **Secure Authentication**: Password hashing and session management  
✅ **Advanced Search**: Multi-field search with partial term support  
✅ **Accurate Cost Calculation**: Real-time calculation with minimum charge policy  
✅ **Professional UI/UX**: Clean, modern design with role-based navigation  
✅ **Comprehensive Error Handling**: Validation and user-friendly error messages  
✅ **Database Integrity**: Proper foreign keys and referential integrity  

### 6.3 Technical Highlights

**Design Patterns**:
- Singleton pattern for database connection
- MVC-inspired architecture
- Separation of concerns

**Security Features**:
- Password hashing with `password_hash()`
- SQL injection prevention with prepared statements
- Session-based authentication
- Role-based access control

**User Experience**:
- Responsive design
- Clear visual feedback
- Intuitive navigation
- Consistent styling

### 6.4 Testing

The application has been thoroughly tested with:
- Multiple user accounts (Admin and User types)
- Various rental scenarios
- Search functionality with different criteria
- Edge cases (minimum charge, duplicate rentals, etc.)
- Form validation
- Database operations

### 6.5 Future Enhancements

Potential improvements for future versions:
- Payment integration
- Email notifications
- Motorbike availability calendar
- User ratings and reviews
- Mobile app version
- Advanced reporting and analytics

### 6.6 Final Statement

The MotoCity application demonstrates proficiency in:
- Object-Oriented PHP programming
- MySQL database design and implementation
- Web application development
- User interface design
- Security best practices

The project is production-ready and successfully meets all educational objectives of the ISIT307 assignment.

---

## 7. Appendices

### Appendix A: Screenshots

**Note**: Please insert screenshots for the following:

1. Login Page
2. Registration Page
3. User Dashboard
4. Admin Dashboard
5. Motorbikes List (User View)
6. Motorbikes List (Admin View with Filters)
7. Search Results
8. Rent Page with Success Notification
9. Return Page with Cost Calculation
10. Rental History
11. Add Motorbike Form
12. Edit Motorbike Form
13. Users List
14. User Search Results
15. Users Currently Renting

### Appendix B: Database Schema Diagram

[Insert ER diagram showing relationships between users, motorbikes, and rentals tables]

### Appendix C: Code Samples

Key code snippets demonstrating OOP principles, security measures, and core functionality can be found in the respective class files in the `classes/` directory.

---

**End of Report**

---

**Document Information**:
- **Project**: MotoCity Motorbike Rental System
- **Course**: ISIT307
- **Assignment**: Assignment 2
- **Date**: February 15, 2026
- **Repository**: https://github.com/Tiarasxm/ISIT307-A2
