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

if (!defined('DIRECTORY_SEPARATOR')) {

    define('DIRECTORY_SEPARATOR',
        strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ? '\\' : '/'
    );
}

define('IN_LOGIN', true);
define('XGP_ROOT', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);

require XGP_ROOT . 'application' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'common.php';

$page = (isset($_GET['page']) ? $_GET['page'] : 'home');

$file_name = XGP_ROOT . HOME_PATH . $page . '.php';

if (file_exists($file_name)) {

    include $file_name;

    $class_name = 'application\controllers\home\\' . ucfirst($page);

    new $class_name();
}

/* end of index.php */
