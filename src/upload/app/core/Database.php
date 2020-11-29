<?php
/**
 * Database
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\core;

use App\libraries\DebugLib;
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
     * @var DebugLib
     */
    private $debug;

    /**
     * DB Data
     *
     * @var array
     */
    private $db_data = [
        'host' => '',
        'user' => '',
        'pass' => '',
        'name' => '',
        'prefix' => '',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        require_once XGP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

        if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME') && defined('DB_PREFIX')) {
            $this->db_data = [
                'host' => DB_HOST,
                'user' => DB_USER,
                'pass' => DB_PASS,
                'name' => DB_NAME,
                'prefix' => DB_PREFIX,
            ];
        }

        $this->debug = new DebugLib();
        $this->openConnection();
    }

    /**
     * Open connection
     *
     * @return void
     */
    public function openConnection()
    {
        if (isset($this->db_data['host']) && isset($this->db_data['user']) && isset($this->db_data['pass']) && isset($this->db_data['name'])) {
            if (!$this->tryConnection($this->db_data['host'], $this->db_data['user'], $this->db_data['pass'])) {
                if (!defined('IN_INSTALL')) {
                    die($this->debug->error(
                        -1,
                        'Database connection failed: ' . $this->connection->connect_error
                    ));
                }
            } else {
                if (!$this->tryDatabase($this->db_data['name'])) {
                    if (!defined('IN_INSTALL')) {
                        die($this->debug->error(
                            -1,
                            'Database selection failed: ' . $this->connection->connect_error
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

        $this->connection = @new mysqli($host, $user, $pass);

        if ($this->connection->connect_error) {
            return false;
        }

        // force utf8 to avoid weird characters
        $this->connection->set_charset("utf8");

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

        $db_select = @$this->connection->select_db($db_name);

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
            if ($this->connection->connect_errno == 0) {
                $this->connection->close();
            }

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
    public function query($sql = '')
    {
        if ($sql != '') {
            $sql = $this->prepareSql($sql);
            $this->last_query = $sql;
            $result = @$this->connection->query($sql);

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
    public function queryFetch($sql = '')
    {
        if ($sql != '') {
            $sql = $this->prepareSql($sql);
            $this->last_query = $sql;
            $result = @$this->connection->query($sql);

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
    public function queryFetchAll($sql = '')
    {
        if ($sql != '') {
            $sql = $this->prepareSql($sql);
            $this->last_query = $sql;
            $result = @$this->connection->query($sql);

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
    public function queryMulty($sql = '')
    {
        if ($sql != '') {
            $sql = $this->prepareSql($sql);
            $this->last_query = $sql;
            $result = @$this->connection->multi_query($sql);

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
        return $result_set->fetch_array(MYSQLI_ASSOC);
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

        $results_array = [];

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
     * Set the auto commit for transactions
     *
     * @param bool $status
     *
     * @return void
     */
    public function setAutoCommit(bool $status = true)
    {
        $this->connection->autocommit($status);
    }

    /**
     * Start a transaction
     *
     * @return void
     */
    public function beginTransaction()
    {
        // disable auto commit
        $this->setAutoCommit(false);

        $this->connection->begin_transaction();
    }

    /**
     * Confirm and commit a transaction
     *
     * @return void
     */
    public function commitTransaction()
    {
        $this->connection->commit();

        // re enable after transaction end
        $this->setAutoCommit();
    }

    /**
     * Rollback changes since transaction begin
     *
     * @return void
     */
    public function rollbackTransaction()
    {
        $this->connection->rollback();

        // re enable after transaction end
        $this->setAutoCommit();
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
            $tables = [];
            $result = $this->query('SHOW TABLES');

            while ($row = $this->fetchRow($result)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }

        $return = '';

        //CYCLE TROUGHT
        foreach ($tables as $table) {
            $result = $this->query('SELECT * FROM ' . $table);
            $num_fields = $this->numFields($result);

            $return .= 'DROP TABLE ' . $table . ';';
            $row2 = $this->fetchRow($this->query('SHOW CREATE TABLE ' . $table));
            $return .= "\n\n" . $row2[1] . ";\n\n";

            for ($i = 0; $i < $num_fields; $i++) {
                while ($row = $this->fetchRow($result)) {
                    $return .= 'INSERT INTO ' . $table . ' VALUES(';

                    for ($j = 0; $j < $num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = str_replace("\n", "\\n", $row[$j]);

                        if (isset($row[$j])) {
                            $return .= '"' . $row[$j] . '"';
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
            $return .= "\n\n\n";
        }

        // SAVE FILE
        $file_name = 'db-backup-' . date('Ymd') . '-' . time() . '-' . (sha1(join(',', $tables))) . '.sql';
        $handle = fopen(XGP_ROOT . BACKUP_PATH . $file_name, 'w+');
        $writed = fwrite($handle, $return);

        fclose($handle);

        return $writed;
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

            die($this->debug->error(-1, $output));
        }

        // DEBUG LOG
        $this->debug->add($this->last_query);
    }

    /**
     * Prepares the query string to be ready to be executed
     *
     * @param string $query
     * @return string
     */
    private function prepareSql(string $query): string
    {
        return strtr($query, ['{xgp_prefix}' => $this->db_data['prefix']]);
    }
}
