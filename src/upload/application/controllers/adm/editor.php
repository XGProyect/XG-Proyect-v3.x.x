<?php
/**
 * Editor Controller
 *
 * PHP Version 7.1+
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

/**
 * Editor Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Editor extends Controller
{

    private $_lang;
    private $_current_file;
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
        if (AdministrationLib::haveAccess($this->_current_user['user_authlevel']) && AdministrationLib::authorization($this->_current_user['user_authlevel'], 'config_game') == 1) {
            $this->build_page();
        } else {
            die(AdministrationLib::noAccessMessage($this->_lang['ge_no_permissions']));
        }
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $parse = $this->_lang;
        $parse['alert'] = '';
        $parse['language_files'] = $this->get_files();

        if ($_POST) {
            if (isset($_POST['file_edit'])) {
                $this->_current_file = $_POST['file_edit'];
            }

            if (isset($_POST['save_file'])) {
                $this->save_contents($_POST['file_content']);

                $parse['alert'] = AdministrationLib::saveMessage('ok', $this->_lang['ce_all_ok_message']);
            }
        }
        
        $parse['language_files'] = $this->get_files();
        $parse['contents'] = empty($this->_current_file) ? '' : $this->get_contents();

        if ($parse['contents'] == '') {
            
            $parse['alert'] = AdministrationLib::saveMessage('error', $this->_lang['ce_all_error_reading']);
        }
        
        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('adm/editor_view'), $parse));
    }

    /**
     * method get_contents
     * param
     * return get file contents
     */
    private function get_contents()
    {
        // GET THE FILE
        $changelog_file = XGP_ROOT . LANG_PATH . DEFAULT_LANG . '/' . $this->_current_file;

        // OPEN THE FILE
        $fs = @fopen($changelog_file, 'a+');
        $contents = '';
        
        if ($fs) {

            // LOOP THRU THE FILE TO GET ITS CONTENT
            while (!feof($fs)) {
                $contents .= fgets($fs, 1024);
            }

            fclose($fs);
        }

        // RETURN CONTENT
        return $contents;
    }

    /**
     * method get_contents
     * param
     * return get file contents
     */
    private function save_contents($file_data)
    {
        // GET THE FILE
        $file = XGP_ROOT . LANG_PATH . DEFAULT_LANG . '/' . $this->_current_file;

        // OPEN THE FILE
        $fs = @fopen($file, 'w');

        if ($fs && $file_data != '') {

            fwrite($fs, $file_data);

            fclose($fs);
        }
    }

    /**
     * method get_files
     * param
     * return the list of language files
     */
    private function get_files()
    {
        $langs_files = opendir(XGP_ROOT . LANG_PATH . DEFAULT_LANG);
        $exceptions = array('.', '..', '.htaccess', 'index.html');
        $lang_options = '';

        while (( $lang_file = readdir($langs_files) ) !== false) {
            if (!in_array($lang_file, $exceptions) && strpos($lang_file, '.', 0) != 0) {
                $lang_options .= '<option ';

                if ($this->_current_file == $lang_file) {
                    $lang_options .= 'selected = selected';
                }

                $lang_options .= ' value="' . $lang_file . '">' . $lang_file . '</option>';
            }
        }

        return $lang_options;
    }
}

/* end of editor.php */
