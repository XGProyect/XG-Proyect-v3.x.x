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
        require_once XGP_ROOT . 'application/libraries/DebugLib.php';
        
        $this->debug               = new DebugLib();
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

            if (!$this->tryConnection(DB_HOST, DB_USER, DB_PASS)) {

                if (!defined('IN_INSTALL')) {

                    die($this->debug->error(
                        'Database connection failed: ' . $this->connection->connect_error,
                        'SQL Error'
                    ));
                }
            } else {

                if (!$this->tryDatabase(DB_NAME)) {

                    if (!defined('IN_INSTALL')) {
                        
                        die($this->debug->error(
                            'Database selection failed: ' . $this->connection->connect_error,
                            'SQL Error'
                        ));
                    }
                } else {

                    return true;
                }
            }
            
            return false;
        }
    }

    /**
     * tryConnection
     *
     * @param string $host Host
     * @param string $user User
     * @param string $pass Pass
     *
     * @return mysqli
     */
    public function tryConnection($host = '', $user = '', $pass = null)
    {
        if (empty($host) or empty($user)) {

            return;
        }

        $this->connection  = @new mysqli($host, $user, $pass);

        if ($this->connection->connect_error) {

            return false;
        }
        
        return true;
    }

    /**
     * tryDatabase
     *
     * @param string $db_name DB Name
     *
     * @return boolean
     */
    public function tryDatabase($db_name)
    {
        if (empty($db_name)) {

            return false;
        }
        
        $db_select  = @$this->connection->select_db($db_name);

        if ($db_select) {
            
            return true;
        } else {

            return false;
        }
    }

    /**
     * Test if MySQLi connection was stablished
     * 
     * @return boolean
     */
    public function testConnection()
    {
        if (is_resource($this->connection) or is_object($this->connection)) {

            if ($this->connection->ping()) {

                return true;
            }   
        }
        
        return false;
    }
    
    /**
     * closeConnection
     *
     * @param
     *
     * @return boolean
     */
    public function closeConnection()
    {
        if (isset($this->connection) && (is_resource($this->connection) or is_object($this->connection))) {

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
     * queryFetchAll
     *
     * @param string $sql SQL String
     *
     * @return mixed
     */
    public function queryFetchAll($sql = false)
    {
        if ($sql != false) {

            $this->last_query   = $sql;
            $result             = @$this->connection->query($sql);

            $this->confirmQuery($result);

            return $this->fetchAll($result);
        }

        return false;
    }

    /**
     * Multi Query
     *
     * @param string $sql SQL String
     * 
     * @return mixed
     */
    public function queryMulty($sql = false)
    {
        if ($sql != false) {

            $this->last_query   = $sql;
            $result             = @$this->connection->multi_query($sql);
            
            $this->confirmQuery($result);

            return $result;
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
     * fetchAll
     *
     * @param array $result_set Result set
     *
     * @return array
     */
    public function fetchAll($result_set)
    {
        if (function_exists('mysqli_fetch_all')) {

            return $result_set->fetch_all(MYSQLI_ASSOC);
        }

        $results_array  = [];

        while ($row = $this->fetchAssoc($result_set)) {

            $results_array[] = $row;
        }

        return $results_array;  
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
        return $result_set->field_count;
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

            $result     = $this->query('SELECT * FROM ' . $table);
            $num_fields = $this->numFields($result);

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
