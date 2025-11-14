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
- **SQLite** - Lightweight database perfect for demos and small deployments (easily switchable to MySQL/PostgreSQL)
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
- PHP 8.2 or higher with SQLite extension
- Composer
- Node.js 20+ and npm

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
   php artisan key:generate
   ```

4. **Set up demo database**

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
   touch database/database.sqlite
   php artisan migrate:fresh
   php artisan db:seed --class=DemoDataSeeder
   ```

5. **Build frontend assets**
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


## Deployment

### Using SQLite in Production

SQLite is already configured and ready to deploy! It's perfect for:
- Demo applications
- Small to medium traffic sites
- Single-server deployments
- Easy backups (just copy the database file)

To use SQLite in production, your `.env` already has:
```env
DB_CONNECTION=sqlite
```

The database file is at `database/database.sqlite` and is automatically created when you run migrations.

### Switching to PostgreSQL/MySQL

For high-traffic production environments, update `.env`:

```env
DB_CONNECTION=pgsql  # or mysql
DB_HOST=your-host
DB_PORT=5432        # or 3306 for MySQL
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

Then run:
```bash
php artisan migrate:fresh
php artisan db:seed --class=DemoDataSeeder
```

### Deployment Platforms

**Recommended Platforms:**

1. **Railway.app** (Easiest)
   - Connects to GitHub automatically
   - Auto-detects Laravel projects
   - Provides free PostgreSQL database
   - One-click deployment
   - Free tier available

2. **Render.com**
   - Free tier available
   - Auto-deploy from GitHub
   - Built-in database options
   - Simple configuration

3. **Fly.io**
   - Free allowance
   - Global deployment
   - Good for SQLite apps
   - Simple CLI: `fly launch && fly deploy`

4. **DigitalOcean App Platform**
   - $5/month
   - Easy Laravel deployment
   - Managed databases available

### Deployment Checklist

Before deploying:
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate new `APP_KEY`: `php artisan key:generate`
- [ ] Run `npm run build` to compile production assets
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed demo data: `php artisan db:seed --class=DemoDataSeeder`
- [ ] Disable auto-login in routes/web.php for production

### CI/CD Configuration

The application is ready for CI/CD:
- Vite wayfinder plugin conditionally loads (dev only)
- Tests run on SQLite in-memory database
- Build process optimized for production
- All assets compile without external dependencies

