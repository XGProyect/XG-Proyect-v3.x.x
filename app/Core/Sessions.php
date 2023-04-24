<?php

namespace App\Core;

use App\Models\Core\Sessions as SessionsModel;

class Sessions
{
    private bool $alive = true;
    private SessionsModel $sessionsModel;

    public function __construct()
    {
        $this->sessionsModel = new SessionsModel();

        session_set_save_handler(
            [&$this->sessionsModel, 'openConnection'],
            [&$this->sessionsModel, 'closeConnection'],
            [&$this->sessionsModel, 'getSessionDataById'],
            [&$this->sessionsModel, 'insertNewSessionData'],
            [&$this->sessionsModel, 'deleteSessionDataById'],
            [&$this->sessionsModel, 'cleanSessionData']
        );

        if (session_id() == '') {
            session_start();
        }
    }

    public function __destruct()
    {
        if ($this->alive) {
            session_write_close();
            $this->alive = false;
        }
    }

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
