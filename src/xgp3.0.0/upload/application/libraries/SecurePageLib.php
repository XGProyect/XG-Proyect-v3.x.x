<?php

/**
 * SecurePage Library.
 *
 * PHP Version 5.5+
 *
 * @category Library
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */

namespace application\libraries;

/**
 * This class is originally developed by "Bendikt Martin Myklebust" this is updated by "Rakesh Chandel".
 * To Secure Global varaible values consisting in array while posting from GET,Session, and POST.
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
class SecurePageLib
{
    private static $instance = null;

    /**
     * __construct.
     */
    public function __construct()
    {
        //apply controller to all
        $_GET     = array_map(array($this, 'validate'), $_GET);
        $_POST    = array_map(array($this, 'validate'), $_POST);
        $_REQUEST = array_map(array($this, 'validate'), $_REQUEST);
        $_SERVER  = array_map(array($this, 'validate'), $_SERVER);
        $_COOKIE  = array_map(array($this, 'validate'), $_COOKIE);
    }

    /**
     * __construct.
     */
    private function validate($value)
    {
        if (!is_array($value)) {
            $value = str_ireplace('script', 'blocked', $value);

            if (get_magic_quotes_gpc()) {
                $value = htmlentities(stripslashes($value), ENT_QUOTES, 'UTF-8', false);
            } else {
                $value = htmlentities($value, ENT_QUOTES, 'UTF-8', false);
            }

            //$value = mysql_real_escape_string ( $value );
        } else {
            $c = 0;

            foreach ($value as $val) {
                $value[$c] = $this->validate($val);
                ++$c;
            }
        }

        return $value;
    }

    /**
     * run.
     */
    public static function run()
    {
        if (self::$instance == null) {
            $c              = __CLASS__;
            self::$instance = new $c();
        }
    }
}

/* end of SecurePageLib.php */
