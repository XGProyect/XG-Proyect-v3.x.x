# Phase 1: Laravel Integration & PostgreSQL Migration - Detailplan

## Übersicht

Phase 1 umfasst die vollständige Migration zu Laravel 11 mit PostgreSQL als primärer Datenbank. Diese Phase bildet das Fundament für alle weiteren Modernisierungen.

**Dauer:** 6-8 Wochen (mit 2-3 Entwicklern)
**Ziel:** Laravel 11 + PostgreSQL + Eloquent ORM + REST API Basis

---

## Warum PostgreSQL?

### Vorteile gegenüber MySQL:

| Feature | PostgreSQL | MySQL 8.0 |
|---------|-----------|-----------|
| **ACID Compliance** | ✅ Vollständig | ⚠️ Teilweise (InnoDB) |
| **JSON Support** | ✅ Native JSONB (binär, schnell) | ✅ JSON (langsamer) |
| **Full-Text Search** | ✅ Integriert, mehrsprachig | ⚠️ Basis-Funktionen |
| **Window Functions** | ✅ Umfangreich | ✅ Ab 8.0 |
| **Arrays** | ✅ Native Arrays | ❌ Keine nativen Arrays |
| **Materialized Views** | ✅ Ja | ❌ Nein |
| **GIS/Spatial Data** | ✅ PostGIS (beste Lösung) | ⚠️ Basis-Support |
| **CTEs (WITH)** | ✅ Recursive CTEs | ✅ Ab 8.0 |
| **Performance** | ✅ Besser bei komplexen Queries | ✅ Besser bei einfachen Reads |
| **Concurrency** | ✅ MVCC (Multi-Version) | ⚠️ Locking |
| **Extensions** | ✅ 1000+ (pg_stat_statements, pgcrypto, etc.) | ⚠️ Limitiert |
| **Licensing** | ✅ PostgreSQL License (liberal) | ⚠️ GPL (Oracle) |
| **Community** | ✅ Open Source Community | ⚠️ Oracle-kontrolliert |

### Spezifische Vorteile für XG-Proyect:

1. **Leaderboards & Rankings:**
   - Window Functions (RANK(), DENSE_RANK(), ROW_NUMBER())
   - Schnellere Statistik-Queries

2. **Battle Reports:**
   - JSONB für flexible Report-Struktur
   - Schnellere JSON-Queries mit Indexierung

3. **Galaxy Search:**
   - Full-Text Search für Planeten/User-Suche
   - GIS für Galaxy-Koordinaten (optional)

4. **Concurrent Fleet Operations:**
   - Bessere Concurrency durch MVCC
   - Keine Deadlocks bei Fleet-Updates

5. **Future-Proof:**
   - Erweiterbar mit Extensions
   - GraphQL Support (PostGraphile)

---

## Phase 1.1: Laravel 11 Installation & Setup (Woche 1-2)

### Task 1.1.1: Laravel 11 Projekt erstellen

```bash
# Neues Laravel 11 Projekt in separatem Verzeichnis
composer create-project laravel/laravel:^11.0 laravel-xgp

# Oder in bestehendes Projekt integrieren (empfohlen)
composer require laravel/framework:^11.0
```

**Struktur:**
```
XG-Proyect-v3.x.x/
├── app/                    # Legacy Code (behalten)
├── laravel/                # Neuer Laravel Code (NEU)
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   ├── Middleware/
│   │   │   └── Requests/
│   │   ├── Models/
│   │   ├── Services/
│   │   ├── Repositories/
│   │   └── Enums/
│   ├── config/
│   ├── database/
│   │   ├── migrations/
│   │   ├── seeders/
│   │   └── factories/
│   ├── routes/
│   │   ├── web.php
│   │   ├── api.php
│   │   └── console.php
│   └── tests/
│       ├── Unit/
│       └── Feature/
├── public/                 # Bestehendes public
└── storage/               # Bestehendes storage
```

**Dateien erstellen:**

1. **laravel/bootstrap/app.php**
```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

2. **laravel/config/app.php** (Laravel 11 vereinfacht)
```php
<?php

return [
    'name' => env('APP_NAME', 'XG Proyect'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'cipher' => 'AES-256-CBC',
];
```

### Task 1.1.2: PostgreSQL Konfiguration

**docker-compose.dev.yml erweitern:**
```yaml
services:
  postgres:
    image: postgres:16-alpine
    container_name: xgproyect-postgres-dev
    restart: unless-stopped
    environment:
      POSTGRES_DB: xgp
      POSTGRES_USER: xgproyect
      POSTGRES_PASSWORD: xgproyect
      POSTGRES_INITDB_ARGS: "--encoding=UTF8 --locale=C"
    ports:
      - "5432:5432"
    volumes:
      - postgres_data_dev:/var/lib/postgresql/data
      - ./database/postgres:/docker-entrypoint-initdb.d
    command:
      - "postgres"
      - "-c"
      - "max_connections=200"
      - "-c"
      - "shared_buffers=256MB"
      - "-c"
      - "effective_cache_size=1GB"
      - "-c"
      - "work_mem=16MB"
      - "-c"
      - "maintenance_work_mem=64MB"
    networks:
      - xgproyect-dev

  pgadmin:
    image: dpage/pgadmin4:latest
    container_name: xgproyect-pgadmin-dev
    restart: unless-stopped
    environment:
      PGADMIN_DEFAULT_EMAIL: admin@xgproyect.local
      PGADMIN_DEFAULT_PASSWORD: admin
    ports:
      - "5050:80"
    depends_on:
      - postgres
    networks:
      - xgproyect-dev

volumes:
  postgres_data_dev:
```

**laravel/config/database.php:**
```php
<?php

return [
    'default' => env('DB_CONNECTION', 'pgsql'),

    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'postgres'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'xgp'),
            'username' => env('DB_USERNAME', 'xgproyect'),
            'password' => env('DB_PASSWORD', 'xgproyect'),
            'charset' => 'utf8',
            'prefix' => env('DB_PREFIX', 'xgp_'),
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'db'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'xgp'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', 'root'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('DB_PREFIX', 'xgp_'),
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', 'xgp:'),
        ],
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', 'redis'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', 'redis'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],
];
```

**.env Update:**
```bash
# Database - PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=xgp
DB_USERNAME=xgproyect
DB_PASSWORD=xgproyect
DB_PREFIX=xgp_

# Legacy MySQL (für Migration)
DB_LEGACY_CONNECTION=mysql
DB_LEGACY_HOST=db
DB_LEGACY_PORT=3306
DB_LEGACY_DATABASE=xgp
DB_LEGACY_USERNAME=root
DB_LEGACY_PASSWORD=root
```

### Task 1.1.3: Dockerfile PostgreSQL-Support

**Dockerfile.optimized erweitern:**
```dockerfile
# Install PostgreSQL extension
RUN docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    opcache \
    zip \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pgsql
```

### Task 1.1.4: Composer Dependencies

**composer.json erweitern:**
```json
{
    "require": {
        "php": "^8.3",
        "laravel/framework": "^11.0",
        "laravel/sanctum": "^4.0",
        "laravel/telescope": "^5.0",
        "predis/predis": "^2.2",
        "ext-pdo": "*",
        "ext-pgsql": "*",
        "ext-redis": "*"
    },
    "require-dev": {
        "laravel/pint": "^1.15",
        "laravel/sail": "^1.29",
        "phpstan/phpstan": "^1.11",
        "larastan/larastan": "^2.9",
        "phpunit/phpunit": "^11.0",
        "fakerphp/faker": "^1.23",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.1"
    }
}
```

---

## Phase 1.2: Database Migrations (Woche 2-3)

### Task 1.2.1: Migration-Dateien generieren

**Alle 21 Tabellen als Migrations:**

```bash
# Users
php artisan make:migration create_users_table
php artisan make:migration create_users_statistics_table
php artisan make:migration create_preferences_table
php artisan make:migration create_sessions_table

# Planets
php artisan make:migration create_planets_table
php artisan make:migration create_buildings_table
php artisan make:migration create_ships_table
php artisan make:migration create_defenses_table

# Fleet
php artisan make:migration create_fleets_table
php artisan make:migration create_acs_table

# Research
php artisan make:migration create_research_table

# Reports
php artisan make:migration create_reports_table

# Alliance
php artisan make:migration create_alliances_table
php artisan make:migration create_alliance_statistics_table

# Social
php artisan make:migration create_buddys_table
php artisan make:migration create_messages_table
php artisan make:migration create_notes_table

# Admin
php artisan make:migration create_banned_table
php artisan make:migration create_changelog_table
php artisan make:migration create_languages_table
php artisan make:migration create_options_table
php artisan make:migration create_premium_table
```

### Task 1.2.2: Beispiel-Migration (Users)

**laravel/database/migrations/2024_01_01_000001_create_users_table.php:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('user_name', 32)->unique();
            $table->string('user_email', 64)->unique();
            $table->string('user_password', 255);

            // Account Info
            $table->string('user_ip', 45)->nullable();
            $table->timestamp('user_registration')->useCurrent();
            $table->timestamp('user_lastlogin')->nullable();
            $table->boolean('user_banned')->default(false);
            $table->timestamp('user_banned_until')->nullable();
            $table->text('user_ban_reason')->nullable();

            // Game Settings
            $table->unsignedBigInteger('user_home_planet_id')->nullable();
            $table->unsignedBigInteger('user_current_planet_id')->nullable();
            $table->enum('user_galaxy', range(1, 9))->default(1);
            $table->enum('user_system', range(1, 499))->default(1);
            $table->enum('user_planet', range(1, 15))->default(1);

            // Officers & Premium
            $table->timestamp('premium_dark_matter_expire_time')->nullable();
            $table->boolean('premium_officier_commander')->default(false);
            $table->boolean('premium_officier_admiral')->default(false);
            $table->boolean('premium_officier_engineer')->default(false);
            $table->boolean('premium_officier_geologist')->default(false);
            $table->boolean('premium_officier_technocrat')->default(false);

            // User Preferences
            $table->string('preference_lang', 10)->default('en');
            $table->string('preference_planet_sort', 20)->default('0');
            $table->smallInteger('preference_planet_order')->default(0);
            $table->boolean('preference_spy_probes')->default(true);
            $table->boolean('preference_vacation_mode')->default(false);
            $table->timestamp('preference_vacation_mode_until')->nullable();

            // Resources
            $table->decimal('user_metal', 20, 2)->default(500);
            $table->decimal('user_crystal', 20, 2)->default(500);
            $table->decimal('user_deuterium', 20, 2)->default(0);
            $table->decimal('user_dark_matter', 20, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_name');
            $table->index('user_email');
            $table->index(['user_galaxy', 'user_system', 'user_planet']);
            $table->index('user_banned');
            $table->index('user_lastlogin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

### Task 1.2.3: PostgreSQL-spezifische Features nutzen

**Planets Migration mit JSONB:**
```php
Schema::create('planets', function (Blueprint $table) {
    $table->id('planet_id');
    $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');

    $table->string('planet_name', 20);
    $table->unsignedSmallInteger('planet_galaxy');
    $table->unsignedSmallInteger('planet_system');
    $table->unsignedSmallInteger('planet_planet');

    // PostgreSQL JSONB für flexible Datenstrukturen
    $table->jsonb('planet_fields')->nullable()->comment('Building fields data');
    $table->jsonb('planet_debris')->nullable()->comment('Debris field data');
    $table->jsonb('planet_production')->nullable()->comment('Resource production rates');

    // Full-Text Search
    $table->text('planet_search_vector')->nullable()->storedAs(
        "to_tsvector('english', coalesce(planet_name, ''))"
    );

    // Resources
    $table->decimal('planet_metal', 20, 2)->default(0);
    $table->decimal('planet_crystal', 20, 2)->default(0);
    $table->decimal('planet_deuterium', 20, 2)->default(0);

    // Timestamps
    $table->timestamp('planet_last_update')->useCurrent();
    $table->timestamps();

    // Indexes
    $table->unique(['planet_galaxy', 'planet_system', 'planet_planet']);
    $table->index(['user_id', 'planet_id']);

    // PostgreSQL-specific GIN index for JSONB
    $table->index('planet_fields', null, 'gin');
    $table->index('planet_search_vector', null, 'gin');
});
```

### Task 1.2.4: Data Migration Script

**Datenmigration von MySQL zu PostgreSQL:**

```php
// laravel/app/Console/Commands/MigrateFromMysql.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateFromMysql extends Command
{
    protected $signature = 'migrate:from-mysql
                            {--table=* : Specific tables to migrate}
                            {--chunk=1000 : Chunk size for batch processing}';

    protected $description = 'Migrate data from MySQL to PostgreSQL';

    public function handle(): int
    {
        $mysqlConnection = config('database.connections.mysql');
        $pgsqlConnection = config('database.connections.pgsql');

        $this->info('Starting migration from MySQL to PostgreSQL...');

        $tables = $this->option('table') ?: $this->getAllTables();
        $chunkSize = (int) $this->option('chunk');

        foreach ($tables as $table) {
            $this->migrateTable($table, $chunkSize);
        }

        $this->info('Migration completed!');
        return 0;
    }

    private function migrateTable(string $table, int $chunkSize): void
    {
        $this->info("Migrating table: {$table}");

        $totalRecords = DB::connection('mysql')->table($table)->count();
        $bar = $this->output->createProgressBar($totalRecords);

        DB::connection('mysql')->table($table)->orderBy('id')->chunk($chunkSize, function ($records) use ($table, $bar) {
            $data = $records->map(function ($record) {
                return (array) $record;
            })->toArray();

            DB::connection('pgsql')->table($table)->insert($data);
            $bar->advance(count($records));
        });

        $bar->finish();
        $this->newLine();
        $this->info("✓ Migrated {$totalRecords} records from {$table}");
    }

    private function getAllTables(): array
    {
        return [
            'users',
            'users_statistics',
            'preferences',
            'sessions',
            'planets',
            'buildings',
            'ships',
            'defenses',
            'fleets',
            'research',
            'reports',
            'alliances',
            'alliance_statistics',
            'buddys',
            'messages',
            'notes',
            'acs',
            'banned',
            'changelog',
            'languages',
            'options',
            'premium',
        ];
    }
}
```

**Verwendung:**
```bash
# Alle Tabellen migrieren
php artisan migrate:from-mysql

# Nur bestimmte Tabellen
php artisan migrate:from-mysql --table=users --table=planets

# Mit größeren Chunks
php artisan migrate:from-mysql --chunk=5000
```

---

## Phase 1.3: Eloquent Models (Woche 3-4)

### Task 1.3.1: Base Model

**laravel/app/Models/BaseModel.php:**
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class BaseModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the table prefix
     */
    public function getTablePrefix(): string
    {
        return config('database.connections.pgsql.prefix', 'xgp_');
    }
}
```

### Task 1.3.2: User Model

**laravel/app/Models/User.php:**
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_name',
        'user_email',
        'user_password',
        'user_ip',
        'user_galaxy',
        'user_system',
        'user_planet',
        'preference_lang',
        'user_metal',
        'user_crystal',
        'user_deuterium',
        'user_dark_matter',
    ];

    protected $hidden = [
        'user_password',
        'remember_token',
    ];

    protected $casts = [
        'user_registration' => 'datetime',
        'user_lastlogin' => 'datetime',
        'user_banned' => 'boolean',
        'user_banned_until' => 'datetime',
        'premium_dark_matter_expire_time' => 'datetime',
        'premium_officier_commander' => 'boolean',
        'premium_officier_admiral' => 'boolean',
        'premium_officier_engineer' => 'boolean',
        'premium_officier_geologist' => 'boolean',
        'premium_officier_technocrat' => 'boolean',
        'preference_vacation_mode' => 'boolean',
        'preference_vacation_mode_until' => 'datetime',
        'user_metal' => 'decimal:2',
        'user_crystal' => 'decimal:2',
        'user_deuterium' => 'decimal:2',
        'user_dark_matter' => 'decimal:2',
    ];

    /**
     * Get all planets owned by the user
     */
    public function planets(): HasMany
    {
        return $this->hasMany(Planet::class, 'user_id', 'user_id')
            ->orderBy('planet_galaxy')
            ->orderBy('planet_system')
            ->orderBy('planet_planet');
    }

    /**
     * Get the home planet
     */
    public function homePlanet(): BelongsTo
    {
        return $this->belongsTo(Planet::class, 'user_home_planet_id', 'planet_id');
    }

    /**
     * Get the current planet
     */
    public function currentPlanet(): BelongsTo
    {
        return $this->belongsTo(Planet::class, 'user_current_planet_id', 'planet_id');
    }

    /**
     * Get user's research
     */
    public function research(): HasOne
    {
        return $this->hasOne(Research::class, 'user_id', 'user_id');
    }

    /**
     * Get user's alliance
     */
    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class, 'alliance_id');
    }

    /**
     * Get user's fleets
     */
    public function fleets(): HasMany
    {
        return $this->hasMany(Fleet::class, 'fleet_owner', 'user_id');
    }

    /**
     * Get user's messages
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'message_receiver', 'user_id');
    }

    /**
     * Get user statistics
     */
    public function statistics(): HasOne
    {
        return $this->hasOne(UserStatistic::class, 'user_id', 'user_id');
    }

    /**
     * Check if user is banned
     */
    public function isBanned(): bool
    {
        if (!$this->user_banned) {
            return false;
        }

        if ($this->user_banned_until && $this->user_banned_until->isPast()) {
            $this->update(['user_banned' => false, 'user_banned_until' => null]);
            return false;
        }

        return true;
    }

    /**
     * Check if user has active premium
     */
    public function hasPremium(): bool
    {
        return $this->premium_dark_matter_expire_time &&
               $this->premium_dark_matter_expire_time->isFuture();
    }

    /**
     * Check if user is in vacation mode
     */
    public function isOnVacation(): bool
    {
        if (!$this->preference_vacation_mode) {
            return false;
        }

        if ($this->preference_vacation_mode_until &&
            $this->preference_vacation_mode_until->isPast()) {
            $this->update([
                'preference_vacation_mode' => false,
                'preference_vacation_mode_until' => null,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Get password field name for authentication
     */
    public function getAuthPassword(): string
    {
        return $this->user_password;
    }
}
```

### Task 1.3.3: Planet Model mit PostgreSQL JSONB

**laravel/app/Models/Planet.php:**
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Planet extends BaseModel
{
    protected $table = 'planets';
    protected $primaryKey = 'planet_id';

    protected $fillable = [
        'user_id',
        'planet_name',
        'planet_galaxy',
        'planet_system',
        'planet_planet',
        'planet_fields',
        'planet_debris',
        'planet_production',
        'planet_metal',
        'planet_crystal',
        'planet_deuterium',
        'planet_last_update',
    ];

    protected $casts = [
        'planet_fields' => 'array',      // PostgreSQL JSONB
        'planet_debris' => 'array',      // PostgreSQL JSONB
        'planet_production' => 'array',  // PostgreSQL JSONB
        'planet_metal' => 'decimal:2',
        'planet_crystal' => 'decimal:2',
        'planet_deuterium' => 'decimal:2',
        'planet_last_update' => 'datetime',
    ];

    /**
     * Get the planet owner
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get planet buildings
     */
    public function buildings(): HasOne
    {
        return $this->hasOne(Building::class, 'planet_id', 'planet_id');
    }

    /**
     * Get planet ships
     */
    public function ships(): HasOne
    {
        return $this->hasOne(Ship::class, 'planet_id', 'planet_id');
    }

    /**
     * Get planet defenses
     */
    public function defenses(): HasOne
    {
        return $this->hasOne(Defense::class, 'planet_id', 'planet_id');
    }

    /**
     * Get fleets stationed on this planet
     */
    public function fleets(): HasMany
    {
        return $this->hasMany(Fleet::class, 'fleet_start_planet_id', 'planet_id');
    }

    /**
     * Get galaxy coordinates as string
     */
    public function getCoordinatesAttribute(): string
    {
        return "{$this->planet_galaxy}:{$this->planet_system}:{$this->planet_planet}";
    }

    /**
     * Update resources based on production
     */
    public function updateResources(): void
    {
        $timeDiff = now()->diffInSeconds($this->planet_last_update);

        $production = $this->planet_production ?? [
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
        ];

        $this->update([
            'planet_metal' => $this->planet_metal + ($production['metal'] * $timeDiff / 3600),
            'planet_crystal' => $this->planet_crystal + ($production['crystal'] * $timeDiff / 3600),
            'planet_deuterium' => $this->planet_deuterium + ($production['deuterium'] * $timeDiff / 3600),
            'planet_last_update' => now(),
        ]);
    }

    /**
     * Scope: Search planets by name (Full-Text Search)
     */
    public function scopeSearch($query, string $searchTerm)
    {
        return $query->whereRaw(
            "planet_search_vector @@ plainto_tsquery('english', ?)",
            [$searchTerm]
        );
    }

    /**
     * Scope: Get planets in galaxy
     */
    public function scopeInGalaxy($query, int $galaxy, ?int $system = null)
    {
        $query->where('planet_galaxy', $galaxy);

        if ($system !== null) {
            $query->where('planet_system', $system);
        }

        return $query;
    }
}
```

### Task 1.3.4: Alle weiteren Models

Erstellen Sie Models für:
- ✅ User
- ✅ Planet
- ⏳ Building
- ⏳ Ship
- ⏳ Defense
- ⏳ Fleet
- ⏳ Research
- ⏳ Report
- ⏳ Alliance
- ⏳ Message
- ⏳ Note
- ⏳ Buddy
- ⏳ BannedUser
- ⏳ Option

**Jedes Model sollte haben:**
1. Typed Properties (PHP 8.3)
2. Relations definiert
3. Casts für Datentypen
4. Scopes für häufige Queries
5. Custom Methods für Business Logic
6. PHPDoc Kommentare

---

## Phase 1.4: API Routing & Controllers (Woche 4-6)

### Task 1.4.1: API Routes

**laravel/routes/api.php:**
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    AuthController,
    PlanetController,
    BuildingController,
    FleetController,
    ResearchController,
    AllianceController,
    GalaxyController,
    UserController,
    ReportController,
};

Route::prefix('v1')->group(function () {

    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // User
        Route::get('/user', [UserController::class, 'show']);
        Route::patch('/user', [UserController::class, 'update']);
        Route::get('/user/statistics', [UserController::class, 'statistics']);

        // Planets
        Route::apiResource('planets', PlanetController::class);
        Route::post('/planets/{planet}/rename', [PlanetController::class, 'rename']);
        Route::post('/planets/{planet}/abandon', [PlanetController::class, 'abandon']);
        Route::get('/planets/{planet}/resources', [PlanetController::class, 'resources']);

        // Buildings
        Route::get('/planets/{planet}/buildings', [BuildingController::class, 'index']);
        Route::post('/planets/{planet}/buildings/{building}/upgrade', [BuildingController::class, 'upgrade']);
        Route::delete('/planets/{planet}/buildings/{building}/cancel', [BuildingController::class, 'cancel']);
        Route::get('/planets/{planet}/buildings/queue', [BuildingController::class, 'queue']);

        // Fleet
        Route::apiResource('fleets', FleetController::class)->only(['index', 'show', 'store']);
        Route::post('/fleets/{fleet}/recall', [FleetController::class, 'recall']);
        Route::get('/planets/{planet}/fleets', [FleetController::class, 'planetFleets']);
        Route::get('/fleets/missions', [FleetController::class, 'missions']);

        // Research
        Route::get('/research', [ResearchController::class, 'index']);
        Route::post('/research/{research}/upgrade', [ResearchController::class, 'upgrade']);
        Route::delete('/research/cancel', [ResearchController::class, 'cancel']);

        // Alliance
        Route::apiResource('alliances', AllianceController::class);
        Route::post('/alliances/{alliance}/join', [AllianceController::class, 'join']);
        Route::post('/alliances/{alliance}/leave', [AllianceController::class, 'leave']);
        Route::get('/alliances/{alliance}/members', [AllianceController::class, 'members']);

        // Galaxy
        Route::get('/galaxy/{galaxy}/{system}', [GalaxyController::class, 'show']);
        Route::post('/galaxy/search', [GalaxyController::class, 'search']);

        // Reports
        Route::apiResource('reports', ReportController::class)->only(['index', 'show', 'destroy']);
        Route::post('/reports/mark-read', [ReportController::class, 'markAsRead']);

        // Admin routes
        Route::middleware('admin')->prefix('admin')->group(function () {
            Route::apiResource('users', Admin\UserController::class);
            Route::post('/users/{user}/ban', Admin\UserController::class, 'ban');
            Route::apiResource('settings', Admin\SettingController::class);
        });
    });
});
```

### Task 1.4.2: API Resource Controller Beispiel

**laravel/app/Http/Controllers/Api/V1/PlanetController.php:**
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Planet\UpdatePlanetRequest;
use App\Http\Resources\PlanetResource;
use App\Models\Planet;
use App\Services\PlanetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlanetController extends Controller
{
    public function __construct(
        private readonly PlanetService $planetService
    ) {}

    /**
     * Get all planets for authenticated user
     */
    public function index(): AnonymousResourceCollection
    {
        $planets = auth()->user()
            ->planets()
            ->with(['buildings', 'ships', 'defenses'])
            ->get();

        return PlanetResource::collection($planets);
    }

    /**
     * Get specific planet details
     */
    public function show(Planet $planet): PlanetResource
    {
        $this->authorize('view', $planet);

        $planet->load(['buildings', 'ships', 'defenses', 'user']);
        $planet->updateResources();

        return new PlanetResource($planet);
    }

    /**
     * Update planet (mainly for switching current planet)
     */
    public function update(UpdatePlanetRequest $request, Planet $planet): PlanetResource
    {
        $this->authorize('update', $planet);

        $planet->update($request->validated());

        return new PlanetResource($planet);
    }

    /**
     * Rename planet
     */
    public function rename(Planet $planet, Request $request): JsonResponse
    {
        $this->authorize('update', $planet);

        $validated = $request->validate([
            'name' => 'required|string|max:20|min:3',
        ]);

        $planet->update(['planet_name' => $validated['name']]);

        return response()->json([
            'message' => 'Planet renamed successfully',
            'data' => new PlanetResource($planet),
        ]);
    }

    /**
     * Abandon planet
     */
    public function abandon(Planet $planet): JsonResponse
    {
        $this->authorize('delete', $planet);

        // Cannot abandon home planet
        if ($planet->planet_id === auth()->user()->user_home_planet_id) {
            return response()->json([
                'message' => 'Cannot abandon home planet',
            ], 422);
        }

        $this->planetService->abandonPlanet($planet);

        return response()->json([
            'message' => 'Planet abandoned successfully',
        ]);
    }

    /**
     * Get planet resources with production rates
     */
    public function resources(Planet $planet): JsonResponse
    {
        $this->authorize('view', $planet);

        $planet->updateResources();

        return response()->json([
            'data' => [
                'current' => [
                    'metal' => $planet->planet_metal,
                    'crystal' => $planet->planet_crystal,
                    'deuterium' => $planet->planet_deuterium,
                ],
                'production' => $planet->planet_production ?? [
                    'metal' => 0,
                    'crystal' => 0,
                    'deuterium' => 0,
                ],
                'storage' => $this->planetService->getStorageCapacity($planet),
                'updated_at' => $planet->planet_last_update,
            ],
        ]);
    }
}
```

### Task 1.4.3: API Resources (Transformers)

**laravel/app/Http/Resources/PlanetResource.php:**
```php
<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->planet_id,
            'name' => $this->planet_name,
            'coordinates' => [
                'galaxy' => $this->planet_galaxy,
                'system' => $this->planet_system,
                'planet' => $this->planet_planet,
                'formatted' => $this->coordinates,
            ],
            'resources' => [
                'metal' => (float) $this->planet_metal,
                'crystal' => (float) $this->planet_crystal,
                'deuterium' => (float) $this->planet_deuterium,
            ],
            'production' => $this->planet_production ?? [
                'metal' => 0,
                'crystal' => 0,
                'deuterium' => 0,
            ],
            'fields' => $this->planet_fields,
            'debris' => $this->planet_debris,
            'buildings' => BuildingResource::collection($this->whenLoaded('buildings')),
            'ships' => ShipResource::collection($this->whenLoaded('ships')),
            'defenses' => DefenseResource::collection($this->whenLoaded('defenses')),
            'owner' => new UserResource($this->whenLoaded('user')),
            'last_update' => $this->planet_last_update,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

### Task 1.4.4: Service Layer

**laravel/app/Services/PlanetService.php:**
```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlanetService
{
    /**
     * Create a new planet for user
     */
    public function createPlanet(
        User $user,
        int $galaxy,
        int $system,
        int $planet,
        string $name = 'New Planet'
    ): Planet {
        return DB::transaction(function () use ($user, $galaxy, $system, $planet, $name) {
            $newPlanet = Planet::create([
                'user_id' => $user->user_id,
                'planet_name' => $name,
                'planet_galaxy' => $galaxy,
                'planet_system' => $system,
                'planet_planet' => $planet,
                'planet_metal' => 500,
                'planet_crystal' => 500,
                'planet_deuterium' => 0,
                'planet_fields' => [
                    'used' => 0,
                    'max' => 163,
                ],
                'planet_production' => [
                    'metal' => 30,
                    'crystal' => 15,
                    'deuterium' => 0,
                ],
                'planet_last_update' => now(),
            ]);

            // Initialize buildings
            $this->initializeBuildings($newPlanet);

            return $newPlanet;
        });
    }

    /**
     * Abandon a planet
     */
    public function abandonPlanet(Planet $planet): void
    {
        DB::transaction(function () use ($planet) {
            // Delete related data
            $planet->buildings()->delete();
            $planet->ships()->delete();
            $planet->defenses()->delete();
            $planet->fleets()->delete();

            // Delete planet
            $planet->delete();
        });
    }

    /**
     * Get storage capacity based on buildings
     */
    public function getStorageCapacity(Planet $planet): array
    {
        $buildings = $planet->buildings;

        // Calculate based on building levels
        $metalStorage = 10000 * (1.5 ** ($buildings->metal_storage ?? 0));
        $crystalStorage = 10000 * (1.5 ** ($buildings->crystal_storage ?? 0));
        $deuteriumStorage = 10000 * (1.5 ** ($buildings->deuterium_storage ?? 0));

        return [
            'metal' => (int) $metalStorage,
            'crystal' => (int) $crystalStorage,
            'deuterium' => (int) $deuteriumStorage,
        ];
    }

    /**
     * Initialize planet buildings
     */
    private function initializeBuildings(Planet $planet): void
    {
        // Create building record with all levels at 0
        $planet->buildings()->create([
            'metal_mine' => 0,
            'crystal_mine' => 0,
            'deuterium_synthesizer' => 0,
            'solar_plant' => 0,
            // ... all other buildings
        ]);
    }
}
```

---

## Phase 1.5: Testing & Documentation (Woche 5-6)

### Task 1.5.1: Feature Tests

**tests/Feature/Api/PlanetTest.php:**
```php
<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Planet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_their_planets(): void
    {
        $user = User::factory()->create();
        $planets = Planet::factory()->count(3)->create(['user_id' => $user->user_id]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/planets');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'coordinates', 'resources'],
                ],
            ]);
    }

    public function test_user_cannot_view_other_users_planet(): void
    {
        $user = User::factory()->create();
        $otherUserPlanet = Planet::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/v1/planets/{$otherUserPlanet->planet_id}");

        $response->assertForbidden();
    }

    public function test_user_can_rename_their_planet(): void
    {
        $user = User::factory()->create();
        $planet = Planet::factory()->create(['user_id' => $user->user_id]);

        $response = $this->actingAs($user)
            ->postJson("/api/v1/planets/{$planet->planet_id}/rename", [
                'name' => 'New Planet Name',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('planets', [
            'planet_id' => $planet->planet_id,
            'planet_name' => 'New Planet Name',
        ]);
    }
}
```

### Task 1.5.2: Model Factories

**laravel/database/factories/UserFactory.php:**
```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'user_name' => $this->faker->unique()->userName(),
            'user_email' => $this->faker->unique()->safeEmail(),
            'user_password' => Hash::make('password'),
            'user_galaxy' => $this->faker->numberBetween(1, 9),
            'user_system' => $this->faker->numberBetween(1, 499),
            'user_planet' => $this->faker->numberBetween(1, 15),
            'user_metal' => 500,
            'user_crystal' => 500,
            'user_deuterium' => 0,
            'user_registration' => now(),
        ];
    }
}
```

### Task 1.5.3: OpenAPI Documentation

**laravel/app/Http/Controllers/Api/V1/PlanetController.php (mit Annotations):**
```php
/**
 * @OA\Get(
 *     path="/api/v1/planets",
 *     summary="Get all user planets",
 *     tags={"Planets"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Planet")
 *             )
 *         )
 *     )
 * )
 */
public function index(): AnonymousResourceCollection
{
    // ...
}
```

---

## PostgreSQL Performance Optimierungen

### Indexes

```sql
-- Galaxy search optimization
CREATE INDEX idx_planets_galaxy_system ON planets (planet_galaxy, planet_system);
CREATE INDEX idx_planets_coordinates ON planets (planet_galaxy, planet_system, planet_planet);

-- Full-Text Search
CREATE INDEX idx_planets_search ON planets USING GIN (planet_search_vector);
CREATE INDEX idx_users_search ON users USING GIN (to_tsvector('english', user_name));

-- JSONB indexes
CREATE INDEX idx_planets_fields ON planets USING GIN (planet_fields);
CREATE INDEX idx_planets_production ON planets USING GIN (planet_production);

-- Fleet operations
CREATE INDEX idx_fleets_arrival ON fleets (fleet_end_time);
CREATE INDEX idx_fleets_owner ON fleets (fleet_owner, fleet_end_time);

-- Statistics
CREATE INDEX idx_users_stats_points ON users_statistics (stat_total_points DESC);
CREATE INDEX idx_alliance_stats_points ON alliance_statistics (stat_total_points DESC);
```

### PostgreSQL Config Tuning

```conf
# postgresql.conf
shared_buffers = 256MB
effective_cache_size = 1GB
maintenance_work_mem = 64MB
work_mem = 16MB
max_connections = 200

# Query optimization
random_page_cost = 1.1  # For SSD
effective_io_concurrency = 200

# Write-Ahead Log
wal_buffers = 16MB
checkpoint_completion_target = 0.9

# Query planning
default_statistics_target = 100
```

---

## Rollout-Strategie

### Week 1-2: Setup
- ✅ Laravel 11 installieren
- ✅ PostgreSQL Container aufsetzen
- ✅ Basis-Konfiguration

### Week 3-4: Migrations & Models
- ✅ Alle 21 Migrations erstellen
- ✅ Eloquent Models mit Relations
- ✅ Data Migration Script

### Week 5-6: API Layer
- ✅ API Routes definieren
- ✅ Controllers implementieren
- ✅ Resources (Transformers)
- ✅ Service Layer

### Week 7-8: Testing & Docs
- ✅ Feature Tests (70% Coverage)
- ✅ API Documentation (OpenAPI)
- ✅ Performance Testing
- ✅ Deployment Vorbereitung

---

## Success Criteria

**Phase 1 ist abgeschlossen wenn:**

✅ Laravel 11 läuft parallel zum Legacy Code
✅ PostgreSQL als primäre DB konfiguriert
✅ Alle 21 Tabellen als Migrations vorhanden
✅ Eloquent Models mit Relations funktionieren
✅ REST API für mindestens 5 Haupt-Features (User, Planet, Fleet, Building, Research)
✅ 70%+ Test Coverage für neue Features
✅ OpenAPI Dokumentation vollständig
✅ Performance: API Response < 100ms (avg)
✅ Data Migration von MySQL → PostgreSQL funktioniert

---

## Risiken & Mitigation

| Risiko | Wahrscheinlichkeit | Impact | Mitigation |
|--------|-------------------|---------|------------|
| PostgreSQL Inkompatibilitäten | Mittel | Hoch | Dual-DB Support (MySQL Fallback) |
| Performance-Regression | Niedrig | Hoch | Load Testing, Indexes, Caching |
| Data Migration Fehler | Mittel | Kritisch | Backup-Strategie, Rollback-Plan |
| Breaking Changes für Clients | Hoch | Mittel | API Versioning (v1), Legacy Support |
| Team PostgreSQL-Kenntnisse | Mittel | Mittel | Training, Dokumentation |

---

## Nächste Schritte

Sollen wir beginnen mit:

1. **Task 1.1.1**: Laravel 11 Setup?
2. **Task 1.1.2**: PostgreSQL Container konfigurieren?
3. **Task 1.2.1**: Erste Migrations schreiben?

Oder möchten Sie einen anderen Teil von Phase 1 zuerst angehen?
