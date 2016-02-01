<?php
/**
 * Database
 *
 * PHP Version 5.5+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace application\core;

use application\libraries\DebugLib;
use mysqli;

/**
 * Database Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Database
{
    /**
     *
     * @var string
     */
    private $last_query;
    
    /**
     *
     * @var mysqli
     */
    private $connection;
    
    /**
     *
     * @var boolean
     */
    private $magic_quotes_active;
    
    /**
     *
     * @var DebugLib
     */
    private $debug;

    /**
     * createPlanetWithOptions
     *
     * @param array $data Data
     *
     * @return void
     */
    public function __construct()
    {
        global $debug;

        $this->debug               = $debug;
        $this->openConnection();
        $this->magic_quotes_active = get_magic_quotes_gpc();
    }

    /**
     * openConnection
     *
     * @return boolean
     */
    public function openConnection()
    {
        if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {

            if (!$this->tryConnection()) {

                if (!defined('IN_INSTALL')) {

                    die($this->debug->error(
                        'Database connection failed: ' . $this->connection->connect_error,
                        'SQL Error'
                    ));
                } else {
                    return false;
                }
            } else {
                if (!$this->tryDatabase()) {

                    if (!defined('IN_INSTALL')) {
                        
                        die($this->debug->error(
                            'Database selection failed: ' . $this->connection->connect_error,
                            'SQL Error'
                        ));
                    } else {
                        return false;
                    }
                }
                else
                    return true;
            }
        }
    }

    /**
     * tryConnection
     *
     * @return mysqli
     */
    public function tryConnection()
    {
        $this->connection  = new mysqli(DB_HOST, DB_USER, DB_PASS);

        return $this->connection;
    }

    /**
     * tryDatabase
     *
     * @return boolean
     */
    public function tryDatabase()
    {
        $db_select  = $this->connection->select_db(DB_NAME);

        if ($db_select) {
            
            return true;
        } else {

            return false;
        }
    }

    /**
     * createPlanetWithOptions
     *
     * @param array $data Data
     *
     * @return void
     */
    public function closeConnection()
    {
        if (is_resource($this->connection) or is_object($this->connection)) {

            $this->connection->close();
            unset($this->connection);

            return true;
        }

        return false;
    }

    /**
     * query
     *
     * @param string $sql SQL String
     *
     * @return mixed
     */
    public function query($sql = false)
    {
        if ($sql != false) {

            $this->last_query   = $sql;
            $result             = @$this->connection->query($sql);
            
            $this->confirmQuery($result);

            return $result;
        }

        return false;
    }

    /**
     * queryFetch
     *
     * @param string $sql SQL String
     *
     * @return mixed
     */
    public function queryFetch($sql = false)
    {
        if ($sql != false) {

            $this->last_query   = $sql;
            $result             = @$this->connection->query($sql);

            $this->confirmQuery($result);

            return $this->fetchArray($result);
        }

        return false;
    }

    /**
     * escapeValue
     *
     * @param mixed $value Value to escape
     *
     * @return mixed
     */
    public function escapeValue($value)
    {
        // undo any magic quote effects so mysqli_real_escape_string can do the work
        if ($this->magic_quotes_active) {

            $value  = stripslashes($value);
        }

        return $this->connection->real_escape_string($value);
    }

    /**
     * fetchArray
     *
     * @param array $result_set Result set
     *
     * @return array
     */
    public function fetchArray($result_set)
    {
        return $result_set->fetch_array();
    }

    /**
     * fetchAssoc
     *
     * @param array $result_set Result set
     *
     * @return array
     */
    public function fetchAssoc($result_set)
    {
        return $result_set->fetch_assoc();
    }

    /**
     * fetchRow
     *
     * @param array $result_set Result set
     *
     * @return array
     */
    public function fetchRow($result_set)
    {
        return $result_set->fetch_row();
    }

    /**
     * numRows
     *
     * @param array $result_set Result set
     *
     * @return int
     */
    public function numRows($result_set)
    {
        return $result_set->num_rows;
    }

    /**
     * numFields
     *
     * @param array $result_set Result set
     *
     * @return int
     */
    public function numFields($result_set)
    {
        return $result_set->num_fields;
    }

    /**
     * insertId
     *
     * @return int
     */
    public function insertId()
    {
        // get the last id inserted over the current db connection
        return $this->connection->insert_id;
    }

    /**
     * affectedRows
     *
     * @return array
     */
    public function affectedRows()
    {
        return $this->connection->affected_rows;
    }

    /**
     * serverInfo
     *
     * @return array
     */
    public function serverInfo()
    {
        return $this->connection->server_info;
    }

    /**
     * freeResult
     *
     * @param array $result_set Result set
     *
     * @return void
     */
    public function freeResult($result_set)
    {
        $result_set->free_result();
    }

    /**
     * confirmQuery
     *
     * @param array $result Result set
     *
     * @return void
     */
    private function confirmQuery($result)
    {
        if (!$result) {

            $output = "Database query failed: " . $this->connection->error;

            // uncomment below line when you want to debug your last query
            $output .= " Last SQL Query: " . $this->last_query;

            die($this->debug->error($output, "SQL Error"));
        }

        // DEBUG LOG
        $this->debug->add($this->last_query);
    }

    /**
     * backupDb
     *
     * @param array $tables Data
     *
     * @return string
     */
    public function backupDb($tables = '*')
    {
        // GET ALL THE TABLES
        if ($tables == '*') {

            $tables = array();
            $result = $this->query('SHOW TABLES');

            while ($row = $this->fetchRow($result)) {

                $tables[]   = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }

        $return = '';

        //CYCLE TROUGHT
        foreach ($tables as $table) {

            $result     = $this->query('SELECT * FROM' . $table);
            $num_fields = $this->num_fields($result);

            $return     .= 'DROP TABLE ' . $table . ';';
            $row2       = $this->fetchRow($this->query('SHOW CREATE TABLE ' . $table));
            $return     .= "\n\n".$row2[1].";\n\n";

            for ($i = 0; $i < $num_fields; $i++) {

                while ($row = $this->fetchRow($result)) {

                    $return .= 'INSERT INTO ' . $table . ' VALUES(';

                    for ($j = 0; $j < $num_fields; $j++) {

                        $row[$j]    = addslashes($row[$j]);
                        $row[$j]    = str_replace("\n", "\\n", $row[$j]);

                        if (isset($row[$j])) {

                            $return .= '"' . $row[$j] . '"' ;
                        } else {

                            $return .= '""';
                        }

                        if ($j < ($num_fields - 1)) {

                            $return .= ',';
                        }
                    }
                    
                    $return .= ");\n";
                }
            }
            $return .="\n\n\n";
        }

        // SAVE FILE
        $file_name  = 'db-backup-' . date('Ymd') . '-' . time() . '-' . (sha1(implode(',', $tables))) . '.sql';
        $handle     = fopen(XGP_ROOT . BACKUP_PATH . $file_name, 'w+');
        $writed     = fwrite($handle, $return);
       
        fclose($handle);

        return $writed;
    }
}

/* end of Database.php */
