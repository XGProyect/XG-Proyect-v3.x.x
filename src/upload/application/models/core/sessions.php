<?php

declare (strict_types = 1);

/**
 * Sessions Model
 *
 * PHP Version 7.1+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\models\core;

use application\core\Database;

/**
 * Sessions Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Sessions
{
    private $db = null;

    /**
     * Constructor
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        // use this to make queries
        $this->db = $db;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->db->closeConnection();
    }

    /**
     * Check if the connection is open
     *
     * @return boolean
     */
    public function openConnection(): bool
    {
        // connection must be already open so return the status
        // only to fulfil session_set_save_handler requirements
        return $this->db->testConnection();
    }

    /**
     * Close connection
     *
     * @return boolean
     */
    public function closeConnection(): bool
    {
        return $this->db->closeConnection();
    }

    /**
     * Get session data by ID
     *
     * @param string $sid
     * @return array
     */
    public function getSessionDataById(string $sid): string
    {
        $sessions = $this->db->query(
            "SELECT
                `session_data`
            FROM `" . SESSIONS . "`
            WHERE `session_id` = '" . $this->db->escapeValue($sid) . "'
            LIMIT 1"
        );

        if ($this->db->numRows($sessions) == 1) {
            $fields = $this->db->fetchAssoc($sessions);

            return $fields['session_data'];
        } else {
            return '';
        }
    }

    /**
     * Insert new sesson data
     *
     * @param string $sid
     * @param string $data
     * @return boolean
     */
    public function insertNewSessionData(string $sid, string $data): bool
    {
        $this->db->query(
            "REPLACE INTO `" . SESSIONS . "` (`session_id`, `session_data`)
            VALUES ('" . $this->db->escapeValue($sid) . "', '" . $this->db->escapeValue($data) . "')"
        );

        return ($this->db->affectedRows() > 0);
    }

    /**
     * Delete session data by ID
     *
     * @param string $sid
     * @return string
     */
    public function deleteSessionDataById(string $sid): bool
    {
        $this->db->query(
            "DELETE FROM `" . SESSIONS . "`
            WHERE `session_id` = '" . $this->db->escapeValue($sid) . "'"
        );

        return ($this->db->affectedRows() > 0);
    }

    /**
     * Clean expired session data
     *
     * @param integer $expire
     * @return string
     */
    public function cleanSessionData(int $expire): bool
    {
        $this->db->query(
            "DELETE FROM `" . SESSIONS . "`
            WHERE DATE_ADD(`session_last_accessed`, INTERVAL " . $expire . " SECOND) < NOW()"
        );

        return ($this->db->affectedRows() > 0);
    }
}

/* end of sessions.php */
