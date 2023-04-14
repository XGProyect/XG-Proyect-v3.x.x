<?php

declare(strict_types=1);

namespace App\Models\Adm;

use App\Core\Model;

class Repair extends Model
{
    /**
     * Get all server users
     *
     * @return array
     */
    public function getAllTables(): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                `table_name`,
                `data_length`,
                `index_length`,
                `data_free`
            FROM information_schema.TABLES
            WHERE table_schema = '" . DB_NAME . "';"
        );
    }

    /**
     * Check a table
     *
     * @param string $table
     * @return void
     */
    public function checkTable(string $table): void
    {
        $this->db->query('CHECK TABLE ' . $table);
    }

    /**
     * Optimize a table
     *
     * @param string $table
     * @return void
     */
    public function optimizeTable(string $table): void
    {
        $this->db->query('OPTIMIZE TABLE ' . $table);
    }

    /**
     * Repair a table
     *
     * @param string $table
     * @return void
     */
    public function repairTable(string $table): void
    {
        $this->db->query('REPAIR TABLE ' . $table);
    }
}
