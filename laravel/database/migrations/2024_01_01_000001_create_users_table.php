<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary Key
            $table->id('user_id');

            // Account Information
            $table->string('user_name', 32)->unique();
            $table->string('user_email', 64)->unique();
            $table->string('user_password', 255);
            $table->string('user_ip', 45)->nullable();
            $table->rememberToken();

            // Timestamps
            $table->timestamp('user_registration')->useCurrent();
            $table->timestamp('user_lastlogin')->nullable();

            // Ban Information
            $table->boolean('user_banned')->default(false);
            $table->timestamp('user_banned_until')->nullable();
            $table->text('user_ban_reason')->nullable();

            // Game Position
            $table->unsignedBigInteger('user_home_planet_id')->nullable();
            $table->unsignedBigInteger('user_current_planet_id')->nullable();
            $table->unsignedSmallInteger('user_galaxy')->default(1);
            $table->unsignedSmallInteger('user_system')->default(1);
            $table->unsignedSmallInteger('user_planet')->default(1);

            // Alliance
            $table->unsignedBigInteger('alliance_id')->nullable();

            // Officers & Premium (timestamps for expiration)
            $table->timestamp('premium_dark_matter_expire_time')->nullable();
            $table->timestamp('premium_officer_commander_until')->nullable();
            $table->timestamp('premium_officer_admiral_until')->nullable();
            $table->timestamp('premium_officer_engineer_until')->nullable();
            $table->timestamp('premium_officer_geologist_until')->nullable();
            $table->timestamp('premium_officer_technocrat_until')->nullable();

            // User Preferences
            $table->string('preference_lang', 10)->default('en');
            $table->string('preference_planet_sort', 20)->default('0');
            $table->smallInteger('preference_planet_order')->default(0);
            $table->boolean('preference_spy_probes')->default(true);
            $table->boolean('preference_vacation_mode')->default(false);
            $table->timestamp('preference_vacation_mode_until')->nullable();

            // Resources (decimal for precision)
            $table->decimal('user_metal', 20, 2)->default(500);
            $table->decimal('user_crystal', 20, 2)->default(500);
            $table->decimal('user_deuterium', 20, 2)->default(0);
            $table->decimal('user_dark_matter', 20, 2)->default(0);

            // Laravel timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('user_name');
            $table->index('user_email');
            $table->index(['user_galaxy', 'user_system', 'user_planet'], 'idx_user_coordinates');
            $table->index('user_banned');
            $table->index('user_lastlogin');
            $table->index('alliance_id');

            // Partial index for active users (PostgreSQL specific)
            // CREATE INDEX idx_users_active ON users (user_lastlogin)
            // WHERE user_banned = false AND preference_vacation_mode = false;
        });

        // Add full-text search support (PostgreSQL)
        DB::statement("
            ALTER TABLE {$this->getTableName('users')}
            ADD COLUMN search_vector tsvector
            GENERATED ALWAYS AS (
                to_tsvector('english', coalesce(user_name, '') || ' ' || coalesce(user_email, ''))
            ) STORED
        ");

        DB::statement("
            CREATE INDEX idx_users_search ON {$this->getTableName('users')}
            USING GIN (search_vector)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }

    /**
     * Get table name with prefix
     */
    private function getTableName(string $table): string
    {
        return DB::getTablePrefix() . $table;
    }
};
