<?php

namespace App\Core;

use App\Libraries\DebugLib;
use Exception;
use mysqli;

class Database
{
    private string $last_query;
    private mysqli $connection;
    private DebugLib $debug;
    private array $db_data = [
        'host' => '',
        'user' => '',
        'pass' => '',
        'name' => '',
        'prefix' => '',
    ];

    public function __construct()
    {
        require_once XGP_ROOT . CONFIGS_PATH . 'config.php';

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
     * @return mixed
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

    public function tryConnection(string $host = '', string $user = '', string $pass = null): bool
    {
        try {
            if (empty($host) or empty($user)) {
                return false;
            }

            $this->connection = new mysqli($host, $user, $pass);

            if ($this->connection->connect_error) {
                return false;
            }

            // force utf8 to avoid weird characters
            $this->connection->set_charset('utf8');

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function tryDatabase(string $db_name): bool
    {
        if (empty($db_name)) {
            return false;
        }

        $db_select = $this->connection->select_db($db_name);

        if ($db_select) {
            return true;
        } else {
            return false;
        }
    }

    public function testConnection(): bool
    {
        if (is_resource($this->connection) or is_object($this->connection)) {
            if ($this->connection->ping()) {
                return true;
            }
        }

        return false;
    }

    public function closeConnection(): bool
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

    public function query(string $sql = '')
    {
        if ($sql != '') {
            $sql = $this->prepareSql($sql);
            $this->last_query = $sql;
            $result = $this->connection->query($sql);

            $this->confirmQuery($result);

            return $result;
        }

        return false;
    }

    public function queryFetch(string $sql = '')
    {
        if ($sql != '') {
            $sql = $this->prepareSql($sql);
            $this->last_query = $sql;
            $result = $this->connection->query($sql);

            $this->confirmQuery($result);

            return $this->fetchArray($result);
        }

        return false;
    }

    public function queryFetchAll($sql = '')
    {
        try {
            if ($sql != '') {
                $sql = $this->prepareSql($sql);
                $this->last_query = $sql;
                $result = $this->connection->query($sql);

                $this->confirmQuery($result);

                return $this->fetchAll($result);
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
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
        try {
            if ($sql != '') {
                $sql = $this->prepareSql($sql);
                $this->last_query = $sql;
                $result = $this->connection->multi_query($sql);

                $this->confirmQuery($result);

                return $result;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function escapeValue($value)
    {
        return $this->connection->real_escape_string($value);
    }

    public function fetchArray($result_set)
    {
        return $result_set->fetch_array(MYSQLI_ASSOC);
    }

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

    public function fetchAssoc($result_set)
    {
        return $result_set->fetch_assoc();
    }

    public function fetchRow($result_set)
    {
        return $result_set->fetch_row();
    }

    public function numRows($result_set)
    {
        return $result_set->num_rows;
    }

    public function numFields($result_set)
    {
        return $result_set->field_count;
    }

    public function insertId()
    {
        // get the last id inserted over the current db connection
        return $this->connection->insert_id;
    }

    public function affectedRows()
    {
        return $this->connection->affected_rows;
    }

    public function serverInfo()
    {
        return $this->connection->server_info;
    }

    public function freeResult($result_set): void
    {
        $result_set->free_result();
    }

    public function setAutoCommit(bool $status = true): void
    {
        $this->connection->autocommit($status);
    }

    public function beginTransaction()
    {
        // disable auto commit
        $this->setAutoCommit(false);

        $this->connection->begin_transaction();
    }

    public function commitTransaction()
    {
        $this->connection->commit();

        // re enable after transaction end
        $this->setAutoCommit();
    }

    public function rollbackTransaction()
    {
        $this->connection->rollback();

        // re enable after transaction end
        $this->setAutoCommit();
    }

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
                        $row[$j] = addslashes((string) $row[$j]);
                        $row[$j] = str_replace("\n", '\\n', $row[$j]);

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

    private function confirmQuery($result)
    {
        if (!$result) {
            $output = 'Database query failed: ' . $this->connection->error;

            // uncomment below line when you want to debug your last query
            $output .= ' Last SQL Query: ' . $this->last_query;

            die($this->debug->error(-1, $output));
        }

        // DEBUG LOG
        $this->debug->add($this->last_query);
    }

    private function prepareSql(string $query): string
    {
        return strtr($query, ['{xgp_prefix}' => $this->db_data['prefix']]);
    }
}
