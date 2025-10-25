# Laravel 11 Integration for XG-Proyect

This directory contains the Laravel 11 integration for XG-Proyect, running **parallel** to the legacy codebase.

## Architecture

XG-Proyect is being modernized using the **Strangler Fig Pattern**:

```
XG-Proyect-v3.x.x/
├── app/                    # Legacy PHP code (preserved)
├── laravel/               # New Laravel 11 code
│   ├── app/
│   │   ├── Http/Controllers/Api/V1/  # API Controllers
│   │   ├── Models/                   # Eloquent Models
│   │   ├── Services/                 # Business Logic (coming soon)
│   │   └── Repositories/             # Data Access (coming soon)
│   ├── config/                       # Laravel Configuration
│   ├── database/
│   │   └── migrations/               # PostgreSQL Migrations
│   ├── routes/
│   │   ├── api.php                   # API Routes (v1)
│   │   └── web.php                   # Web Routes
│   └── tests/                        # Unit & Feature Tests
├── public/                           # Public directory (shared)
├── storage/                          # Storage (shared)
└── artisan                           # Laravel CLI
```

## Technology Stack

- **Framework:** Laravel 11
- **PHP:** 8.3+
- **Database:** PostgreSQL 16 (primary), MySQL 8.0 (legacy)
- **Cache/Queue:** Redis 7
- **Authentication:** Laravel Sanctum
- **Testing:** PHPUnit 11, Pest

## Features

### Already Implemented

✅ **Laravel 11 Setup**
- Modern application structure
- Configuration for PostgreSQL & MySQL
- Redis caching & queue support
- Health check endpoints

✅ **Database Migrations**
- Users table with PostgreSQL Full-Text Search
- Planets table with JSONB fields
- Alliances table with Full-Text Search
- Foreign keys and proper indexing

✅ **Eloquent Models**
- `User` - with authentication, relations, and scopes
- `Planet` - with JSONB casting, resource management
- `Alliance` - with member management
- Full-Text Search support on all models

✅ **API Routes (v1)**
- Health check: `GET /api/v1/health`
- Authentication endpoints (placeholders)
- Planets API: `GET /api/v1/planets`
- Protected routes with Sanctum

✅ **Controllers**
- `PlanetController` - full CRUD example
- RESTful JSON responses
- Request validation

### Coming Soon

⏳ Service Layer (Business Logic separation)
⏳ Repository Pattern (Data Access)
⏳ API Resources (Response Transformers)
⏳ Request Validation Classes
⏳ Complete Fleet, Research, Building APIs
⏳ WebSocket Support (real-time updates)
⏳ Comprehensive Test Suite

## Setup

### Prerequisites

```bash
# PostgreSQL must be running
docker-compose -f docker-compose.dev.yml up -d postgres

# Install Composer dependencies
composer install

# Generate application key (when implemented)
php artisan key:generate
```

### Database Migrations

```bash
# Run migrations on PostgreSQL
php artisan migrate --database=pgsql

# Rollback migrations
php artisan migrate:rollback

# Refresh database (drop all tables and re-migrate)
php artisan migrate:fresh
```

### Environment Configuration

Copy the example environment file:

```bash
cp laravel/.env.example laravel/.env
```

**Key configurations:**

```env
# PostgreSQL (Primary)
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=xgp
DB_USERNAME=xgproyect
DB_PASSWORD=xgproyect

# MySQL (Legacy, for migration)
DB_LEGACY_HOST=db
DB_LEGACY_PORT=3306

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=xgproyect
```

## API Usage

### Health Check

```bash
curl http://localhost/api/v1/health
```

**Response:**
```json
{
  "status": "ok",
  "database": "connected",
  "redis": "connected",
  "timestamp": "2024-01-01T12:00:00+00:00"
}
```

### Authentication (Coming Soon)

```bash
# Register
curl -X POST http://localhost/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "user_name": "player1",
    "user_email": "player1@xgproyect.test",
    "password": "secret123"
  }'

# Login
curl -X POST http://localhost/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "user_email": "player1@xgproyect.test",
    "password": "secret123"
  }'
```

### Planets API (Requires Authentication)

```bash
# Get all user planets
curl http://localhost/api/v1/planets \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get specific planet
curl http://localhost/api/v1/planets/1 \
  -H "Authorization: Bearer YOUR_TOKEN"

# Update planet name
curl -X PUT http://localhost/api/v1/planets/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"planet_name": "New Homeworld"}'
```

## PostgreSQL Features

### JSONB Fields

Planets use JSONB for flexible data:

```php
// Store production rates as JSON
$planet->update([
    'planet_production' => [
        'metal' => 30,
        'crystal' => 15,
        'deuterium' => 0,
    ],
]);

// Query JSONB data
Planet::whereJsonContains('planet_production->metal', 30)->get();
```

### Full-Text Search

All models support PostgreSQL Full-Text Search:

```php
// Search users by name or email
User::search('john')->get();

// Search planets by name
Planet::search('homeworld')->get();

// Search alliances
Alliance::search('empire')->get();
```

### Window Functions (Coming Soon)

Leaderboards using PostgreSQL Window Functions:

```php
use Illuminate\Support\Facades\DB;

$leaderboard = DB::table('users_statistics')
    ->select([
        'user_id',
        'stat_total_points',
        DB::raw('RANK() OVER (ORDER BY stat_total_points DESC) as rank')
    ])
    ->orderByDesc('stat_total_points')
    ->limit(100)
    ->get();
```

## Development

### Artisan Commands

```bash
# List all commands
php artisan list

# Make a new model
php artisan make:model Building -m

# Make a new controller
php artisan make:controller Api/V1/FleetController --api

# Make a new migration
php artisan make:migration create_fleets_table

# Run tinker (REPL)
php artisan tinker
```

### Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=PlanetTest

# With coverage
php artisan test --coverage
```

### Code Quality

```bash
# Run PHPStan
composer phpstan

# Run Laravel Pint (code style)
composer pint

# Run all checks
composer test
```

## Models

### User Model

```php
use App\Models\User;

// Find user
$user = User::find(1);

// Get user's planets
$planets = $user->planets;

// Get home planet
$homePlanet = $user->homePlanet;

// Check if user is banned
if ($user->isBanned()) {
    // Handle banned user
}

// Check premium status
if ($user->hasPremium()) {
    // Grant premium features
}

// Search users
$users = User::search('player')->get();

// Active users only
$activeUsers = User::active()->get();
```

### Planet Model

```php
use App\Models\Planet;

// Find planet
$planet = Planet::find(1);

// Get planet owner
$owner = $planet->user;

// Update resources (calculates production)
$planet->updateResources();

// Check if planet has enough resources
if ($planet->hasResources(1000, 500, 100)) {
    $planet->deductResources(1000, 500, 100);
}

// Search planets
$planets = Planet::search('earth')->get();

// Get planets in galaxy
$planets = Planet::inGalaxy(1, 100)->get();

// Only planets (no moons)
$planets = Planet::onlyPlanets()->get();
```

### Alliance Model

```php
use App\Models\Alliance;

// Find alliance
$alliance = Alliance::find(1);

// Get members
$members = $alliance->members;

// Get owner
$owner = $alliance->owner;

// Check if accepting applications
if ($alliance->acceptsApplications()) {
    // Show apply button
}

// Search alliances
$alliances = Alliance::search('empire')->get();
```

## Migration from Legacy

The Laravel application runs **parallel** to the legacy codebase:

1. **Legacy routes** continue to work (`/game.php`, `/admin.php`, etc.)
2. **New API routes** are available (`/api/v1/*`)
3. **Shared database** - both systems use the same PostgreSQL database
4. **Gradual migration** - features are migrated one by one

### Migration Command (Coming Soon)

```bash
# Migrate data from MySQL to PostgreSQL
php artisan migrate:from-mysql

# Migrate specific tables
php artisan migrate:from-mysql --table=users --table=planets

# With progress
php artisan migrate:from-mysql --verbose
```

## Performance

### Caching

```php
use Illuminate\Support\Facades\Cache;

// Cache user planets for 1 hour
$planets = Cache::remember("user.{$userId}.planets", 3600, function () use ($userId) {
    return Planet::where('user_id', $userId)->get();
});
```

### Query Optimization

```php
// Eager loading to avoid N+1 queries
$users = User::with(['planets', 'alliance'])->get();

// Select specific columns
$users = User::select(['user_id', 'user_name'])->get();

// Use chunking for large datasets
User::chunk(1000, function ($users) {
    foreach ($users as $user) {
        // Process user
    }
});
```

## Documentation

- **Phase 1 Plan:** `/docs/PHASE_1_PLAN.md`
- **PostgreSQL Migration:** `/docs/POSTGRESQL_MIGRATION.md`
- **Modernization Overview:** `/MODERNIZATION.md`

## Support

- **Issues:** https://github.com/XGProyect/XG-Proyect-v3.x.x/issues
- **Laravel Docs:** https://laravel.com/docs/11.x
- **PostgreSQL Docs:** https://www.postgresql.org/docs/16/

## License

GPL-3.0-only (same as main project)
