<?php
/**
 * Ajax File
 *
 * PHP Version 5.4+
 *
 * @category Application
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

define('IN_LOGIN', true);
define('XGP_ROOT', './');

include XGP_ROOT . 'application/core/common.php';

if (isset($_GET['content'])) {
    
    $file_name  = XGP_ROOT . AJAX_PATH . $_GET['content'] . '.php';

    if (file_exists($file_name)) {

        include $file_name;
        
        $class_name = ucfirst($_GET['content']);
        
        new $class_name();
    }   
}

/* end of ajax.php */