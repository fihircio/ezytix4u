# Event Portal - Event Management System

## Project Overview
This is an event management system built with Laravel, designed to handle event creation, management, and ticketing. The system provides a comprehensive solution for event organizers and attendees.

## Features
- Event Creation and Management
- Ticket Sales and Management
- User Authentication and Authorization
- Event Categories and Tags
- Search and Filtering
- Admin Dashboard
- Reporting and Analytics

## Technical Stack
- Laravel (PHP Framework)
- MySQL Database
- Vue.js (Frontend)
- Bootstrap (UI Framework)
- Eventmie Pro Integration

## Setup Instructions

### Prerequisites
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL
- Web Server (Apache/Nginx)

### Installation Steps
1. Clone the repository
```bash
git clone [repository-url]
cd [project-directory]
```

2. Install PHP dependencies
```bash
composer install
```

3. Install Node.js dependencies
```bash
npm install
```

4. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure database in .env file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations and seeders
```bash
php artisan migrate --seed
```

7. Compile assets
```bash
npm run dev
```

8. Start the development server
```bash
php artisan serve
```

## Development Guidelines

### Code Style
- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Add comments for complex logic
- Write unit tests for new features

### Git Workflow
1. Create feature branches from develop
2. Use meaningful commit messages
3. Create pull requests for code review
4. Ensure all tests pass before merging

### Testing
```bash
php artisan test
```

## Architecture Overview

### Directory Structure
- `app/` - Core application code
- `config/` - Configuration files
- `database/` - Migrations and seeders
- `resources/` - Views and assets
- `routes/` - Route definitions
- `tests/` - Test files

### Key Components
- Controllers: Handle HTTP requests
- Models: Database interactions
- Services: Business logic
- Middleware: Request filtering
- Events: Event handling
- Listeners: Event subscribers

## API Documentation
API documentation is available at `/api/documentation` when running the application.

## Database Schema
The database schema is defined in the migration files under `database/migrations/`.

## Deployment Instructions

### Production Setup
1. Set up production environment variables
2. Configure web server (Apache/Nginx)
3. Set up SSL certificate
4. Configure database
5. Run migrations
6. Compile assets for production
```bash
npm run production
```

### Maintenance
- Regular backups
- Security updates
- Performance monitoring
- Error logging

## Support and Contact
For support or questions, please contact [your-email@domain.com]

## License
This project is licensed under the MIT License - see the LICENSE file for details.
