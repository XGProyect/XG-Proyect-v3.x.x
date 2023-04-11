<?php

use App\Core\Common;

define('IN_INSTALL', true);
define('XGP_ROOT', '../../');

require XGP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Common.php';

$system = new Common();
$system->bootUp('install');

$page = isset($_GET['page']) ? $_GET['page'] : 'installation';
$file_name = XGP_ROOT . INSTALL_PATH . $page . '.php';

if (file_exists($file_name)) {
    include $file_name;

    $class_name = 'App\Controllers\Install\\' . ucfirst($page);

    (new $class_name())->index();
}
