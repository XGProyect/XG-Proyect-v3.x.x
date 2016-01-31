<?php

/**
 * Hooks.
 *
 * PHP Version 5.5+
 *
 * @category Core
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */

namespace application\core;

/**
 * Hooks Class.
 *
 * @category Classes
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */
class Hooks
{
    public $_enabled     = false;
    public $_hooks       = array();
    public $_in_progress = false;

    /**
     * __construct.
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * __construct.
     */
    public function initialize()
    {
        // IF HOOKS ARE NOT ENABLED THERE IS NOTHING ELSE TO DO
            if (HOOKS_ENABLED === 'FALSE') {
                return;
            }

            // GRAB THE HOOKS FILE, IF THERE ARE NO HOOKS, WE'RE DONE
            if (is_file(XGP_ROOT . 'application/config/hooks.php')) {
                include XGP_ROOT . 'application/config/hooks.php';
            }

        if (!isset($hook) or !is_array($hook)) {
            return;
        }

        $this->_hooks   = &$hook;
        $this->_enabled = true;
    }

    /**
     * __construct.
     */
    public function call_hook($which = '')
    {
        if (!$this->_enabled or !isset($this->_hooks[$which])) {
            return false;
        }

        if (isset($this->_hooks[$which][0]) && is_array($this->_hooks[$which][0])) {
            foreach ($this->_hooks[$which] as $val) {
                $this->run_hook($val);
            }
        } else {
            $this->run_hook($this->_hooks[$which]);
        }

        return true;
    }

    /**
     * __construct.
     */
    public function run_hook($data)
    {
        if (!is_array($data)) {
            return false;
        }

            // PREVENTS LOOPS
            if ($this->_in_progress == true) {
                return;
            }

            // SET FILE PATH
            if (!isset($data['filepath']) or !isset($data['filename'])) {
                return false;
            }

        $filepath = XGP_ROOT . 'application/' . $data['filepath'] . '/' . $data['filename'];

        if (!file_exists($filepath)) {
            return false;
        }

            // SET CLASS / FUNCTION NAME
            $class = false;
        $function  = false;
        $params    = '';

        if (isset($data['class']) && $data['class'] != '') {
            $class = $data['class'];
        }

        if (isset($data['function'])) {
            $function = $data['function'];
        }

        if (isset($data['params'])) {
            $params = $data['params'];
        }

        if ($class === false && $function === false) {
            return false;
        }

            // SET in_progress FLAG
            $this->_in_progress = true;

            // CALL THE CLASS AND / OR FUNCTION
            if ($class !== false) {
                if (!class_exists($class)) {
                    require $filepath;
                }

                $HOOK = new $class();
                $HOOK->$function ($params);
            } else {
                if (!function_exists($function)) {
                    require $filepath;
                }

                $function ($params);
            }

        $this->_in_progress = false;

        return true;
    }
}

/* end of Hooks.php */
