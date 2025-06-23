# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Development Server
```bash
composer dev
```
Runs the full development stack including:
- PHP Artisan server
- Queue listener
- Pail (log viewer)
- Vite dev server

### Individual Commands
- `php artisan serve` - Start Laravel development server
- `php artisan queue:listen --tries=1` - Start queue worker
- `php artisan pail --timeout=0` - View logs in real-time
- `npm run dev` - Start Vite development server for frontend assets
- `npm run build` - Build frontend assets for production

### Testing & Code Quality
- `composer test` - Run full test suite (includes type coverage, rector, lint, static analysis, unit tests)
- `composer test:unit` - Run unit tests with coverage
- `composer test:types` - Run PHPStan static analysis
- `composer test:lint` - Run Laravel Pint code style checks
- `composer test:rector` - Run Rector refactoring checks (dry-run)
- `composer test:type-coverage` - Check type coverage (minimum 100%)

### Code Formatting & Analysis
- `composer lint` - Fix code style issues with Laravel Pint
- `composer rector` - Run Rector refactoring

### Code Style
- **PHP**: Class names PascalCase, methods/properties camelCase, Strict mode enabled
- **Formatting**: Single quotes, semicolons required, 150 char width, 4-space tabs (2 for YAML)
- **Components**: Livewire Volt, Tailwind CSS, maryUI components
- **Error Handling**: Use Laravel's built-in exception handling for PHP, Sentry for monitoring
- **Testing**: Pest PHP with Feature/Unit directory structure
- **Linting**: PHP Pint for linting
- **Type Check**: PHP Stal (via LaraStan) for type check safety 
  
## Architecture Overview

### Core Technology Stack
- **Backend**: Laravel 12 with PHP 8.3+
- **Frontend**: Livewire 3 + Volt (functional components)
- **UI Framework**: Mary UI (built on Tailwind CSS + DaisyUI)
- **Queue**: Laravel Horizon for background job processing
- **Database**: Uses Eloquent ORM with soft deletes
- **Icon System**: Multiple Blade icon packages (Huge Icons, Remix Icons, etc.)

### Key Models & Relationships
- **User**: Has many Categories and Habits; supports invitation-based registration with admin role
- **Category**: Belongs to User, has many Habits (with slugs via Spatie Sluggable)
- **Habit**: Belongs to Category, Unit, and Period; has many HabitEntries
- **HabitEntry**: Tracks individual habit completions with timestamps and notes
- **Unit/Period**: Reference data for habit measurements and recurrence

### Architecture Patterns
- **Service Layer**: Business logic in Services (CategoryService, HabitService, etc.)
- **Data Transfer**: Uses Spatie Laravel Data for type-safe DTOs
- **Caching**: Extensive use of tagged cache for performance
- **Queue Jobs**: NotificationsSummaryJob for daily Pushover notifications
- **Console Commands**: Menu-based CLI interface for data management

### Frontend Architecture
- **Livewire Components**: Located in `resources/views/livewire/`
- **Volt Components**: Functional Livewire components using Volt syntax
- **Mary UI Components**: Pre-built components for consistent design
- **Real-time Updates**: Livewire provides reactive UI without page reloads

### Key Features
- **Habit Tracking**: Users can define habits with target values, units, and periods
- **Progress Visualization**: Track completion history and streaks
- **Notification System**: Daily summary notifications via Pushover
- **Invitation System**: Admin-controlled user registration
- **Multi-tenant**: Each user has isolated data

### Console Interface
The application includes a sophisticated console interface (`app/Console/`) with:
- Menu-based navigation system
- Task-based operations for CRUD operations
- Colorized output using traits

### Background Processing
- **Laravel Horizon**: Manages Redis-based queues
- **Daily Notifications**: Automated job scheduling for user engagement
- **Cache Management**: Tagged cache invalidation for data consistency

### Configuration Files
- `phpstan.neon` - Static analysis configuration
- `pint.json` - Code style configuration  
- `phpunit.xml` - Test configuration
- `rector.php` - Refactoring rules
- `deploy.php` - Deployment configuration (Deployer)

### Testing
- **Pest PHP**: Modern testing framework
- **Type Coverage**: 100% type coverage requirement
- **Parallel Testing**: Tests run in parallel for speed
- **Factory Pattern**: Database factories for test data generation
