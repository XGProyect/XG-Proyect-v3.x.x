<?php
/**
 * Index File
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

require XGP_ROOT . 'application/core/common.php';

$page   = strtr(
    (isset($_GET['page']) ? $_GET['page'] : 'home'),
    array(
        'reg' => 'register'
    )
);

$file_name  = XGP_ROOT . HOME_PATH . $page . '.php';

if (file_exists($file_name)) {

    include $file_name;

    $class_name = ucfirst($page);

    new $class_name();
}

/* end of index.php */
