<?php
/**
 * Home Controller
 *
 * PHP Version 5.5+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace application\controllers\adm;

use application\core\XGPCore;
use application\libraries\adm\AdministrationLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Home Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Home extends XGPCore
{
    private $_lang;
    private $_current_user;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        $this->_lang = parent::$lang;
        $this->_current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (!AdministrationLib::haveAccess($this->_current_user['user_authlevel'])) {
            die(AdministrationLib::noAccessMessage($this->_lang['ge_no_permissions']));
        } else {
            $this->build_page();
        }
    }

    /**
     * method __destruct
     * param
     * return close db connection
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $parse = $this->_lang;
        $error = 0;
        $message[1] = '';
        $message[2] = '';
        $message[3] = '';
        $message[4] = '';

        // VERIFICATIONS
        if ($this->_current_user['user_authlevel'] >= 3) {
            if (is_writable(XGP_ROOT . CONFIGS_PATH . 'config.php')) {
                $message[1] = $this->_lang['hm_config_file_writable'] . '<br />';
                $error++;
            }

            if (( @filesize(XGP_ROOT . LOGS_PATH . 'ErrorLog.php') ) != 0) {
                $message[2] = $this->_lang['hm_database_errors'] . '<br />';
                $error++;
            }

            if ($this->check_updates()) {
                $message[3] = $this->_lang['hm_old_version'] . '<br />';
                $error++;
            }

            if (AdministrationLib::installDirExists()) {
                $message[4] = $this->_lang['hm_install_file_detected'] . '<br />';
                $error++;
            }
        }

        if ($error > 1) {
            $parse['error_message'] = '<br />' . $message[1] . $message[2] . $message[3] . $message[4];
            $parse['second_style'] = "alert-error";
            $parse['error_type'] = $this->_lang['hm_errors'];
        } elseif ($error == 1) {
            $parse['error_message'] = '<br />' . $message[1] . $message[2] . $message[3] . $message[4];
            $parse['second_style'] = "alert-block";
            $parse['error_type'] = $this->_lang['hm_warning'];
        } else {
            $parse['error_message'] = $this->_lang['hm_all_ok'];
            $parse['second_style'] = "alert-success";
            $parse['error_type'] = $this->_lang['hm_ok'];
        }

        $parse['server_type']               = PHP_OS;
        $parse['web_server']                = $this->getWebServer();
        $parse['php_version']               = PHP_VERSION;
        $parse['php_max_post_size']         = FormatLib::prettyBytes((int)(str_replace('M', '', ini_get('post_max_size')) * 1024 * 1024));
        $parse['php_upload_max_filesize']   = FormatLib::prettyBytes((int)(str_replace('M', '', ini_get('upload_max_filesize')) * 1024 * 1024));
        $parse['php_memory_limit']          = FormatLib::prettyBytes((int)(str_replace('M', '', ini_get('memory_limit')) * 1024 * 1024));
        $parse['mysql_version']             = parent::$db->serverInfo();
        $parse['mysql_packet_size']         = FormatLib::prettyBytes(parent::$db->queryFetch("SHOW VARIABLES LIKE 'max_allowed_packet'")[1]);
        
        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('adm/home_view'), $parse));
    }

    /**
     * method check_updates
     * param
     * return check for updates and returns true or false
     */
    private function check_updates()
    {
        if (function_exists('file_get_contents')) {
            $last_v     = json_decode(@file_get_contents('http://xgproyect.org/current.php'))->version;
            $system_v   = FunctionsLib::readConfig('version');
            
            return version_compare($system_v, $last_v, '<');
        }
    }
    
    /**
     * getWebServer
     * 
     * @return string
     */
    private function getWebServer()
    {
        $sapi_name  = php_sapi_name();
        $addsapi    = false;
        
        if (preg_match('#(Apache)/([0-9\.]+)\s#siU', $_SERVER['SERVER_SOFTWARE'], $wsregs))
        {
            $webserver  = "$wsregs[1] v$wsregs[2]";
            if ($sapi_name == 'cgi' or $sapi_name == 'cgi-fcgi')
            {
                $addsapi    = true;
            }
        }
        else if (preg_match('#Microsoft-IIS/([0-9\.]+)#siU', $_SERVER['SERVER_SOFTWARE'], $wsregs))
        {
            $webserver  = "IIS v$wsregs[1]";
            $addsapi    = true;
        }
        else if (preg_match('#Zeus/([0-9\.]+)#siU', $_SERVER['SERVER_SOFTWARE'], $wsregs))
        {
            $webserver  = "Zeus v$wsregs[1]";
            $addsapi    = true;
        }
        else if (strtoupper($_SERVER['SERVER_SOFTWARE']) == 'APACHE')
        {
            $webserver  = 'Apache';
            if ($sapi_name == 'cgi' or $sapi_name == 'cgi-fcgi')
            {
                $addsapi = true;
            }
        }
        else
        {
            $webserver  = $sapi_name;
        }

        if ($addsapi)
        {
            $webserver  .= ' (' . $sapi_name . ')';
        }
        
        return $webserver;
    }
}

/* end of home.php */
