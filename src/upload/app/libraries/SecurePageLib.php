<?php
/**
 * SecurePage Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\libraries;

/**
 * This class is originally developed by "Bendikt Martin Myklebust" this is updated by "Rakesh Chandel".
 * To Secure Global varaible values consisting in array while posting from GET,Session, and POST.
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class SecurePageLib
{
    /**
     * @var mixed
     */
    private static $instance = null;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        //apply controller to all
        $_GET = array_map([$this, 'validate'], $_GET);
        $_POST = array_map([$this, 'validate'], $_POST);
        $_REQUEST = array_map([$this, 'validate'], $_REQUEST);
        $_SERVER = array_map([$this, 'validate'], $_SERVER);
        $_COOKIE = array_map([$this, 'validate'], $_COOKIE);
    }

    /**
     * __construct
     *
     * @return void
     */
    private function validate($value)
    {
        if (!is_array($value)) {
            $value = str_ireplace("script", "blocked", $value);

            if (version_compare(PHP_VERSION, '7.3.0', '<')) {
                if (get_magic_quotes_gpc()) {
                    $value = htmlentities(stripslashes($value), ENT_QUOTES, 'UTF-8', false);
                } else {
                    $value = htmlentities($value, ENT_QUOTES, 'UTF-8', false);
                }
            }
        } else {
            $c = 0;

            foreach ($value as $val) {
                $value[$c] = $this->validate($val);
                $c++;
            }
        }

        return $value;
    }

    /**
     * run
     *
     * @return void
     */
    public static function run()
    {
        if (self::$instance == null) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
    }
}
