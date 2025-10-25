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
        Schema::create('alliances', function (Blueprint $table) {
            // Primary Key
            $table->id('alliance_id');

            // Basic Information
            $table->string('alliance_name', 32)->unique();
            $table->string('alliance_tag', 8)->unique();

            // Owner
            $table->unsignedBigInteger('alliance_owner');
            $table->foreign('alliance_owner')->references('user_id')->on('users')->onDelete('restrict');

            // Descriptions
            $table->text('alliance_description')->nullable();
            $table->text('alliance_web')->nullable();
            $table->string('alliance_image', 255)->nullable();

            // Request to join
            $table->boolean('alliance_request_notallow')->default(false);
            $table->text('alliance_request')->nullable();
            $table->text('alliance_request_waiting')->nullable();

            // Texts
            $table->text('alliance_text_intern')->nullable();
            $table->text('alliance_text_extern')->nullable();
            $table->text('alliance_text_apply')->nullable();

            // Laravel timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('alliance_name');
            $table->index('alliance_tag');
            $table->index('alliance_owner');
        });

        // Add foreign key to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('alliance_id')->references('alliance_id')->on('alliances')->onDelete('set null');
        });

        // Full-Text Search for alliances
        DB::statement("
            ALTER TABLE {$this->getTableName('alliances')}
            ADD COLUMN search_vector tsvector
            GENERATED ALWAYS AS (
                to_tsvector('english',
                    coalesce(alliance_name, '') || ' ' ||
                    coalesce(alliance_tag, '') || ' ' ||
                    coalesce(alliance_description, '')
                )
            ) STORED
        ");

        DB::statement("
            CREATE INDEX idx_alliances_search ON {$this->getTableName('alliances')}
            USING GIN (search_vector)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['alliance_id']);
        });

        Schema::dropIfExists('alliances');
    }

    /**
     * Get table name with prefix
     */
    private function getTableName(string $table): string
    {
        return DB::getTablePrefix() . $table;
    }
};
