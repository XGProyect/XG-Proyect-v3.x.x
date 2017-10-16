<?php
/**
 * Errors Controller
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

use application\core\Controller;
use application\libraries\adm\AdministrationLib;
use application\libraries\FunctionsLib;

/**
 * Errors Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Errors extends Controller
{

    /**
     *
     * @var array Language data
     */
    private $_lang;

    /**
     *
     * @var array User data 
     */
    private $_user;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        $this->_lang = $this->getLang();
        $this->_user = $this->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::haveAccess($this->_user['user_authlevel']) && AdministrationLib::authorization($this->_user['user_authlevel'], 'config_game') == 1) {

            // time to do something
            $this->runAction();
            
            // build the page
            $this->buildPage();
        } else {

            die(AdministrationLib::noAccessMessage($this->_lang['ge_no_permissions']));
        }
    }

    /**
     * Process deleteall request
     * 
     * @return void
     */
    private function runAction()
    {
        $delete_all = filter_input(INPUT_GET, 'deleteall', FILTER_DEFAULT);
        
        if ($delete_all == 'yes') {
            
            $files  = $this->getListOfLogFiles();
            
            if ($files != '') {

                foreach($files as $file_name) {
                    
                    unlink($file_name);
                }
            }
            
            FunctionsLib::redirect('admin.php?page=errors');
        }
    }
    
    /**
     * Build the page
     * 
     * @return void
     */
    private function buildPage()
    {
        $parse                  = $this->_lang;
        $list_of_errors         = $this->processErrorsLogs();
        
        $parse['alert']             = '';
        $parse['errors_list']       = $list_of_errors;
        $parse['errors_list_resume']= count($list_of_errors) . $this->_lang['er_errors'];

        parent::$page->display(
            $this->getTemplate()->set('adm/errors_view', $parse)
        );
    }
    
    /**
     * Parse the recovered log files
     * 
     * @return array
     */
    private function processErrorsLogs()
    {        
        // list of log files
        $files          = $this->getListOfLogFiles();
        $list_of_errors = [];
        
        if ($files != '') {
            
            foreach($files as $file_name) {
                
                $contents = file_get_contents($file_name);

                if ($contents) {

                    $error_columns  = explode('|', $contents);

                    $list_of_errors[] = [
                        'user_ip' => $error_columns[1],
                        'error_type' => $error_columns[2],
                        'error_code' => $error_columns[3],
                        'error_message' => $error_columns[4],
                        'error_trace' => $error_columns[5],
                        'error_datetime' => $error_columns[6]
                    ];   
                }
            }
        }
        
        return $list_of_errors;
    }
    
    /**
     * Get a list of the log files
     * 
     * @return array
     */
    private function getListOfLogFiles()
    {
        $logs_path  = XGP_ROOT . LOGS_PATH;
        
        // list of log files
        return glob($logs_path . '*.txt');
    }
}

/* end of errors.php */
