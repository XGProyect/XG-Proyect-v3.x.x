<?php
/**
 * Game File
 *
 * PHP Version 5.5+
 *
 * @category Root File
 * @package  N/A
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
use application\libraries\FunctionsLib;

define('IN_GAME', true);
define('XGP_ROOT', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);

require XGP_ROOT . 'application' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'common.php';

$hooks->call_hook('before_page');

$page = filter_input(INPUT_GET, 'page');

if (is_null($page)) {
    
    FunctionsLib::redirect('game.php?page=overview');
}

// kind of a mapping
$page = strtr(
    $page, [
        'resources' => 'buildings',
        'resourceSettings' => 'resources',
        'station' => 'buildings',
        'federationlayer' => 'federation',
        'shortcuts' => 'fleetshortcuts',
        'forums' => 'forum',
        'defense' => 'shipyard'
    ]
);

$file_name = XGP_ROOT . GAME_PATH . $page . '.php';

if (isset($page)) {

    // logout
    if ($page == 'logout') {
        $session->delete();
        FunctionsLib::redirect(SYSTEM_ROOT);
    }

    // other pages
    if (file_exists($file_name)) {

        include $file_name;

        $class_name = 'application\controllers\game\\' . ucfirst($page);

        new $class_name();
    }
}

// call hooks
if (!$hooks->call_hook('new_page')) {
    FunctionsLib::redirect('game.php?page=overview');
}

// any other case
FunctionsLib::redirect('game.php?page=overview');

/* end of game.php */
