<?php

declare (strict_types = 1);

/**
 * Model
 *
 * PHP Version 7.0+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\core;

use application\core\Database;

/**
 * Model Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
abstract class Model
{
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    protected $db;

    public function __construct()
    {
        $this->db = $this->database(
            [
                'hostname' => DB_HOST,
                'username' => DB_USER,
                'password' => DB_PASS,
                'database' => DB_NAME,
                'dbdriver' => 'mysqli',
                'dbprefix' => DB_PREFIX,
                'pconnect' => false,
                'db_debug' => true,
                'cache_on' => false,
                'cachedir' => '',
                'char_set' => 'utf8',
                'dbcollat' => 'utf8_general_ci',
            ],
            true,
            false
        );
    }

    /**
     * Database Loader
     *
     * @param    mixed    $params        Database configuration options
     * @param    bool    $return     Whether to return the database object
     * @param    bool    $query_builder    Whether to enable Query Builder
     *                    (overrides the configuration setting)
     *
     * @return    object|bool    Database object if $return is set to TRUE,
     *                    FALSE on failure, CI_Loader instance in any other case
     */
    private function database($params = '', $return = false, $query_builder = null)
    {
        // Grab the super object
        //$CI = &get_instance();

        // Do we even need to load the database class?
        //if ($return === false && $query_builder === null && isset($CI->db) && is_object($CI->db) && !empty($CI->db->conn_id)) {
        //    return false;
        //}

        require_once XGP_ROOT . SYSTEM_PATH . 'ci3_custom' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'DB.php';

        if ($return === true) {
            return DB($params, $query_builder);
        }

        // Initialize the db variable. Needed to prevent
        // reference errors with some configurations
        //$CI->db = '';

        // Load the DB class
        //$CI->db = &DB($params, $query_builder);
        return $this;
    }
}

/* end of Model.php */
