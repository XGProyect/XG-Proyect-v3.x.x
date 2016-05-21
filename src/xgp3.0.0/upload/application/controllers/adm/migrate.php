<?php
/**
 * Migrate Controller
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

use application\core\Database;
use application\core\XGPCore;
use application\libraries\adm\AdministrationLib;

/**
 * Migrate Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Migrate extends XGPCore
{
    private $langs;
    private $current_user;
    private $dbObject;
    private $host;
    private $user;
    private $password;
    private $name;
    private $prefix;
    private $version;
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
        
        $parse['versions_list'] = $this->getVersionsList();
        
        if ($_POST) {
            
            $this->host     = isset($_POST['host']) ? $_POST['host'] : null;
            $this->user     = isset($_POST['user']) ? $_POST['user'] : null;
            $this->password = isset($_POST['password']) ? $_POST['password'] : null;
            $this->name     = isset($_POST['db']) ? $_POST['db'] : null;
            $this->prefix   = isset($_POST['prefix']) ? $_POST['prefix'] : null;
            $this->version  = $_POST['version_select'];
            $this->demo     = (isset($_POST['demo_mode']) && $_POST['demo_mode'] == 'on') ? true : false;
            
            if (!$this->validateDbData()) {

                $alerts     = $this->langs['mi_empty_fields_error'];
                $continue   = false;
            }
            
            if (!$this->checkVersion() && $continue) {

                $alerts     = $this->langs['mi_no_migration_file'];
                $continue   = false;
            }
            
            // New database object
            $this->dbObject = new Database();
            
            if (!$this->tryConnection() && $continue) {

                $alerts     = $this->langs['mi_not_connected_error'];
                $continue   = false;
            }

            if (!$this->tryDatabase() && $continue) {
                $alerts     = $this->langs['mi_db_not_exists'];
                $continue   = false;
            }
            
            if ($continue) {

                $this->startMigration();
                
                $parse['alert'] = AdministrationLib::saveMessage('ok', $this->langs['mi_success']);
                
                if ($this->demo) {
                    
                    $parse['result']    = print_r($this->output, true);
                    
                    parent::$page->display(    
                        parent::$page->parseTemplate(
                            parent::$page->getTemplate('adm/migrate_result_view'),
                            $parse
                        )
                    );
                }
            } else {

                $parse['alert']     = AdministrationLib::saveMessage('warning', $alerts);
                $parse['v_host']    = $this->host;
                $parse['v_db']      = $this->name;
                $parse['v_user']    = $this->user;
                $parse['v_prefix']  = $this->prefix;
            }
        }
        
        parent::$page->display(
            parent::$page->parseTemplate(parent::$page->getTemplate('adm/migrate_view'), $parse)
        );
    }
    
    /**
     * getVersionsList
     * 
     * @return string
     */
    private function getVersionsList()
    {
        $versions   = [
            '290' => '2.9.0',
            '291' => '2.9.1',
            '292' => '2.9.2',
            '293' => '2.9.3',
            '294' => '2.9.4',
            '295' => '2.9.5',
            '296' => '2.9.6',
            '297' => '2.9.7',
            '298' => '2.9.8',
            '299' => '2.9.9',
            '2910' => '2.9.10',
            '2100' => '2.10.0',
            '2101' => '2.10.1',
            '2102' => '2.10.2',
            '2103' => '2.10.3',
            '2104' => '2.10.4',
            '2105' => '2.10.5',
            '2106' => '2.10.6',
            '2107' => '2.10.7',
            '2108' => '2.10.8',
            '2109' => '2.10.9'
        ];
        
        $version_options    = '';
        
        foreach ($versions as $id => $version) {
            
            if ($id == $this->version) {
                $select = 'selected="selected"';
            } else {
                $select = '';
            }
            
            $version_options    .= '<option value="' . $id . '" ' . $select . '>' . 'v' . $version . '</option>';
        }
        
        return $version_options;
    }
    
    /**
     * validateDbData
     *
     * @return boolean
     */
    private function validateDbData()
    {
        return !empty($this->host) && !empty($this->name) &&
                !empty($this->user) && !empty($this->prefix);
    }
    
    /**
     * checkVersion
     * 
     * @return boolean
     */
    private function checkVersion()
    {   
        return file_exists(XGP_ROOT . 'install/migration/migrate_' . $this->version . '.php');
    }
    
    /**
     * tryConnection
     *
     * @return boolean
     */
    private function tryConnection()
    {
        return $this->dbObject->tryConnection($this->host, $this->user, $this->password);
    }
    
    /**
     * tryDatabase
     *
     * @return boolean
     */
    private function tryDatabase()
    {
        return $this->dbObject->tryDatabase($this->name);
    }
    
    /**
     * startMigration
     * 
     * @return void
     */
    private function startMigration()
    {
        /**
         * 1º Step
         * 
         * Update old DB to latest in its current branch
         */
        $this->firstStep();
        
        /**
         * 2º Step
         * At this point the DB and the configs files are like the last stable 2.10.x.
         * Now we have to start moving the data, the funny part :D
         */
        $this->secondStep();
    }
    
    /**
     * firstStep
     * 
     * @return void
     */
    private function firstStep()
    {
        // Define some stuff
        $migration_path = XGP_ROOT . 'install/migration/migrate_' . $this->version . '.php';
        $queries        = [];
        
        require_once $migration_path;
        
        // Check if there was something
        if (isset($queries) && count($queries) > 0) {
            foreach ($queries as $query) {
                
                // set the prefix
                $query          = strtr($query, ['{prefix}' => $this->prefix]);
                
                if (!$this->demo) {

                    $this->output[] = $this->dbObject->query($query);
                } else {
                    
                    $this->output[] = $query;
                }
            }
        }
    }
    
    /**
     * secondStep
     * 
     * @return void
     */
    private function secondStep()
    {
        // Define some stuff
        $migration_path = XGP_ROOT . 'install/migration/migrate_common.php';
        $queries        = [];
        $password       = $this->current_user['user_password'];
        
        require_once $migration_path;
        
        // Check if there was something/*
        if (isset($queries) && count($queries) > 0) {
            foreach ($queries as $query) {
                
                // set the prefix
                $query          = strtr($query, ['{prefix}' => $this->prefix]);
                
                if (!$this->demo) {

                    $this->output[] = $this->dbObject->query($query);
                } else {
                    
                    $this->output[] = $query;
                }
            }
        }
        
        unset($password);
    }
}

/* end of migrate.php */
