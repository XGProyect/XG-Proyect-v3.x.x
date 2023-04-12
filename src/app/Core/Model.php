<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Database;

abstract class Model
{
    protected Database $db;

    public function __construct()
    {
        $this->setNewDb();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->db->closeConnection();
    }

    /**
     * Creates a new Database object
     *
     * @return void
     */
    private function setNewDb(): void
    {
        $this->db = new Database();
    }
}
