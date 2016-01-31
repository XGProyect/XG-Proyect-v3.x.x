<?php
/**
 * Sessions
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
    private $alive  = true;
    
    /**
     *
     * @var Database
     */
    private $dbc    = null;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // WE'RE GOING TO HANDLE A DIFFERENT DB OBJECT FOR THE SESSIONS
        $this->dbc  = clone parent::$db;

        session_set_save_handler(
            array (&$this, 'open'),
            array (&$this, 'close'),
            array (&$this, 'read'),
            array (&$this, 'write'),
            array (&$this, 'delete'),
            array (&$this, 'clean')
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
            $this->alive    = false;
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
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        if (!empty($_SESSION)) {

            unset($_SESSION);
            @session_destroy();
        }

        $this->alive    = false;
    }

    /**
     * open
     *
     * @return Database
     */
    private function open()
    {
        return $this->dbc->openConnection();
    }

    /**
     * close
     *
     * @return void
     */
    private function close()
    {
        return $this->dbc->closeConnection();
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
        $row    = $this->dbc->query(
            "SELECT `session_data`
            FROM " . SESSIONS . "
            WHERE `session_id` = '" .  $this->dbc->escapeValue($sid) . "'
            LIMIT 1"
        );

        if ($this->dbc->numRows($row) == 1) {

            $fields = $this->dbc->fetchAssoc($row);

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
    private function write($sid, $data)
    {
        $this->dbc->query(
            "REPLACE INTO `" . SESSIONS . "` (`session_id`, `session_data`)
            VALUES ('" . $this->dbc->escapeValue($sid) . "', '" . $this->dbc->escapeValue($data) . "')"
        );

        return $this->dbc->affectedRows();
    }

    /**
     * destroy
     *
     * @param string $sid Session Id
     *
     * @return array
     */
    private function destroy($sid)
    {
        $this->dbc->query(
            "DELETE FROM `" . SESSIONS . "`
            WHERE `session_id` = '" . $this->dbc->escapeValue($sid) . "'"
        );

        $_SESSION   = array();

        return $this->dbc->affectedRows();
    }

    /**
     * clean
     *
     * @param int $expire Expire
     *
     * @return array
     */
    private function clean($expire)
    {
        $this->dbc->query(
            "DELETE FROM `" . SESSIONS . "`
            WHERE DATE_ADD(`session_last_accessed`, INTERVAL " . (int)$expire . " SECOND) < NOW()"
        );

        return $this->dbc->affectedRows();
    }
}

/* end of Sessions.php */
