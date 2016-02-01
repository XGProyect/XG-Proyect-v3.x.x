<?php
/**
 * Installation Controller
 *
 * PHP Version 5.5+
 *
 * @category Controllers
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace application\controllers\install;

use application\core\XGPCore;
use application\libraries\FunctionsLib;

/**
 * Installation Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Installation extends XGPCore
{
    private $host;
    private $name;
    private $user;
    private $password;
    private $prefix;
    private $langs;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        $this->langs    = parent::$lang;
        $this->_planet  = FunctionsLib::load_library('PlanetLib');

        if ($this->serverRequirementes()) {
            
            $this->buildPage();
        } else {
            die(FunctionsLib::message($this->langs['ins_no_server_requirements']));
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
     * method buildPage
     * param
     * return main method, loads everything
     */
    private function buildPage()
    {
        $parse      = $this->langs;
        $continue   = true;

        // VERIFICATION - WE DON'T WANT ANOTHER INSTALLATION
        if ($this->isInstalled()) {
            die(FunctionsLib::message($this->langs['ins_already_installed'], '', '', false, false));
        }

        if (!$this->checkXmlFile()) {
            die(FunctionsLib::message($this->langs['ins_missing_xml_file'], '', '', false, false));
        }

        // ACTION FOR THE CURRENT PAGE
        switch ((isset($_POST['page']) ? $_POST['page'] : '')) {
            case 'step1':
                $this->host     = $_POST['host'];
                $this->name     = $_POST['db'];
                $this->user     = $_POST['user'];
                $this->password = $_POST['password'];
                $this->prefix   = $_POST['prefix'];

                if (!$this->validateDbData()) {
                    $alerts     = $this->langs['ins_empty_fields_error'];
                    $continue   = false;
                }

                if (!$this->writeConfigFile()) {
                    $alerts     = $this->langs['ins_write_config_error'];
                    $continue   = false;
                }

                if ($continue) {
                    FunctionsLib::redirect('?page=install&mode=step2');
                }

                $parse['alert'] = $this->saveMessage($alerts, 'warning');
                
                $current_page   = parent::$page->parse_template(
                    parent::$page->get_template('install/in_database_view'),
                    $parse
                );
                
                break;

            case 'step2':
                if (!$this->tryConnection()) {
                    $alerts     = $this->langs['ins_not_connected_error'];
                    $continue   = false;
                }

                if ($continue) {
                    FunctionsLib::redirect('?page=install&mode=step3');
                }

                $parse['alert'] = $this->saveMessage($alerts, 'warning');
                $current_page   = parent::$page->parse_template(
                    parent::$page->get_template('install/in_database_view'),
                    $parse
                );

                break;

            case 'step3':
                if (!$this->insertDbData()) {
                    $alerts     = $this->langs['ins_insert_tables_error'];
                    $continue   = false;
                }

                if ($continue) {
                    
                    FunctionsLib::redirect('?page=install&mode=step4');
                }

                $parse['alert'] = $this->saveMessage($alerts, 'warning');
                $current_page   = parent::$page->parse_template(
                    parent::$page->get_template('install/in_database_view'),
                    $parse
                );
                break;

            case 'step4':
                FunctionsLib::redirect('?page=install&mode=step5');
                break;

            case 'step5':
                $create_account_status = $this->createAccount();
                if ($create_account_status < 0) {
                    // Failure
                    $error_message = $create_account_status == -1 ? $this->langs['ins_adm_empty_fields_error'] : $this->langs['ins_adm_invalid_email_address'];
                    $parse['alert'] = $this->saveMessage($error_message, 'warning');
                    $current_page   = parent::$page->parse_template(
                        parent::$page->get_template('install/in_create_admin_view'),
                        $parse
                    );
                    $continue       = false;
                }

                if ($continue) {
                    FunctionsLib::update_config('stat_last_update', time());
                    FunctionsLib::update_config('game_installed', '1');

                    $current_page   = parent::$page->parse_template(
                        parent::$page->get_template('install/in_create_admin_done_view'),
                        $this->langs
                    );
                    
                    // This will continue on false meaning "This is the end of the installation, no else where to go"
                    $continue       = false;
                }
                break;

            case '':
            default:
                break;
        }

        if ($continue) {
            switch ((isset($_GET['mode']) ? $_GET['mode'] : '')) {
                case 'step1':
                    $current_page   = parent::$page->parse_template(
                        parent::$page->get_template('install/in_database_view'),
                        $this->langs
                    );

                    break;

                case 'step2':
                    $parse['step']              = 'step2';
                    $parse['done_config']       = $this->langs['ins_done_config'];
                    $parse['done_connected']    = '';
                    $parse['done_insert']       = '';
                    $current_page               = parent::$page->parse_template(
                        parent::$page->get_template('install/in_done_actions_view'),
                        $parse
                    );

                    break;

                case 'step3':
                    $parse['step']              = 'step3';
                    $parse['done_config']       = '';
                    $parse['done_connected']    = $this->langs['ins_done_connected'];
                    $parse['done_insert']       = '';
                    $current_page               = parent::$page->parse_template(
                        parent::$page->get_template('install/in_done_actions_view'),
                        $parse
                    );

                    break;

                case 'step4':
                    $parse['step']              = 'step4';
                    $parse['done_config']       = '';
                    $parse['done_connected']    = '';
                    $parse['done_insert']       = $this->langs['ins_done_insert'];
                    $current_page               = parent::$page->parse_template(
                        parent::$page->get_template('install/in_done_actions_view'),
                        $parse
                    );

                    break;

                case 'step5':
                    $parse['step']  = 'step5';
                    $current_page   = parent::$page->parse_template(
                        parent::$page->get_template('install/in_create_admin_view'),
                        $parse
                    );

                    break;

                case 'license':
                    $current_page   = parent::$page->parse_template(
                        parent::$page->get_template('install/in_license_view'),
                        $this->langs
                    );

                    break;

                case '':
                case 'overview':
                default:
                    $current_page   = parent::$page->parse_template(
                        parent::$page->get_template('install/in_welcome_view'),
                        $this->langs
                    );

                    break;
            }
        }

        parent::$page->display($current_page);
    }

    /**
     * method server_requirementes
     * param
     * return true if the required server requirements are met
     */
    private function serverRequirementes()
    {
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            
            return false;
        } else {
            
            return true;
        }
    }

    /**
     * method isInstalled
     * param
     * return true if the game is already installed, false if not
     */
    private function isInstalled()
    {
        return (FunctionsLib::read_config('game_installed') == 1);
    }

    /**
     * method tryConnection
     * param
     * return true if the required server requirements are met
     */
    private function tryConnection()
    {
        // Try & check
        if (parent::$db->tryConnection() && parent::$db->tryDatabase()) {
            
            return true;
        } else {
            
            return false;
        }
    }

    /**
     * method writeConfigFile
     * param
     * return write configuration file
     */
    private function writeConfigFile()
    {
        $config_file    = @fopen(XGP_ROOT . CONFIGS_PATH . 'config.php', "w");

        if (!$config_file) {
            
                return false;
        }

        $data   = "<?php\n";
        $data   .= "defined('DB_HOST') ? NULL : define('DB_HOST', '".$this->host."');\n";
        $data   .= "defined('DB_USER') ? NULL : define('DB_USER', '".$this->user."');\n";
        $data   .= "defined('DB_PASS') ? NULL : define('DB_PASS', '".$this->password."');\n";
        $data   .= "defined('DB_NAME') ? NULL : define('DB_NAME', '".$this->name."');\n";
        $data   .= "defined('DB_PREFIX') ? NULL : define('DB_PREFIX', '".$this->prefix."');\n";
        $data   .= "defined('SECRETWORD') ? NULL : define('SECRETWORD', 'xgp-".$this->generateToken()."');\n";
        $data   .= "?>";

        fwrite($config_file, $data);
        fclose($config_file);

        return true;
    }

    /**
     * method insertDbData
     * param
     * return TRUE successfully inserted data | FALSE an error ocurred
     */
    private function insertDbData()
    {
        // get the database structure
        require_once XGP_ROOT . 'install/databaseinfos.php';

        foreach ($tables as $table => $query) {
            
            // run query for each table
            $status[$table] = parent::$db->query($query);

            // if something fails... return false
            if ($status[$table] != 1) {
                return false;
            }
        }

        // ok!
        return true;
    }

    /**
     * @method createAccount
     * @return negative value if an error ocurred, or 0 if admin account was successfully created
     *          -1: Some field is empty
     *          -2: Admin email is invalid
     */
    private function createAccount()
    {
        // validations
        if (empty($_POST['adm_user']) || empty($_POST['adm_pass']) || empty($_POST['adm_email'])) {
            return -1;
        }
            
        if(!Functions_Lib::valid_email($_POST['adm_email'])) {
            return -2;
        }

        // some default values
        $adm_name   = parent::$db->escapeValue($_POST['adm_user']);
        $adm_email  = parent::$db->escapeValue($_POST['adm_email']);
        $adm_pass   = sha1($_POST['adm_pass']);

        // a bunch of of queries :/
        parent::$db->query("INSERT INTO " . USERS . " SET
            `user_id` = '1',
            `user_name` = '". $adm_name ."',
            `user_email` = '". $adm_email ."',
            `user_email_permanent` = '". $adm_email ."',
            `user_ip_at_reg` = '". $_SERVER['REMOTE_ADDR'] . "',
            `user_agent` = '',
            `user_authlevel` = '3',
            `user_home_planet_id` = '1',
            `user_galaxy` = '1',
            `user_system` = '1',
            `user_planet` = '1',
            `user_current_planet` = '1',
            `user_register_time` = '". time() ."',
            `user_password` = '". $adm_pass ."';");

        $this->_planet->createPlanetWithOptions(
            array(
                'planet_user_id' => 1,
                'planet_name' => $adm_name,
                'planet_galaxy' => 1,
                'planet_system' => 1,
                'planet_planet' => 1,
                'planet_last_update' => time(),
                'planet_metal' => 500,
                'planet_crystal' => 500,
                'planet_deuterium' => 0
            )
        );

        parent::$db->query("INSERT INTO " . RESEARCH . " SET `research_user_id` = '1';");
        parent::$db->query("INSERT INTO " . USERS_STATISTICS . " SET `user_statistic_user_id` = '1';");
        parent::$db->query("INSERT INTO " . PREMIUM . " SET `premium_user_id` = '1';");
        parent::$db->query("INSERT INTO " . SETTINGS . " SET `setting_user_id` = '1';");
        parent::$db->query("INSERT INTO " . BUILDINGS . " SET `building_planet_id` = '1';");
        parent::$db->query("INSERT INTO " . DEFENSES . " SET `defense_planet_id` = '1';");
        parent::$db->query("INSERT INTO " . SHIPS . " SET `ship_planet_id` = '1';");

        // write the new admin email for support and debugging
        FunctionsLib::update_config('admin_email', $adm_email);

        return true;
    }

    /**
     * method validateDbData
     * param
     * return check inserted data, try connection and return the result
     */
    private function validateDbData()
    {
        return !empty($this->host) && !empty($this->name) &&
                !empty($this->user) && !empty($this->prefix);
        }

    /**
     * method checkXmlFile
     * param
     * return true if file was found, else if not
     */
    private function checkXmlFile()
    {
        $needed_config_file     = @fopen(XGP_ROOT . CONFIGS_PATH . 'config.xml', "r");
        $default_config_file    = @fopen(XGP_ROOT . CONFIGS_PATH . 'config.xml.cfg', "r");

        if (!$needed_config_file) {
            
            if (!$default_config_file) {
                
                return false;
            } else {
                
                // Will return true if the file was successfully created
                return $this->createXml();
            }
        }

        return true;
    }

    /**
     * method createXml
     * param
     * return true if file was succesfully created
     */
    private function createXml()
    {
        $location               = XGP_ROOT . CONFIGS_PATH;
        $default_config_file    = $location . 'config.xml.cfg';
        $needed_config_file     = $location . 'config.xml';

        @chmod($location, 0777);
        
        if (@copy($default_config_file, $needed_config_file)) {

            @chmod($needed_config_file, 0777);
            
            return true;
        }
        
        return false;
    }

    /**
     * method generateToken
     * param
     * return the security token generated
     */
    private function generateToken()
    {
        $characters = 'aazertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890';
        $count      = strlen($characters);
        $new_token  = '';
        $lenght     = 16;
        srand((double)microtime() * 1000000);

        for ($i = 0; $i < $lenght; $i++) {
            $character_boucle   = mt_rand(0, $count - 1);
            $new_token          = $new_token . substr($characters, $character_boucle, 1);
        }

        return $new_token;
    }

    /**
     * method saveMessage
     * param $result
     * return show the save message
     */
    private function saveMessage($message, $result = 'ok')
    {
        switch ($result) {
            case 'ok':
                $parse['color']     = 'alert-success';
                $parse['status']    = $this->langs['ins_ok_title'];
                break;

            case 'error':
                $parse['color']     = 'alert-error';
                $parse['status']    = $this->langs['ins_error_title'];
                break;

            case 'warning':
                $parse['color']     = 'alert-block';
                $parse['status']    = $this->langs['ins_warning_title'];
                break;
        }

        $parse['message']   = $message;

        return parent::$page->parse_template(
            parent::$page->get_template('adm/save_message_view'),
            $parse
        );
    }
}

/* end of installation.php */
