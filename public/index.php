<?php

use App\Core\Common;

define('IN_LOGIN', true);
define('XGP_ROOT', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);

require XGP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Common.php';

$system = new Common();
$system->bootUp('home');

$page = filter_input(INPUT_GET, 'page');

if (is_null($page)) {
    $page = 'home';
}

$file_name = XGP_ROOT . HOME_PATH . ucfirst($page) . 'Controller.php';

if (file_exists($file_name)) {
    include $file_name;

    $class_name = 'App\Http\Controllers\Home\\' . ucfirst($page) . 'Controller';

    (new $class_name())->index();
}
