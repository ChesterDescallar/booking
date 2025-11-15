# Booking Management Application

A modern, full-stack booking management system that allows users to efficiently manage appointments and client bookings with intelligent overlap prevention and weekly calendar views.

## Quick Start

Get started in 5 minutes:

```bash
# 1. Install dependencies
composer install && npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Configure for SQLite (edit .env)
# Set: DB_CONNECTION=sqlite

# 4. Create database and seed data
touch database/database.sqlite
php artisan migrate:fresh --seed --seeder=DemoDataSeeder

# 5. Start servers (in separate terminals)
php artisan serve    # Terminal 1
npm run dev          # Terminal 2

# 6. Open http://localhost:8000
```

## Overview

This application provides a complete solution for managing bookings with real-time validation, preventing scheduling conflicts, and maintaining client relationships. Built with modern web technologies, it offers a seamless user experience with a clean, intuitive interface.

## Features

### 📅 Booking Management
- **Create, Edit, and Delete Bookings** - Full CRUD operations for managing appointments
- **Overlap Prevention** - Automatic detection and prevention of conflicting bookings
- **Weekly Calendar View** - Filter and view bookings by week with date picker
- **Real-time Validation** - Immediate feedback with modal-based error display
- **User & Client Assignment** - Associate bookings with specific users and clients

### 👥 Client Management
- **Client Database** - Maintain a complete database of clients
- **Contact Information** - Store client names, emails, and phone numbers
- **Quick Selection** - Dropdown selection for easy booking assignment

### 💡 User Interface
- **Modern Design** - Clean, professional interface with Shadcn UI components
- **Responsive Tables** - Sortable and filterable data tables
- **Modal Forms** - Intuitive modal-based booking creation and editing
- **Date Pickers** - Easy-to-use date and time selection
- **Error Handling** - Clear, user-friendly error messages displayed in context

## Technology Stack

### Backend
- **[Laravel 12](https://laravel.com/)** - Latest PHP framework with modern features
- **PHP 8.2+** - Modern PHP with type safety and performance improvements
- **SQLite** - Lightweight, zero-configuration database (easily switchable to MySQL/PostgreSQL)
- **RESTful API** - Clean, resourceful API architecture with Laravel API Resources

### Frontend
- **[Vue 3](https://vuejs.org/)** - Progressive JavaScript framework with Composition API
- **[TypeScript](https://www.typescriptlang.org/)** - Type-safe JavaScript for better code quality
- **[Inertia.js](https://inertiajs.com/)** - Modern monolith approach (no separate API layer needed)
- **[Shadcn/ui](https://ui.shadcn.com/)** - Beautiful, accessible component library
- **[Tailwind CSS](https://tailwindcss.com/)** - Utility-first CSS framework
- **[Vite](https://vitejs.dev/)** - Lightning-fast build tool

### Testing
- **PHPUnit** - PHP testing framework
- **Feature Tests** - Complete test coverage (11 tests, 29 assertions)
- **SQLite** - In-memory database for fast testing

## Installation

### Prerequisites

Ensure you have the following installed:
- PHP 8.2 or higher
- Composer
- Node.js 20+ and npm

### Quick Start (Recommended - No XAMPP Required)

This is the easiest way to get started using Laravel's built-in server and SQLite:

#### 1. **Clone the repository**
```bash
git clone <repository-url>
cd booking
```

#### 2. **Install dependencies**
```bash
composer install
npm install
```

#### 3. **Configure environment**
```bash
# Windows
copy .env.example .env

# Linux/Mac
cp .env.example .env
```

Then generate the application key:
```bash
php artisan key:generate
```

#### 4. **Update .env file for SQLite**

Edit the `.env` file and update the following settings:

```env
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# Comment out or remove these MySQL settings:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=bookingapp
# DB_USERNAME=root
# DB_PASSWORD=
```

#### 5. **Create SQLite database**
```bash
# Windows
type nul > database\database.sqlite

# Linux/Mac
touch database/database.sqlite
```

#### 6. **Set up demo database**

Run the database migrations and seed demo data:

MAKE SURE TO UPDATE YOUR DB_DATABASE to point to the newly generated database\database.sqlite -> Right click on the file copy path and paste it on the env

**Windows:**
```bash
setup-demo.bat
```

**Linux/Mac:**
```bash
chmod +x setup-demo.sh
./setup-demo.sh
```

**Or manually:**
```bash
php artisan migrate:fresh
php artisan db:seed --class=DemoDataSeeder
```

#### 7. **Start the development servers**

Open two terminal windows in the project directory:

**Terminal 1 - Laravel server:**
```bash
php artisan serve
```

**Terminal 2 - Vite dev server (for hot reload):**
```bash
npm run dev
```

#### 8. **Access the application**

Open your browser and navigate to:
```
http://localhost:8000
```

The application will automatically log you in as **admin@example.com** in development mode.

#### 9. **Stopping the servers**

To stop the development servers:
- Press `Ctrl+C` in each terminal window
- Or simply close the terminal windows

---

### API Access

The application provides RESTful API endpoints for managing bookings, clients, and users.

**Base URL:** `http://localhost:8000`

**Authentication:** API routes require session-based authentication. Access the home page first to get authenticated, then use the session cookie for API calls.

**Example API endpoints:**

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/bookings` | GET | Get all bookings |
| `/api/bookings` | POST | Create a new booking |
| `/api/bookings/1` | GET | Get specific booking |
| `/api/bookings/1` | PUT | Update a booking |
| `/api/bookings/1` | DELETE | Delete a booking |
| `/api/bookings?week=2025-01-15` | GET | Filter bookings by week |
| `/api/clients` | GET | Get all clients |
| `/api/users` | GET | Get all users |

**Testing with cURL:**
```bash
# First, get authenticated
curl -c cookies.txt http://localhost:8000

# Then use the session for API calls
curl -b cookies.txt http://localhost:8000/api/bookings
curl -b cookies.txt http://localhost:8000/api/clients
```

**Testing with Postman:**
1. Base URL: `http://localhost:8000`
2. First visit `http://localhost:8000` in your browser to authenticate
3. Copy the session cookie from browser dev tools
4. Add the cookie to Postman headers or use Postman's cookie manager
5. Make API requests to `/api/bookings`, `/api/clients`, etc.

---

### Alternative Setup: Using MySQL

If you prefer MySQL over SQLite:

#### Prerequisites
- MySQL 5.7+ or MariaDB 10.3+ (standalone or via XAMPP)

#### Configuration

**1. Create MySQL database:**

Using MySQL command line or phpMyAdmin:
```sql
CREATE DATABASE bookingapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**2. Update .env file:**
```env
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookingapp
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

**3. Run migrations and seeders:**
```bash
php artisan migrate:fresh
php artisan db:seed --class=DemoDataSeeder
```

**4. Start the server:**
```bash
php artisan serve
```

Visit: `http://localhost:8000`

---

### Alternative Setup: XAMPP (Legacy)

If you must use XAMPP:

**1. Place project in XAMPP directory:**
```bash
cd c:\xampp\htdocs\dashboard\projects\bookingapplication
```

**2. Update .env:**
```env
APP_URL=http://localhost/dashboard/projects/bookingapplication/booking/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookingapp
DB_USERNAME=root
DB_PASSWORD=
```

**3. Create database via phpMyAdmin** at `http://localhost/phpmyadmin`

**4. Run migrations:**
```bash
php artisan migrate:fresh
php artisan db:seed --class=DemoDataSeeder
```

**5. Access at:**
```
http://localhost/dashboard/projects/bookingapplication/booking/public
```

## Development Workflow

### Daily Development Routine

1. **Start the servers** (in project directory):
   ```bash
   # Terminal 1
   php artisan serve

   # Terminal 2
   npm run dev
   ```

2. **Access the application**: Open `http://localhost:8000` in your browser

3. **Make changes**: Edit files and see changes reflected immediately with hot reload

4. **Stop the servers**: Press `Ctrl+C` in each terminal when done

### Common Commands

```bash
# Database operations
php artisan migrate:fresh        # Reset database
php artisan db:seed --class=DemoDataSeeder  # Seed demo data
php artisan migrate:fresh --seed --seeder=DemoDataSeeder  # Reset + seed

# Testing
php artisan test                 # Run all tests
php artisan test --filter=BookingTest  # Run specific tests

# Cache clearing
php artisan cache:clear          # Clear application cache
php artisan config:clear         # Clear config cache
php artisan route:clear          # Clear route cache

# Build for production
npm run build                    # Compile production assets
```

---

## Usage

### Demo Data

The application comes pre-loaded with demo data for immediate testing:

**5 Demo Users** (Password: `password` for all):
- john@example.com (John Smith)
- sarah@example.com (Sarah Johnson)
- michael@example.com (Michael Brown)
- emily@example.com (Emily Davis)
- admin@example.com (Admin User) - Auto-login in development

**5 Demo Clients:**
- Acme Corporation
- Tech Solutions Inc
- Global Enterprises
- Innovate Labs
- Creative Studios

**7 Sample Bookings** spanning the next 8 days with various users and clients.

The application includes **auto-login for development** - you'll be automatically logged in as admin@example.com when you visit the app

### Creating a Booking

1. Click the **"New Booking"** button
2. Fill in the booking details:
   - **Title**: A descriptive name for the booking
   - **Description**: Optional additional details
   - **Client**: Select from existing clients
   - **User**: Select the responsible user
   - **Start Date/Time**: When the booking begins
   - **End Date/Time**: When the booking ends
3. Click **"Save"**

The system will automatically check for overlapping bookings and display an error in the modal if a conflict is detected.

### Managing Clients

- View all clients in the clients section
- Add new clients with name, email, and phone
- Edit or delete existing clients
- Clients can be assigned to multiple bookings

### Filtering by Week

- Use the date picker to select any date
- The application displays all bookings for that week (Monday-Sunday)
- Week boundaries are calculated automatically

## API Documentation


**Base URL:** `http://localhost:8000` (when using `php artisan serve`)

### Authentication

The API uses Laravel's session-based authentication:
1. Visit `http://localhost:8000` in your browser (auto-login as admin@example.com in development)
2. Use the session cookie for subsequent API calls
3. For testing, you can use tools like Postman with cookie management or cURL with `-b cookies.txt`

### Bookings API

**Endpoints:**

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/bookings` | List all bookings (sorted by start_time DESC) |
| GET | `/api/bookings?week=YYYY-MM-DD` | Filter bookings by week |
| POST | `/api/bookings` | Create a new booking |
| GET | `/api/bookings/{id}` | Get specific booking details |
| PUT | `/api/bookings/{id}` | Update a booking |
| DELETE | `/api/bookings/{id}` | Delete a booking |

**Booking Response Format:**
```json
{
  "id": 1,
  "title": "Client Meeting",
  "description": "Quarterly review",
  "user_id": 1,
  "client_id": 5,
  "start_time": "2025-01-15T10:00:00.000000Z",
  "end_time": "2025-01-15T11:00:00.000000Z",
  "created_at": "2025-01-10T12:00:00.000000Z",
  "updated_at": "2025-01-10T12:00:00.000000Z",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com"
  },
  "client": {
    "id": 5,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "555-0123"
  }
}
```

### Clients API

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/clients` | List all clients |
| POST | `/api/clients` | Create a new client |
| GET | `/api/clients/{id}` | Get specific client |
| PUT | `/api/clients/{id}` | Update a client |
| DELETE | `/api/clients/{id}` | Delete a client |

### Users API

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/users` | List all users |

## Troubleshooting

### Common Issues

**Port 8000 already in use:**
```bash
# Use a different port
php artisan serve --port=8001
```

**Database locked (SQLite):**
```bash
# Close any database browsers (DB Browser for SQLite, etc.)
# Or restart the server
```

**Vite not connecting:**
```bash
# Make sure npm run dev is running
# Check that you're accessing http://localhost:8000 (not 127.0.0.1)
```

**Session/Auth issues:**
```bash
# Clear cache and sessions
php artisan cache:clear
php artisan config:clear

# Check APP_KEY is set in .env
php artisan key:generate
```

**Database migration errors:**
```bash
# Reset the database
php artisan migrate:fresh --seed --seeder=DemoDataSeeder
```

**CSS/JS not loading:**
```bash
# Rebuild assets
npm run build

# Or run dev server
npm run dev
```

---

## Testing

### Running Tests

Run the complete test suite:
```bash
php artisan test
```

Run specific test classes:
```bash
php artisan test --filter=BookingTest
```

Run with coverage:
```bash
php artisan test --coverage
```

### Booking Overlap Prevention

The application uses a custom validation rule ([NoBookingOverlap.php](app/Rules/NoBookingOverlap.php)) that:
- Checks for conflicting time ranges in existing bookings
- Excludes the current booking when updating (allows editing without false conflicts)
- Returns clear, user-friendly error messages
- Validates at the database level for data integrity

### Modal Error Display

Validation errors appear directly in the booking modal:
- Errors display below form fields, above action buttons
- Clear, actionable error messages
- Automatic clearing when modal closes
- No page reload required

### Weekly Filtering

The weekly filtering feature:
- Accepts any date as input
- Calculates the week boundaries (Monday start, Sunday end)
- Returns all bookings within that week
- Includes week start/end dates in response

