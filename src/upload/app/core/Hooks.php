<?php
/**
 * Hooks
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
 * Hooks Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Hooks
{
    /**
     * @var mixed
     */
    public $_enabled = false;
    /**
     * @var array
     */
    public $_hooks = [];
    /**
     * @var mixed
     */
    public $_in_progress = false;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * __construct
     *
     * @return void
     */
    public function initialize()
    {
        // IF HOOKS ARE NOT ENABLED THERE IS NOTHING ELSE TO DO
        if (HOOKS_ENABLED === false) {
            return;
        }

        // GRAB THE HOOKS FILE, IF THERE ARE NO HOOKS, WE'RE DONE
        if (is_file(XGP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'hooks.php')) {
            include XGP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'hooks.php';
        }

        if (!isset($hook) or !is_array($hook)) {
            return;
        }

        $this->_hooks = &$hook;
        $this->_enabled = true;
    }

    /**
     * __construct
     *
     * @return void
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
     * __construct
     *
     * @return void
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

        $filepath = XGP_ROOT . 'app/' . $data['filepath'] . '/' . $data['filename'];

        if (!file_exists($filepath)) {
            return false;
        }

        // SET CLASS / FUNCTION NAME
        $class = false;
        $function = false;
        $params = '';

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

            $HOOK = new $class;
            $HOOK->$function($params);
        } else {
            if (!function_exists($function)) {
                require $filepath;
            }

            $function($params);
        }

        $this->_in_progress = false;
        return true;
    }
}
