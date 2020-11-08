<?php
/**
 * Index File
 *
 * @category Root File
 * @package  N/A
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */

use Application\core\Common;

define('IN_LOGIN', true);
define('XGP_ROOT', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);

require XGP_ROOT . 'application' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'common.php';

$system = new Common;
$system->bootUp('home');

$page = filter_input(INPUT_GET, 'page');

if (is_null($page)) {
    $page = 'home';
}

$file_name = XGP_ROOT . HOME_PATH . $page . '.php';

if (file_exists($file_name)) {
    include $file_name;

    $class_name = 'application\controllers\home\\' . ucfirst($page);

    new $class_name();
}
