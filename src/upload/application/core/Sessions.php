<?php
/**
 * Sessions
 *
 * PHP Version 7.1+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\core;

/**
 * Sessions Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Sessions extends XGPCore
{

    /**
     *
     * @var boolean
     */
    private $alive = true;

    /**
     *
     * @var Database
     */
    private $_db = null;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->_db = new Database();

        session_set_save_handler(
            [&$this, 'open'], [&$this, 'close'], [&$this, 'read'], [&$this, 'write'], [&$this, 'destroy'], [&$this, 'clean']
        );

        if (session_id() == '') {

            session_start();
        }
    }

    /**
     * __destruct
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->alive) {

            session_write_close();
            $this->alive = false;
        }
    }

    /**
     * delete
     *
     * @return void
     */
    public function delete()
    {
        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']
            );
        }

        if (!empty($_SESSION)) {

            unset($_SESSION);
            @session_destroy();
        }

        $this->alive = false;
    }

    /**
     * open
     *
     * @return Database
     */
    public function open()
    {
        return $this->_db->openConnection();
    }

    /**
     * close
     *
     * @return void
     */
    public function close()
    {
        return $this->_db->closeConnection();
    }

    /**
     * read
     *
     * @param string $sid Session Id
     *
     * @return void
     */
    private function read($sid)
    {
        $row = $this->_db->query(
            "SELECT `session_data`
            FROM " . SESSIONS . "
            WHERE `session_id` = '" . $this->_db->escapeValue($sid) . "'
            LIMIT 1"
        );

        if ($this->_db->numRows($row) == 1) {

            $fields = $this->_db->fetchAssoc($row);

            return $fields['session_data'];
        } else {

            return '';
        }
    }

    /**
     * write
     *
     * @param string $sid  Session Id
     * @param string $data Session Data
     *
     * @return array
     */
    public function write($sid, $data)
    {
        $this->_db->query(
            "REPLACE INTO `" . SESSIONS . "` (`session_id`, `session_data`)
            VALUES ('" . $this->_db->escapeValue($sid) . "', '" . $this->_db->escapeValue($data) . "')"
        );

        return ($this->_db->affectedRows() > 0);
    }

    /**
     * destroy
     *
     * @param string $sid Session Id
     *
     * @return array
     */
    public function destroy($sid)
    {
        $this->_db->query(
            "DELETE FROM `" . SESSIONS . "`
            WHERE `session_id` = '" . $this->_db->escapeValue($sid) . "'"
        );
        
        return $this->_db->affectedRows();
    }

    /**
     * clean
     *
     * @param int $expire Expire
     *
     * @return array
     */
    public function clean($expire)
    {
        $this->_db->query(
            "DELETE FROM `" . SESSIONS . "`
            WHERE DATE_ADD(`session_last_accessed`, INTERVAL " . (int) $expire . " SECOND) < NOW()"
        );

        return $this->_db->affectedRows();
    }
}

/* end of Sessions.php */
