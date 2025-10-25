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
        Schema::create('planets', function (Blueprint $table) {
            // Primary Key
            $table->id('planet_id');

            // Owner
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');

            // Planet Information
            $table->string('planet_name', 20);
            $table->unsignedSmallInteger('planet_galaxy');
            $table->unsignedSmallInteger('planet_system');
            $table->unsignedSmallInteger('planet_planet');
            $table->enum('planet_type', ['planet', 'moon', 'debris'])->default('planet');

            // Fields (using JSONB for flexible structure)
            $table->jsonb('planet_fields')->nullable()->comment('Building fields data');
            $table->jsonb('planet_debris')->nullable()->comment('Debris field data');
            $table->jsonb('planet_production')->nullable()->comment('Resource production rates');

            // Resources
            $table->decimal('planet_metal', 20, 2)->default(0);
            $table->decimal('planet_crystal', 20, 2)->default(0);
            $table->decimal('planet_deuterium', 20, 2)->default(0);

            // Planet Image/Type
            $table->string('planet_image', 32)->default('normaltempplanet01');
            $table->unsignedSmallInteger('planet_diameter')->default(12800);
            $table->unsignedSmallInteger('planet_field_current')->default(0);
            $table->unsignedSmallInteger('planet_field_max')->default(163);
            $table->tinyInteger('planet_temp_min')->default(-17);
            $table->tinyInteger('planet_temp_max')->default(23);

            // Last Update Timestamp
            $table->timestamp('planet_last_update')->useCurrent();
            $table->timestamp('planet_last_jump_time')->nullable();

            // Laravel timestamps
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint for coordinates
            $table->unique(['planet_galaxy', 'planet_system', 'planet_planet'], 'idx_planet_coordinates');

            // Indexes
            $table->index(['user_id', 'planet_id']);
            $table->index('planet_type');
        });

        // PostgreSQL-specific: GIN indexes for JSONB
        DB::statement("
            CREATE INDEX idx_planets_fields ON {$this->getTableName('planets')}
            USING GIN (planet_fields)
        ");

        DB::statement("
            CREATE INDEX idx_planets_debris ON {$this->getTableName('planets')}
            USING GIN (planet_debris)
        ");

        DB::statement("
            CREATE INDEX idx_planets_production ON {$this->getTableName('planets')}
            USING GIN (planet_production)
        ");

        // Full-Text Search
        DB::statement("
            ALTER TABLE {$this->getTableName('planets')}
            ADD COLUMN search_vector tsvector
            GENERATED ALWAYS AS (
                to_tsvector('english', coalesce(planet_name, ''))
            ) STORED
        ");

        DB::statement("
            CREATE INDEX idx_planets_search ON {$this->getTableName('planets')}
            USING GIN (search_vector)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planets');
    }

    /**
     * Get table name with prefix
     */
    private function getTableName(string $table): string
    {
        return DB::getTablePrefix() . $table;
    }
};
