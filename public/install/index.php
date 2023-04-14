<?php

use App\Core\Common;

define('IN_INSTALL', true);
define('XGP_ROOT', '../../');

require XGP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Common.php';

$system = new Common();
$system->bootUp('install');

$page = isset($_GET['page']) ? $_GET['page'] : 'installation';
$file_name = XGP_ROOT . INSTALL_PATH . ucfirst($page) . 'Controller.php';

if (file_exists($file_name)) {
    include $file_name;

    $class_name = 'App\Http\Controllers\Install\\' . ucfirst($page) . 'Controller';

    (new $class_name())->index();
}
