<?php
/**
 * Ajax File
 *
 * PHP Version 7.1+
 *
 * @category Root File
 * @package  N/A
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */

define('IN_LOGIN', true);
define('XGP_ROOT', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);

include XGP_ROOT . 'application/core/common.php';

if (isset($_GET['content'])) {
    $file_name = XGP_ROOT . AJAX_PATH . $_GET['content'] . '.php';

    if (file_exists($file_name)) {
        include $file_name;

        $class_name = 'application\controllers\ajax\\' . ucfirst($_GET['content']);

        new $class_name();
    }
}

/* end of ajax.php */
