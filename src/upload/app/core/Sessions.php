<?php
/**
 * XG Proyect
 *
 * Open-source OGame Clon
 *
 * This content is released under the GPL-3.0 License
 *
 * Copyright (c) 2008-2020 XG Proyect
 *
 * @package    XG Proyect
 * @author     XG Proyect Team
 * @copyright  2008-2020 XG Proyect
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0 License
 * @link       https://github.com/XGProyect/
 * @since      3.0.0
 */
namespace App\core;

use App\libraries\Functions;

/**
 * Sessions class
 */
class Sessions
{
    /**
     *
     * @var boolean
     */
    private $alive = true;

    /**
     * Contains the model
     *
     * @var Sessions
     */
    private $sessionsModel;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        // load model
        $this->sessionsModel = Functions::model('core/sessions');

        session_set_save_handler(
            [ & $this->sessionsModel, 'openConnection'],
            [ & $this->sessionsModel, 'closeConnection'],
            [ & $this->sessionsModel, 'getSessionDataById'],
            [ & $this->sessionsModel, 'insertNewSessionData'],
            [ & $this->sessionsModel, 'deleteSessionDataById'],
            [ & $this->sessionsModel, 'cleanSessionData']
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
