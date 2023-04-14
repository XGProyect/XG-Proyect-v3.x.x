<?php

use App\Core\Common;
use App\Libraries\Functions;

define('XGP_ROOT', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);

require XGP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Common.php';

$system = new Common();
$system->bootUp('game');

$page = filter_input(INPUT_GET, 'page');

if (is_null($page)) {
    Functions::redirect('game.php?page=overview');
}

// kind of a mapping
$page = strtr(
    $page,
    [
        'resources' => 'Buildings',
        'resourceSettings' => 'Resources',
        'station' => 'Buildings',
        'federationlayer' => 'Federation',
        'shortcuts' => 'Fleetshortcuts',
        'forums' => 'Forum',
        'defense' => 'Shipyard',
    ]
);

$file_name = XGP_ROOT . GAME_PATH . ucfirst($page) . 'Controller.php';

if (isset($page)) {
    // logout
    if ($page == 'logout') {
        $system->getSession()->delete();
        Functions::redirect(SYSTEM_ROOT);
    }

    // other pages
    if (file_exists($file_name)) {
        include $file_name;

        $class_name = 'App\Http\Controllers\Game\\' . ucfirst($page) . 'Controller';

        (new $class_name())->index();
    }
}

// any other case
Functions::redirect('game.php?page=overview');
