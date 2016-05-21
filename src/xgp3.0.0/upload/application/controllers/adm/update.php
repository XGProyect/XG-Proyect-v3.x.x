<?php
/**
 * Update Controller
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
use application\libraries\FunctionsLib;

/**
 * Update Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Update extends XGPCore
{
    private $langs;
    private $current_user;
    private $system_version;
    private $db_version;
    private $demo;
    private $output = [];

    /**
     * __construct()
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        $this->langs        = parent::$lang;
        $this->current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::haveAccess(
            $this->current_user['user_authlevel']
        ) && $this->current_user['user_authlevel'] == 3) {
            
            $this->buildPage();
        } else {

            die(AdministrationLib::noAccessMessage($this->langs['ge_no_permissions']));
        }
    }

    /**
     * __destruct
     * 
     * @return void
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * build_page
     *
     * @return void
     */
    private function buildPage()
    {
        $parse      = $this->langs;
        $continue   = true;
        
        $this->system_version   = SYSTEM_VERSION;
        $this->db_version       = FunctionsLib::readConfig('version');

        if ($this->system_version == $this->db_version) {
            die(AdministrationLib::noAccessMessage($this->langs['up_no_update_required']));
        }
        
        if ($_POST && isset($_POST['send'])) {

            $this->demo             = (isset($_POST['demo_mode']) && $_POST['demo_mode'] == 'on') ? true : false;
            
            if (!$this->checkVersion()) {

                $alerts     = $this->langs['up_no_version_file'];
                $continue   = false;
            }
            
            if ($continue) {

                $this->startUpdate();
                
                $parse['alert'] = AdministrationLib::saveMessage('ok', $this->langs['up_success']);
                
                if ($this->demo) {
                    
                    $parse['result']    = print_r($this->output, true);
                    
                    parent::$page->display(    
                        parent::$page->parseTemplate(
                            parent::$page->getTemplate('adm/update_result_view'),
                            $parse
                        )
                    );
                } else {

                    die(AdministrationLib::noAccessMessage($this->langs['up_success']));
                }
            } else {
                $parse['alert']     = AdministrationLib::saveMessage('warning', $alerts);
            }
        }
        
        $parse['up_sub_title']  = sprintf($this->langs['up_sub_title'], $this->db_version, $this->system_version);
        
        parent::$page->display(
            parent::$page->parseTemplate(parent::$page->getTemplate('adm/update_view'), $parse)
        );
    }
    
    /**
     * checkVersion
     * 
     * @return boolean
     */
    private function checkVersion()
    {   
        return file_exists(
            XGP_ROOT . 'install/update/update_common.php'
        );
    }
    
    /**
     * startUpdate
     * 
     * @return void
     */
    private function startUpdate()
    {
        $updates_dir    = opendir(XGP_ROOT . 'install/update/');
        $exceptions     = ['.', '..', '.htaccess', 'index.html', '.DS_Store', 'update_common.php'];
        $files_to_read  = [];
        $db_version     = strtr($this->db_version, ['v' => '', '.' => '']);

        while (($update_dir = readdir($updates_dir)) !== false) {

            if (!in_array($update_dir, $exceptions)) {

                $file_version = strtr(
                    $update_dir,
                    ['update_' => '', '.php' => '']
                );

                // ignore previous versions, we only want the newer ones
                if ($db_version >= $file_version) {

                    continue;
                }
                
                array_push($files_to_read, $file_version);
            }
        }
        
        // sort very important to keep versions order
        asort($files_to_read);
        
        // add common
        array_push($files_to_read, 'common');
        
        // Do we have something? Go...
        if (count($files_to_read) > 0) {

            foreach ($files_to_read as $version) {

                $this->executeFile($version);
            }
        }
    }
    
    /**
     * executeFile
     * 
     * @param string $version Version number
     * 
     * @return void
     */
    private function executeFile($version)
    {
        // Define some stuff
        $update_path    = XGP_ROOT . 'install/update/update_' . $version . '.php';
        $queries        = [];
        
        require_once $update_path;
        
        // Check if there was something
        if (isset($queries) && count($queries) > 0) {

            foreach ($queries as $query) {
                
                if (!$this->demo) {

                    $this->output[] = parent::$db->query($query);
                } else {
                    
                    $this->output[] = $query;
                }
            }
        }
    }
}

/* end of update.php */
