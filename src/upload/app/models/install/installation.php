<?php
/**
 * Installation Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\install;

use App\core\Database;
use App\core\Model;

/**
 * Installation Class
 */
class Installation extends Model
{
    /**
     * Get a list of tables
     *
     * @param type $db_name DB Name
     *
     * @return array
     */
    public function getListOfTables($db_name)
    {
        return $this->db->queryFetchAll(
            "SHOW TABLES FROM " . $db_name
        );
    }

    /**
     * Get a count of admins
     *
     * @return array
     */
    public function getAdmin()
    {
        return $this->db->queryFetch(
            "SELECT COUNT(`user_id`) as count FROM " . USERS . "
                WHERE `user_id` = '1' OR `user_authlevel` = '3';"
        );
    }

    /**
     * Check if the connection can be stablish
     *
     * @param string $host     Host
     * @param string $user     User
     * @param string $password Password
     *
     * @return Database
     */
    public function tryConnection($host, $user, $password)
    {
        return $this->db->tryConnection($host, $user, $password);
    }

    /**
     * Check if the database name exists
     *
     * @param string $db_name DB Name
     *
     * @return Database
     */
    public function tryDatabase($db_name)
    {
        return $this->db->tryDatabase($db_name);
    }

    /**
     * Set for windows sql mode to MYSQL40
     *
     * @return void
     */
    public function setWindowsSqlMode()
    {
        // Store the current sql_mode
        $this->db->query("set @orig_mode = @@global.sql_mode");

        // Set sql_mode to one that won't trigger errors...
        $this->db->query('set @@global.sql_mode = "MYSQL40"');
    }

    /**
     * Run a simple insert query
     *
     * @param string $query Query
     *
     * @return int
     */
    public function runSimpleQuery($query)
    {
        return $this->db->query($query);
    }

    /**
     * Set for windows sql mode to normal
     *
     * @return void
     */
    public function setNormalMode()
    {
        // Change it back to original sql_mode
        $this->db->query('set @@global.sql_mode = @orig_mode');
    }

    /**
     * Escape a value
     *
     * @param string $var
     * @return string
     */
    public function escapeValue($var): string
    {
        return $this->db->escapeValue($var);
    }
}
