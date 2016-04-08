<?php
/**
 * Repair Controller
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
 * Repair Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Repair extends XGPCore
{
    private $langs;
    private $current_user;

    /**
     * __construct
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
        if (AdministrationLib::haveAccess($this->current_user['user_authlevel'])
            && AdministrationLib::authorization($this->current_user['user_authlevel'], 'config_game') == 1) {
            $this->buildPage($this->current_user);
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
     * buildPage
     *
     * @return void
     */
    private function buildPage()
    {
        $parse          = $this->langs;
        $template       = parent::$page->getTemplate('adm/repair_row_view');
        $template_head  = parent::$page->getTemplate('adm/repair_row_head_view');
        $result_tpl     = parent::$page->getTemplate('adm/repair_result_view');
        $result_tpl_head= parent::$page->getTemplate('adm/repair_result_head_view');
        $parse['alert'] = '';

        if (!$_POST) {

            $tables     = parent::$db->query(
                "SELECT 
                    `table_name`,
                    `data_length`,
                    `index_length`,
                    `data_free`
                FROM information_schema.TABLES 
                WHERE table_schema = '" . DB_NAME . "';"
            );
            
            $parse['display']   = 'block';
            $parse['head']      = parent::$page->parseTemplate($template_head, $this->langs);
            $parse['tables']    = '';

            while ($row = parent::$db->fetchArray($tables)) {

                $row['row']             = $row['table_name'];
                $row['data']            = FormatLib::prettyBytes($row['data_length']);
                $row['index']           = $row['index_length'];
                $row['overhead']        = $row['data_free'];
                $row['status_style']    = 'text-info';

                $parse['tables']         .= parent::$page->parseTemplate(
                    $template,
                    array_merge($row, $this->langs)
                );
            }
        } else {

            $parse['display']   = 'none';
            $parse['head']      = parent::$page->parseTemplate($result_tpl_head, $this->langs);
            
            if (isset($_POST['table']) && is_array($_POST['table'])) {

                $result_rows    = '';
                
                foreach ($_POST['table'] as $key => $table) {
                    
                    $parse['row']   = $table;

                    parent::$db->query("CHECK TABLE " . $table);
                    $parse['result']    = $this->langs['db_check_ok'];
                    $result_rows        .= parent::$page->parseTemplate($result_tpl, $parse);
                    
                    if (isset($_POST['Optimize']) && $_POST['Optimize'] == 'yes') {
                        parent::$db->query("OPTIMIZE TABLE " . $table);
                        $parse['result']    = $this->langs['db_opt'];
                        $result_rows        .= parent::$page->parseTemplate($result_tpl, $parse);
                    }

                    if (isset($_POST['Repair']) && $_POST['Repair'] == 'yes') {
                        parent::$db->query("REPAIR TABLE " . $table);
                        $parse['result']    = $this->langs['db_rep'];
                        $result_rows        .= parent::$page->parseTemplate($result_tpl, $parse);
                    }
                }
                
                $parse['results']   = $result_rows;
            } else {
                FunctionsLib::redirect('admin.php?page=repair');
            }
        }

        parent::$page->display(
            parent::$page->parseTemplate(
                parent::$page->getTemplate('adm/repair_view'),
                $parse
            )
        );
    }
}

/* end of repair.php */
