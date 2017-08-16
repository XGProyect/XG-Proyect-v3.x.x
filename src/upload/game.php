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
 * @version  3.0.0
 */

use application\libraries\FunctionsLib;

define('IN_GAME', true);
define('XGP_ROOT', './');

require XGP_ROOT . 'application/core/common.php';

$hooks->call_hook('before_page');

$page   = isset($_GET['page']) ? $_GET['page'] : FunctionsLib::redirect('game.php?page=overview');

// some replacements to adapt the pages
$page   = strtr(
    $page,
    array(
        'resources'         => 'buildings',
        'resourceSettings'  => 'resources',
        'station'           => 'buildings',
        'federationlayer'   => 'federation',
        'shortcuts'         => 'fleetshortcuts',
        'forums'            => 'forum'
    )
);

$file_name  = XGP_ROOT . GAME_PATH . $page . '.php';

if (isset($page)) {
    
    // logout
    if ($page == 'logout') {
        $session->delete();
        FunctionsLib::redirect(XGP_ROOT);
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
