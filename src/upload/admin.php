<?php
/**
 * Admin File
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

use application\libraries\adm\AdministrationLib;
use application\libraries\FunctionsLib;

define('IN_ADMIN', true);
define('XGP_ROOT', './');

require XGP_ROOT . 'application/core/common.php';

include_once XGP_ROOT . 'application/libraries/adm/AdministrationLib.php';

// check if SSL is setted
AdministrationLib::secureConnection();
AdministrationLib::updateRequired();

$page       = isset($_GET['page']) ? $_GET['page'] : 'home';
$file_name  = XGP_ROOT . ADMIN_PATH . $page . '.php';

// logout
if ($page == 'logout') {

    AdministrationLib::closeSession();
    FunctionsLib::redirect(XGP_ROOT . 'game.php?page=overview');
}

if (file_exists($file_name)) {

    include $file_name;

    
    
    $class_name = 'application\controllers\adm\\' . ucfirst($page);

    new $class_name();
} else {

    FunctionsLib::redirect(XGP_ROOT . 'admin.php');
}

/* end of admin.php */
