<?php
/**
 * Sessions
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\core;

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
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // load model
        parent::loadModel('core/sessions');

        session_set_save_handler(
            [ & $this->Sessions_Model, 'openConnection'],
            [ & $this->Sessions_Model, 'closeConnection'],
            [ & $this->Sessions_Model, 'getSessionDataById'],
            [ & $this->Sessions_Model, 'insertNewSessionData'],
            [ & $this->Sessions_Model, 'deleteSessionDataById'],
            [ & $this->Sessions_Model, 'cleanSessionData']
        );

        if (session_id() == '') {
            session_start();
        }
    }

    /**
     * Destructor
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

        $this->alive = false;
    }
}
