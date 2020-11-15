<?php
/**
 * Ajax File
 *
 * @category Root File
 * @package  N/A
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */

use App\core\Common;

define('IN_LOGIN', true);
define('XGP_ROOT', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);

require XGP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'common.php';

$system = new Common;
$system->bootUp('home');

if (isset($_GET['content'])) {
    $file_name = XGP_ROOT . AJAX_PATH . $_GET['content'] . '.php';

    if (file_exists($file_name)) {
        include $file_name;

        $class_name = 'App\controllers\ajax\\' . ucfirst($_GET['content']);

        (new $class_name())->index();
    }
}
