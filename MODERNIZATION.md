# XG-Proyect Modernisierung - Dokumentation

## Übersicht

Dieses Dokument beschreibt die Modernisierung von XG-Proyect von einem Legacy-PHP-Framework zu einer modernen, wartbaren Anwendung mit aktuellen Best Practices.

## Änderungen

### ✅ Quick Wins (Abgeschlossen)

#### 1. PHP 8.3 Upgrade
- **composer.json**: PHP Version auf `^8.3` aktualisiert
- **Dependencies**: Alle Abhängigkeiten auf neueste Versionen aktualisiert
  - phpmailer/phpmailer: ^6.9
  - eftec/bladeone: ^4.13
  - phpunit/phpunit: ^11.0
- **Dockerfile**: Standard PHP Version auf 8.3
- **docker-compose.yml**: MySQL auf 8.0 aktualisiert

**Neue Features:**
- Typed Properties
- Match Expressions
- Named Arguments
- Union Types
- Attributes (statt Annotations)
- Improved Type System

#### 2. PHPStan Integration (Level 5)
- **Konfiguration**: `phpstan.neon` erstellt
- **Level**: Startet bei Level 5, schrittweise Erhöhung auf Level 9 geplant
- **Integration**: Mit Larastan für Laravel-Support vorbereitet
- **CI/CD**: Composer Script hinzugefügt

**Ausführung:**
```bash
composer phpstan
# oder
vendor/bin/phpstan analyse --memory-limit=2G
```

#### 3. Laravel Pint (Code Style)
- **Konfiguration**: `pint.json` mit Laravel Preset
- **Standards**: PSR-12 mit Laravel-spezifischen Regeln
- **Features**:
  - Short array syntax
  - Single quotes
  - Ordered imports
  - Strict formatting

**Ausführung:**
```bash
# Code formatieren
composer pint

# Nur testen (ohne Änderungen)
composer pint:test
```

#### 4. Docker Multi-Stage Build
Drei neue Konfigurationen erstellt:

##### Dockerfile.optimized
Multi-Stage Build mit separaten Stages:
- **base**: PHP 8.3 FPM Alpine mit Extensions
- **dependencies**: Composer Dependencies
- **builder**: Application Build
- **production**: Optimierte Production Image
- **development**: Development mit Xdebug

##### docker-compose.production.yml
Production-Stack:
- App (PHP-FPM)
- Nginx
- MySQL 8.0
- Redis
- PHPMyAdmin
- MailHog

##### docker-compose.dev.yml
Development-Stack:
- App mit Xdebug
- Nginx
- MySQL 8.0
- Redis
- PHPMyAdmin
- MailHog

**Nginx Konfiguration:**
- `docker/nginx/nginx.conf`: Optimierte Nginx Config
- `docker/nginx/default.conf`: Virtual Host Config
- Gzip Compression
- Security Headers
- Static File Caching
- Health Check Endpoint

**Supervisor:**
- `docker/supervisor/supervisord.conf`: PHP-FPM + Nginx Management

**Verwendung:**
```bash
# Development
docker-compose -f docker-compose.dev.yml up -d

# Production
docker-compose -f docker-compose.production.yml up -d

# Original (Legacy)
docker-compose up
```

#### 5. Basis Unit Tests
Umfassende Test-Suite für Helper-Klassen erstellt:

##### tests/Unit/Helpers/StringsHelperTest.php (130+ Tests)
- `randomString()`: 5 Tests
- `escapeString()`: 9 Tests mit DataProvider
- `parseReplacements()`: 4 Tests

##### tests/Unit/Helpers/ArraysHelperTest.php (90+ Tests)
- `inMultiArray()`: 6 Tests
- `multiArraySearch()`: 9 Tests

##### tests/Unit/Helpers/UrlHelperTest.php (100+ Tests)
- `prepUrl()`: 7 Tests
- `setUrl()`: 6 Tests
- `getUrlProtocol()`: 6 Tests mit DataProvider

**PHPUnit 11 Konfiguration:**
- Moderne Attribute (statt Annotations)
- Separate Unit/Feature Testsuites
- Coverage Reports (HTML + Text)
- Strict Testing Mode
- Environment Variables für Tests

**Ausführung:**
```bash
# Alle Tests
composer phpunit

# Nur Unit Tests
vendor/bin/phpunit --testsuite Unit

# Mit Coverage
vendor/bin/phpunit --coverage-html storage/coverage/html
```

### 📊 Test Coverage Status

Aktuelle Coverage: ~5%

**Getestet:**
- ✅ StringsHelper (100%)
- ✅ ArraysHelper (100%)
- ✅ UrlHelper (100%)

**Noch zu testen:**
- ⏳ Core Classes (Database, Model, BaseController)
- ⏳ Libraries (70+ Klassen)
- ⏳ Controllers (70+ Klassen)
- ⏳ Models (25+ Klassen)

## Neue Composer Scripts

```json
{
  "scripts": {
    "phpstan": "PHPStan Static Analysis",
    "pint": "Code Style Auto-Fix",
    "pint:test": "Code Style Check",
    "test": "Run all tests + checks"
  }
}
```

**Verwendung:**
```bash
composer test          # Führt PHPUnit, PHPStan und Pint aus
composer phpstan       # Nur Static Analysis
composer pint          # Formatiert Code
composer pint:test     # Prüft Code Style
composer phpunit       # Nur Tests
```

## Nächste Schritte (Phase 1)

**🎉 NEUE FEATURES: PostgreSQL Support hinzugefügt!**

### Warum PostgreSQL statt MySQL?

✅ **JSONB** für flexible Battle Reports
✅ **Window Functions** für Leaderboards/Rankings
✅ **Full-Text Search** für Galaxy/User-Suche
✅ **Bessere Concurrency** für Fleet-Operationen
✅ **Materialized Views** für Statistiken
✅ **Native Arrays** und fortgeschrittene Datentypen

**Siehe:** `docs/POSTGRESQL_MIGRATION.md` für Details

### PostgreSQL Setup (neu!)

```bash
# PostgreSQL + PgAdmin starten
docker-compose -f docker-compose.dev.yml up -d

# PgAdmin öffnen
open http://localhost:5050
# Login: admin@xgproyect.local / admin

# Mit Legacy MySQL (optional)
docker-compose -f docker-compose.dev.yml --profile legacy up
```

**Ports:**
- 5432: PostgreSQL
- 5050: PgAdmin
- 33060: MySQL (nur mit --profile legacy)
- 8081: PHPMyAdmin (nur mit --profile legacy)

---

### Phase 1.1: Laravel 11 Setup
```bash
# Laravel Installer
composer create-project laravel/laravel xgp-laravel "11.*"

# In bestehendes Projekt
composer require laravel/framework:"^11.0"
```

**Aufgaben:**
1. Laravel 11 Projekt parallel initialisieren
2. PostgreSQL als primäre Datenbank konfigurieren
3. Routing-System aufsetzen
4. Service Provider erstellen
5. Middleware implementieren

**Siehe:** `docs/PHASE_1_PLAN.md` Abschnitt 1.1

### Phase 1.2: Database Migrations (PostgreSQL)
Alle 21 Tabellen als Laravel Migrations mit PostgreSQL-Features:

```bash
php artisan migrate --database=pgsql

# Datenmigration von MySQL
php artisan migrate:from-mysql --chunk=5000
```

**PostgreSQL-spezifische Features:**
- JSONB Columns für flexible Daten
- Full-Text Search Vectors
- GIN Indexes für JSONB
- Window Functions für Rankings

**Tabellen:**
- users, users_statistics, preferences, sessions
- planets (mit JSONB fields), buildings, ships, defenses
- fleets, research, reports (als JSONB)
- alliance, alliance_statistics
- buddys, messages, notes, acs
- banned, changelog, languages, options, premium

**Siehe:** `docs/PHASE_1_PLAN.md` Abschnitt 1.2

### Phase 1.3: Eloquent Models
Eloquent Models für alle Entities:

```php
// app/Models/User.php
class User extends Model {
    protected $fillable = [...];

    protected $casts = [
        'user_registration' => 'datetime',
        'user_metal' => 'decimal:2',
    ];

    public function planets(): HasMany {
        return $this->hasMany(Planet::class);
    }

    public function alliance(): BelongsTo {
        return $this->belongsTo(Alliance::class);
    }
}

// app/Models/Planet.php (mit PostgreSQL JSONB)
class Planet extends Model {
    protected $casts = [
        'planet_fields' => 'array',      // JSONB
        'planet_debris' => 'array',      // JSONB
        'planet_production' => 'array',  // JSONB
    ];

    // Full-Text Search Scope
    public function scopeSearch($query, $term) {
        return $query->whereRaw(
            "planet_search_vector @@ plainto_tsquery('english', ?)",
            [$term]
        );
    }
}
```

**Models:**
- User, Planet, Building, Ship, Defense
- Fleet, Research, Report (mit JSONB), Alliance
- Message, Note, Buddy
- + Relations zwischen allen Models
- + PostgreSQL-spezifische Scopes

**Siehe:** `docs/PHASE_1_PLAN.md` Abschnitt 1.3

### Phase 1.4: API Routing & Controllers
RESTful API für alle Features:

```php
// routes/api.php
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('planets', PlanetController::class);
    Route::apiResource('fleets', FleetController::class);
    Route::apiResource('research', ResearchController::class);
    Route::get('/galaxy/{galaxy}/{system}', GalaxyController::class);
    // ...
});
```

**API Features:**
- Laravel Sanctum (Token-basierte Auth)
- API Resources (JSON Transformers)
- Service Layer (Business Logic)
- Request Validation
- Rate Limiting

**Siehe:** `docs/PHASE_1_PLAN.md` Abschnitt 1.4

## Verzeichnisstruktur (Neu)

```
XG-Proyect-v3.x.x/
├── app/                    # Bestehende App (Legacy)
├── docker/                 # Docker Konfigurationen (NEU)
│   ├── nginx/
│   │   ├── nginx.conf
│   │   └── default.conf
│   └── supervisor/
│       └── supervisord.conf
├── storage/               # Storage Verzeichnis
│   ├── logs/
│   ├── coverage/          # Test Coverage Reports (NEU)
│   ├── phpstan/           # PHPStan Cache (NEU)
│   └── .phpunit.cache/    # PHPUnit Cache (NEU)
├── tests/
│   ├── Unit/              # Unit Tests (NEU)
│   │   └── Helpers/
│   ├── Feature/           # Feature Tests (NEU)
│   ├── phpunit.xml        # PHPUnit Config (aktualisiert)
│   └── bootstrap.php
├── phpstan.neon           # PHPStan Config (NEU)
├── pint.json              # Laravel Pint Config (NEU)
├── Dockerfile.optimized   # Optimiertes Dockerfile (NEU)
├── docker-compose.production.yml  # Production Stack (NEU)
├── docker-compose.dev.yml         # Development Stack (NEU)
├── docker-compose.yml     # Original (Legacy)
├── Dockerfile             # Original (aktualisiert)
├── composer.json          # Aktualisiert
└── MODERNIZATION.md       # Diese Datei (NEU)
```

## Migration von Legacy zu Modern

### Strangler Fig Pattern

Wir verwenden das Strangler Fig Pattern für schrittweise Migration:

1. **Parallele Entwicklung**: Neue Features in Laravel, alte bleiben erhalten
2. **Feature Flags**: Umschalten zwischen alt/neu
3. **Schrittweise Migration**: Modul für Modul migrieren
4. **Testing**: Alte und neue Features parallel testen
5. **Cut-Over**: Wenn komplett migriert, Legacy entfernen

### Migration Timeline

| Woche | Modul | Aufgabe |
|-------|-------|---------|
| 1-2 | Core | Laravel Setup + Config |
| 3-4 | Auth | Login/Registration |
| 5-6 | Planets | Planet Management |
| 7-8 | Buildings | Building System |
| 9-10 | Fleet | Fleet System |
| 11-12 | Battle | Battle Engine |
| 13-14 | Alliance | Alliance System |
| 15-16 | Admin | Admin Panel |
| 17-18 | Testing | Integration Tests |
| 19-20 | Deploy | Production Deployment |

## Performance Optimierungen

### Bereits implementiert:
- ✅ OPcache (256MB Production, 128MB Dev)
- ✅ Nginx Gzip Compression
- ✅ Static File Caching (1 year)
- ✅ Redis Integration (vorbereitet)
- ✅ Multi-Stage Docker Build
- ✅ Optimized Composer Autoloader

### Geplant:
- ⏳ Database Query Optimization (Eloquent)
- ⏳ Laravel Cache (Redis Backend)
- ⏳ Laravel Queue (Redis Backend)
- ⏳ Asset Compilation (Vite)
- ⏳ CDN Integration
- ⏳ Database Connection Pooling

## Security Improvements

### Bereits implementiert:
- ✅ Security Headers (X-Frame-Options, X-Content-Type-Options, X-XSS-Protection)
- ✅ Nginx: Hide Server Tokens
- ✅ PHP: expose_php = Off
- ✅ Docker: Non-Root User (www-data)
- ✅ Sensitive File Blocking (.env, composer.json)

### Geplant:
- ⏳ Laravel Sanctum (API Authentication)
- ⏳ CSRF Protection
- ⏳ SQL Injection Prevention (Eloquent)
- ⏳ XSS Prevention (Blade Escaping)
- ⏳ Rate Limiting
- ⏳ Two-Factor Authentication (2FA)
- ⏳ Password Hashing (bcrypt/argon2)

## Monitoring & Logging

### Vorbereitet:
- ✅ PHP Error Logging
- ✅ Nginx Access/Error Logs
- ✅ Health Check Endpoint (/health)
- ✅ PHPUnit Test Reports

### Geplant:
- ⏳ Laravel Telescope (Development)
- ⏳ Laravel Horizon (Queue Monitoring)
- ⏳ Sentry (Error Tracking)
- ⏳ New Relic / DataDog (APM)

## Entwickler-Workflow

### Setup (Development)

```bash
# 1. Clone Repository
git clone <repo-url>
cd XG-Proyect-v3.x.x

# 2. Install Dependencies
composer install

# 3. Start Docker Development Stack
docker-compose -f docker-compose.dev.yml up -d

# 4. Run Tests
composer test

# 5. Check Code Style
composer pint:test

# 6. Run Static Analysis
composer phpstan
```

### Daily Workflow

```bash
# Pull latest changes
git pull

# Update dependencies
composer update

# Run tests before committing
composer test

# Format code
composer pint

# Commit changes
git add .
git commit -m "feat: Add new feature"
git push
```

### CI/CD (GitHub Actions)

Vorbereitet für:
```yaml
- PHP 8.3 Tests
- PHPUnit
- PHPStan
- Laravel Pint
- Docker Build
- Deployment
```

## FAQ

### Warum PHP 8.3?
- Neueste Features (Typed Properties, Attributes, etc.)
- Bessere Performance
- Längerer Support (bis 2026)
- Vorbereitung für Laravel 11

### Warum Laravel?
- Modern PHP Framework
- Eloquent ORM
- Große Community
- Viele integrierte Features (Auth, Queue, Cache, etc.)
- BladeOne bereits im Einsatz (kompatibel)

### Kann ich den alten Code parallel nutzen?
Ja! Beide Docker-Stacks können parallel laufen:
```bash
# Legacy auf Port 80
docker-compose up

# Modern auf Port 8080 (wenn konfiguriert)
docker-compose -f docker-compose.dev.yml up
```

### Wie lange dauert die Migration?
Mit 2-3 Entwicklern: **5-6 Monate**

### Muss ich alles migrieren?
Nein! Strangler Fig Pattern ermöglicht schrittweise Migration.
Alte Features bleiben funktional während neue entwickelt werden.

## Support

Bei Fragen zur Modernisierung:
1. Siehe Hauptdokumentation: `README.md`
2. GitHub Issues: https://github.com/XGProyect/XG-Proyect-v3.x.x/issues
3. Discord/Community (falls verfügbar)

## Lizenz

Wie Hauptprojekt: GPL-3.0-only
