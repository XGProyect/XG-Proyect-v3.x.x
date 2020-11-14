<?php declare (strict_types = 1);

/**
 * Model
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\core;

use App\core\Database;

/**
 * Model Class
 */
abstract class Model
{
    /**
     * Contains the Database instance
     *
     * @var Database
     */
    protected $db;

    /**
     * Constructor
     */
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
        $this->db = new Database;
    }
}
