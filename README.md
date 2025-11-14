# Booking Management Application

A modern, full-stack booking management system that allows users to efficiently manage appointments and client bookings with intelligent overlap prevention and weekly calendar views.

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
- **MySQL** - Robust relational database for data persistence
- **RESTful API** - Clean, resourceful API architecture

### Frontend
- **[Vue 3](https://vuejs.org/)** - Progressive JavaScript framework with Composition API
- **[TypeScript](https://www.typescriptlang.org/)** - Type-safe JavaScript for better code quality
- **[Inertia.js](https://inertiajs.com/)** - Modern monolith approach (no separate API layer needed)
- **[Shadcn/ui](https://ui.shadcn.com/)** - Beautiful, accessible component library
- **[Tailwind CSS](https://tailwindcss.com/)** - Utility-first CSS framework
- **[Vite](https://vitejs.dev/)** - Lightning-fast build tool

### Testing
- **PHPUnit** - PHP testing framework
- **Feature Tests** - Complete test coverage (12 tests, 30 assertions)
- **SQLite** - In-memory database for fast testing

## Installation

### Prerequisites

Ensure you have the following installed:
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 5.7+ or MariaDB
- Web server (Apache/Nginx) or Laravel Valet/Herd

### Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd booking
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   ```

   Edit `.env` and configure your database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bookingapp
   DB_USERNAME=
   DB_PASSWORD=
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```

7. **Start the application**
   ```bash
   php artisan serve
   ```

   Visit: `http://localhost:8000`

### Development Mode

For local development with hot module replacement:

```bash
npm run dev
```

This will start the Vite development server with automatic reloading when you make changes.

## Usage

### First Time Setup

The application includes **auto-login for development** purposes. When you first visit the application:

- A default user is automatically created with:
  - **Email**: `admin@example.com`
  - **Password**: `password`
- You are automatically logged in
- This allows immediate testing without setting up authentication

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

All API endpoints require authentication via Laravel's session-based auth.

### Bookings API

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/bookings` | List all bookings |
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


### CI/CD Configuration

The application is configured for GitLab CI/CD:
- Automated testing on push
- Build process optimized for production
- Vite wayfinder plugin disabled in CI to prevent build failures

