<?php
/**
 * My Class Hook - This is a hook example emulating a controller,
 * but it could directly extend from XGPCore and be used for a different purpose!
 * Actually, making it work as a controller won't be the right thing, since
 * controllers should go in their own folder. The idea of using hooks is to extend
 * certain functionality. Like a banner? or an alert to certain users...
 *
 * PHP Version 7.1+
 *
 * @category Config
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */

use application\core\Database;
use application\core\XGPCore;

/**
 * My Class Hook
 *
 * PHP Version 7.1+
 *
 * @category Config
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class MyClass extends XGPCore
{

    public function __construct()
    {
        parent::__construct();

        // this is just a test, please always use a model!!!
        $this->_db = new Database();
    }

    /**
     * This is my method
     *
     * @param array $params
     * @return void
     */
    public function myMethod(array $params = array()): void
    {
        $query = $this->_db->queryFetch(
            "SELECT `user_name` FROM `" . USERS . "` u WHERE u.`user_id` = '1'"
        );

        echo '<div style="background-color: black;font-weight:bold;z-index: 99999999;position: absolute;top: 0;width: 100%;height: 150px;text-align: center;">';
        echo '<p>Hooks are working!!!</p>';
        echo '<p>' . $query['user_name'] . ' likes: ' . $params[0] . ', ' . $params[1] . ' and ' . $params[2] . '</p>';
        echo '<span>YEAH!</span>';
        echo '<br><br>';
        echo '<span style="color:red">Disable this hook in application/config/hooks.php</span>';
        echo '<br><br>';
        echo '<span style="color:red">Disable all hooks in application/config/constants.php and changing define(\'HOOKS_ENABLED\', false);</span>';
        echo '</div>';
    }
}

/* end of MyClass.php */
