<?php
/**
 * Install File
 *
 * PHP Version 5.5+
 *
 * @category Install File
 * @package  N/A
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

define('IN_INSTALL', true);
define('XGP_ROOT', '../../');

require XGP_ROOT . 'application' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'common.php';

$page       = isset($_GET['page']) ? $_GET['page'] : 'installation';
$file_name  = XGP_ROOT . INSTALL_PATH . $page . '.php';

if (file_exists($file_name)) {

    include $file_name;

    $class_name = 'application\controllers\install\\' . ucfirst($page);

    new $class_name();
}

/* end of index.php */
