<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2016
 */

define('IN_GAME', true);
define('XGP_ROOT', './');

require XGP_ROOT . 'application/core/common.php';

$hooks->call_hook('before_page');

$page   = isset($_GET['page']) ? $_GET['page'] : null;

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
        Functions_Lib::redirect(XGP_ROOT);
    }
    
    if (file_exists($file_name)) {

        include $file_name;

        $class_name = ucfirst($page);

        new $class_name();
    } 
}

if ($page == null) {
    
    if (!$hooks->call_hook('new_page')) {
        Functions_Lib::redirect('game.php?page=overview');
    }
    
    Functions_Lib::redirect('game.php?page=overview');
}

/* end of game.php */
