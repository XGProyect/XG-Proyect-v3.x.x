<?php
/**
 * Admin File
 *
 * @category Root File
 * @package  N/A
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
use App\core\common;
use App\libraries\adm\AdministrationLib;
use App\libraries\Functions;

define('IN_ADMIN', true);
define('XGP_ROOT', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);

require XGP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'common.php';

$system = new Common;
$system->bootUp('admin');

include_once XGP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'adm' . DIRECTORY_SEPARATOR . 'AdministrationLib.php';

// check updates
$page = filter_input(INPUT_GET, 'page');

if (is_null($page)) {
    $page = 'home';
}

$file_name = XGP_ROOT . ADMIN_PATH . $page . '.php';

// logout
if ($page == 'logout') {
    AdministrationLib::closeSession();
    Functions::redirect(SYSTEM_ROOT . 'admin.php?page=login');
}

if (file_exists($file_name)) {
    include $file_name;

    $class_name = 'App\controllers\adm\\' . ucfirst($page);

    (new $class_name)->index();
} else {
    Functions::redirect(XGP_ROOT . 'admin.php');
}
