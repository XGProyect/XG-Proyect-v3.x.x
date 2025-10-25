# PostgreSQL Migration Guide

## Warum PostgreSQL?

XG-Proyect migriert von MySQL 8.0 zu **PostgreSQL 16** aus folgenden Gründen:

### Technische Vorteile

1. **Bessere ACID-Compliance**
   - Vollständige Transaktions-Sicherheit
   - MVCC (Multi-Version Concurrency Control)
   - Keine Locking-Probleme bei Fleet-Updates

2. **Fortgeschrittene Features**
   - Native JSONB für Battle Reports
   - Arrays für flexible Datenstrukturen
   - Window Functions für Leaderboards
   - Full-Text Search (mehrsprachig)
   - Materialized Views für Statistiken

3. **Performance**
   - Schneller bei komplexen Queries
   - Bessere Concurrency
   - Effiziente Indexes (GIN, GiST, BRIN)

4. **Extensions**
   - pg_stat_statements (Query-Analyse)
   - pgcrypto (Verschlüsselung)
   - PostGIS (Galaxy-Koordinaten)
   - pg_trgm (Fuzzy Search)

5. **Open Source**
   - Keine Oracle-Abhängigkeit
   - Liberal License
   - Starke Community

---

## Migration Strategy

### Phase 1: Dual-Database Setup (Woche 1-2)

**Beide DBs parallel laufen lassen:**

```yaml
# docker-compose.dev.yml
services:
  postgres:    # Primary (neue Features)
  db:          # MySQL (Legacy, readonly)
```

**Verwendung:**
```bash
# Normal (PostgreSQL)
docker-compose -f docker-compose.dev.yml up

# Mit MySQL (Legacy-Support)
docker-compose -f docker-compose.dev.yml --profile legacy up
```

### Phase 2: Schema Migration (Woche 2-3)

**Laravel Migrations erstellen:**
```bash
php artisan migrate --database=pgsql
```

**21 Tabellen als Migrations:**
- users, users_statistics, preferences, sessions
- planets, buildings, ships, defenses
- fleets, research, reports
- alliances, alliance_statistics
- buddys, messages, notes, acs
- banned, changelog, languages, options, premium

### Phase 3: Data Migration (Woche 3-4)

**Artisan Command verwenden:**
```bash
# Alle Daten migrieren
php artisan migrate:from-mysql

# Nur bestimmte Tabellen
php artisan migrate:from-mysql --table=users --table=planets

# Mit größeren Batches (schneller)
php artisan migrate:from-mysql --chunk=5000

# Mit Fortschrittsanzeige
php artisan migrate:from-mysql --verbose
```

**Was wird migriert:**
- ✅ Alle User-Accounts mit Passwörtern (bcrypt)
- ✅ Alle Planeten mit Koordinaten
- ✅ Alle Gebäude, Schiffe, Verteidigung
- ✅ Alle Forschungen
- ✅ Alle Flotten (laufende Missionen)
- ✅ Alle Battle Reports
- ✅ Alle Allianzen mit Mitgliedern
- ✅ Alle Nachrichten und Notizen

**Was wird transformiert:**
- Battle Reports → JSONB (strukturierte Daten)
- Planet Fields → JSONB (flexible Felder)
- Debris Fields → JSONB
- Production Rates → JSONB

### Phase 4: Testing & Validation (Woche 4-5)

**Data Integrity Checks:**
```bash
# Vergleich Anzahl Datensätze
php artisan db:compare-counts

# Vergleich kritischer Daten (Checksums)
php artisan db:validate-migration

# Performance-Vergleich
php artisan db:benchmark
```

**Test Cases:**
```bash
# Unit Tests
composer phpunit

# Feature Tests mit PostgreSQL
php artisan test --env=testing-pgsql

# Load Testing
ab -n 1000 -c 10 http://localhost/api/v1/planets
```

### Phase 5: Cut-Over (Woche 5-6)

**Production Deployment:**
```bash
# 1. Backup MySQL
mysqldump -u root -p xgp > backup_mysql_$(date +%Y%m%d).sql

# 2. PostgreSQL bereitstellen
docker-compose -f docker-compose.production.yml up -d postgres

# 3. Migrations ausführen
php artisan migrate --database=pgsql --force

# 4. Daten migrieren
php artisan migrate:from-mysql --chunk=10000

# 5. Validieren
php artisan db:validate-migration

# 6. Switch auf PostgreSQL
# .env: DB_CONNECTION=pgsql

# 7. Application neustarten
docker-compose -f docker-compose.production.yml restart app

# 8. Monitoring aktivieren
php artisan telescope:install
```

---

## PostgreSQL-spezifische Anpassungen

### 1. JSONB für flexible Daten

**Battle Reports (vorher: serialized PHP array):**
```php
// Vorher (MySQL):
$report = unserialize($row['report_data']);

// Nachher (PostgreSQL JSONB):
$report = Report::find($id)->report_data;  // Already decoded
```

**Schema:**
```sql
CREATE TABLE reports (
    report_id BIGSERIAL PRIMARY KEY,
    report_data JSONB NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);

-- JSONB Index für schnelle Queries
CREATE INDEX idx_reports_data ON reports USING GIN (report_data);

-- Query innerhalb von JSONB
SELECT * FROM reports
WHERE report_data->>'winner' = 'attacker'
  AND (report_data->'loot'->>'metal')::bigint > 100000;
```

### 2. Full-Text Search

**Planeten-Suche:**
```sql
-- Search Vector
ALTER TABLE planets
ADD COLUMN search_vector tsvector
GENERATED ALWAYS AS (
    to_tsvector('english', coalesce(planet_name, ''))
) STORED;

-- GIN Index
CREATE INDEX idx_planets_search ON planets USING GIN (search_vector);

-- Query
SELECT * FROM planets
WHERE search_vector @@ plainto_tsquery('english', 'homeworld');
```

**Eloquent:**
```php
// app/Models/Planet.php
public function scopeSearch($query, string $term)
{
    return $query->whereRaw(
        "search_vector @@ plainto_tsquery('english', ?)",
        [$term]
    );
}

// Usage:
$planets = Planet::search('my planet')->get();
```

### 3. Window Functions für Leaderboards

**Top 100 Users by Points:**
```sql
-- MySQL (langsam):
SELECT
    u.user_id,
    u.user_name,
    us.stat_total_points,
    (SELECT COUNT(*) + 1 FROM users_statistics WHERE stat_total_points > us.stat_total_points) as rank
FROM users u
JOIN users_statistics us ON u.user_id = us.user_id
ORDER BY us.stat_total_points DESC
LIMIT 100;

-- PostgreSQL (schnell):
SELECT
    user_id,
    user_name,
    stat_total_points,
    RANK() OVER (ORDER BY stat_total_points DESC) as rank
FROM users u
JOIN users_statistics us USING (user_id)
ORDER BY stat_total_points DESC
LIMIT 100;
```

**Eloquent:**
```php
use Illuminate\Support\Facades\DB;

$leaderboard = User::select([
    'users.user_id',
    'users.user_name',
    'users_statistics.stat_total_points',
    DB::raw('RANK() OVER (ORDER BY stat_total_points DESC) as rank')
])
->join('users_statistics', 'users.user_id', '=', 'users_statistics.user_id')
->orderByDesc('stat_total_points')
->limit(100)
->get();
```

### 4. Arrays für flexible Listen

**Fleet Composition:**
```sql
-- Vorher (MySQL): Separate Columns
fleet_ship_202, fleet_ship_203, fleet_ship_204, ...

-- Nachher (PostgreSQL): Array/JSONB
fleet_composition JSONB

-- Beispiel:
{
  "202": 100,  -- Small Cargo: 100
  "203": 50,   -- Large Cargo: 50
  "204": 10    -- Light Fighter: 10
}

-- Query: Flotten mit Light Fighters
SELECT * FROM fleets
WHERE fleet_composition ? '204'
  AND (fleet_composition->>'204')::int > 0;
```

### 5. Materialized Views für Statistiken

**Alliance Statistics (cached):**
```sql
CREATE MATERIALIZED VIEW alliance_statistics_mv AS
SELECT
    a.alliance_id,
    a.alliance_name,
    COUNT(u.user_id) as member_count,
    SUM(us.stat_total_points) as total_points,
    AVG(us.stat_total_points) as avg_points,
    RANK() OVER (ORDER BY SUM(us.stat_total_points) DESC) as rank
FROM alliances a
LEFT JOIN users u ON u.alliance_id = a.alliance_id
LEFT JOIN users_statistics us ON u.user_id = us.user_id
GROUP BY a.alliance_id, a.alliance_name;

-- Index
CREATE UNIQUE INDEX ON alliance_statistics_mv (alliance_id);
CREATE INDEX ON alliance_statistics_mv (rank);

-- Refresh (täglich via Cron)
REFRESH MATERIALIZED VIEW CONCURRENTLY alliance_statistics_mv;
```

**Eloquent Access:**
```php
// Read from materialized view
$stats = DB::table('alliance_statistics_mv')
    ->where('rank', '<=', 100)
    ->get();
```

---

## Performance Tuning

### 1. Indexes

```sql
-- Primary Keys (automatisch)
CREATE INDEX idx_users_email ON users (user_email);
CREATE INDEX idx_users_name ON users (user_name);

-- Composite Indexes (Koordinaten)
CREATE INDEX idx_planets_coords ON planets (planet_galaxy, planet_system, planet_planet);

-- Partial Indexes (nur aktive User)
CREATE INDEX idx_users_active ON users (user_lastlogin)
WHERE user_banned = false AND preference_vacation_mode = false;

-- JSONB GIN Indexes
CREATE INDEX idx_reports_data ON reports USING GIN (report_data);
CREATE INDEX idx_planets_fields ON planets USING GIN (planet_fields);

-- Full-Text GIN Indexes
CREATE INDEX idx_planets_search ON planets USING GIN (search_vector);
```

### 2. Query Optimization

**EXPLAIN ANALYZE verwenden:**
```sql
EXPLAIN ANALYZE
SELECT * FROM planets
WHERE planet_galaxy = 1 AND planet_system = 100;
```

**Eloquent Query Log:**
```php
DB::enableQueryLog();
// ... queries ...
dd(DB::getQueryLog());
```

### 3. Connection Pooling

**PgBouncer (Production):**
```yaml
services:
  pgbouncer:
    image: pgbouncer/pgbouncer
    environment:
      DATABASES_HOST: postgres
      DATABASES_PORT: 5432
      DATABASES_DBNAME: xgp
      PGBOUNCER_POOL_MODE: transaction
      PGBOUNCER_MAX_CLIENT_CONN: 1000
      PGBOUNCER_DEFAULT_POOL_SIZE: 20
    ports:
      - "6432:6432"
```

**Laravel Config:**
```php
// config/database.php
'pgsql' => [
    'host' => env('DB_HOST', 'pgbouncer'),
    'port' => env('DB_PORT', '6432'),  // PgBouncer Port
    // ...
],
```

### 4. Partitioning (für große Tabellen)

**Reports Table Partitioning (nach Datum):**
```sql
-- Parent Table
CREATE TABLE reports (
    report_id BIGSERIAL,
    user_id BIGINT NOT NULL,
    report_data JSONB,
    created_at TIMESTAMP NOT NULL
) PARTITION BY RANGE (created_at);

-- Partitions (monatlich)
CREATE TABLE reports_2024_01 PARTITION OF reports
FOR VALUES FROM ('2024-01-01') TO ('2024-02-01');

CREATE TABLE reports_2024_02 PARTITION OF reports
FOR VALUES FROM ('2024-02-01') TO ('2024-03-01');

-- Auto-Partition mit pg_partman Extension
CREATE EXTENSION pg_partman;
```

---

## Monitoring & Maintenance

### 1. Query Performance (pg_stat_statements)

```sql
-- Enable Extension
CREATE EXTENSION pg_stat_statements;

-- Top 10 Slowest Queries
SELECT
    query,
    calls,
    mean_exec_time,
    max_exec_time,
    stddev_exec_time
FROM pg_stat_statements
ORDER BY mean_exec_time DESC
LIMIT 10;
```

### 2. Index Usage

```sql
-- Unused Indexes
SELECT
    schemaname,
    tablename,
    indexname,
    idx_scan
FROM pg_stat_user_indexes
WHERE idx_scan = 0
  AND indexname NOT LIKE '%pkey%';
```

### 3. Vacuum & Analyze

```bash
# Auto-vacuum (sollte aktiviert sein)
# postgresql.conf:
autovacuum = on

# Manuell
VACUUM ANALYZE;

# Full Vacuum (sperrt Tabelle)
VACUUM FULL;
```

### 4. Backup Strategy

**pg_dump (täglich):**
```bash
#!/bin/bash
# backup.sh
pg_dump -h localhost -U xgproyect -d xgp \
    -F c -b -v -f backup_$(date +%Y%m%d_%H%M%S).dump

# Compress
gzip backup_*.dump

# Retention (7 Tage)
find . -name "backup_*.dump.gz" -mtime +7 -delete
```

**Point-in-Time Recovery:**
```conf
# postgresql.conf
wal_level = replica
archive_mode = on
archive_command = 'cp %p /var/lib/postgresql/wal_archive/%f'
```

---

## Troubleshooting

### Problem: Slow Queries

**Lösung:**
```sql
-- 1. EXPLAIN ANALYZE
EXPLAIN ANALYZE SELECT ...;

-- 2. Check Indexes
\d+ table_name

-- 3. Update Statistics
ANALYZE table_name;

-- 4. Check for Sequential Scans
SELECT * FROM pg_stat_user_tables
WHERE seq_scan > 1000 AND idx_scan = 0;
```

### Problem: Connection Limit

**Lösung:**
```sql
-- Check current connections
SELECT count(*) FROM pg_stat_activity;

-- Increase limit (postgresql.conf)
max_connections = 200

-- Use Connection Pooling (PgBouncer)
```

### Problem: Disk Space

**Lösung:**
```sql
-- Check table sizes
SELECT
    schemaname,
    tablename,
    pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) AS size
FROM pg_tables
WHERE schemaname = 'public'
ORDER BY pg_total_relation_size(schemaname||'.'||tablename) DESC;

-- Vacuum to reclaim space
VACUUM FULL;
```

---

## Rollback Plan

Falls PostgreSQL-Migration fehlschlägt:

```bash
# 1. Stop Application
docker-compose down

# 2. Switch back to MySQL
# .env: DB_CONNECTION=mysql

# 3. Restore MySQL Backup (if needed)
mysql -u root -p xgp < backup_mysql_20240101.sql

# 4. Restart with MySQL
docker-compose -f docker-compose.dev.yml --profile legacy up
```

---

## Cheat Sheet: MySQL vs PostgreSQL

| Feature | MySQL | PostgreSQL |
|---------|-------|------------|
| **Auto Increment** | `AUTO_INCREMENT` | `SERIAL` oder `BIGSERIAL` |
| **String Concat** | `CONCAT(a, b)` | `a \|\| b` |
| **Limit/Offset** | `LIMIT 10 OFFSET 20` | `LIMIT 10 OFFSET 20` (gleich) |
| **Date/Time** | `NOW()` | `NOW()` oder `CURRENT_TIMESTAMP` |
| **IFNULL** | `IFNULL(a, b)` | `COALESCE(a, b)` |
| **String Case** | `UCASE()`, `LCASE()` | `UPPER()`, `LOWER()` |
| **Substring** | `SUBSTRING(str, pos, len)` | `SUBSTRING(str FROM pos FOR len)` |
| **Regex** | `REGEXP` | `~` oder `~*` (case insensitive) |

---

## FAQ

**Q: Wird meine bestehende MySQL-Datenbank gelöscht?**
A: Nein. MySQL bleibt als Legacy-DB erhalten. Sie können jederzeit zurückwechseln.

**Q: Wie lange dauert die Datenmigration?**
A: Bei 100.000 Users ca. 10-20 Minuten (mit Chunk Size 5000).

**Q: Kann ich beide Datenbanken parallel nutzen?**
A: Ja, während der Migration. Langfristig nur PostgreSQL.

**Q: Funktionieren meine MySQL-Queries noch?**
A: Laravel Eloquent übersetzt automatisch. Rohe SQL-Queries müssen angepasst werden.

**Q: Ist PostgreSQL schneller als MySQL?**
A: Für XG-Proyect ja - besonders bei komplexen Queries (Leaderboards, Reports).

---

## Support

Bei Problemen:
1. Siehe `PHASE_1_PLAN.md`
2. PostgreSQL Docs: https://www.postgresql.org/docs/16/
3. Laravel Docs: https://laravel.com/docs/11.x/database
4. GitHub Issues

---

**Ready to migrate?**
```bash
# Start PostgreSQL Setup
docker-compose -f docker-compose.dev.yml up -d postgres pgadmin

# Access PgAdmin
open http://localhost:5050
# Login: admin@xgproyect.local / admin
```
