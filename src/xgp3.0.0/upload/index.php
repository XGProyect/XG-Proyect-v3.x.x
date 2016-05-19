<?php
/**
 * Index File
 *
 * PHP Version 5.5+
 *
 * @category Root File
 * @package  N/A
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

define('IN_LOGIN', true);
define('XGP_ROOT', './');

require XGP_ROOT . 'application/core/common.php';

$page   = (isset($_GET['page']) ? $_GET['page'] : 'home');

$file_name  = XGP_ROOT . HOME_PATH . $page . '.php';

if (file_exists($file_name)) {

    include $file_name;

    $class_name = 'application\controllers\home\\' . ucfirst($page);

    new $class_name();
}

/* end of index.php */
