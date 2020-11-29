<?php
/**
 * Repair Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\controllers\adm;

use App\core\BaseController;
use App\libraries\adm\AdministrationLib as Administration;
use App\libraries\FormatLib;
use App\libraries\Functions;

/**
 * Repair Class
 */
class Repair extends BaseController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Model
        parent::loadModel('adm/repair');

        // load Language
        parent::loadLang(['adm/global', 'adm/repair']);
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

        // build the page
        $this->buildPage();
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        $parse = $this->langs->language;
        $parse['alert'] = '';

        if (!$_POST) {
            $tables = $this->Repair_Model->getAllTables();

            $parse['display'] = 'block';
            $parse['head'] = $this->getTemplate()->set('adm/repair_row_head_view', $this->langs->language);
            $parse['tables'] = '';
            $parse['np_general'] = '';
            $parse['results'] = '';

            foreach ($tables as $row) {
                $row['row'] = $row['table_name'];
                $row['data'] = FormatLib::prettyBytes($row['data_length']);
                $row['index'] = FormatLib::prettyBytes($row['index_length']);
                $row['overhead'] = FormatLib::prettyBytes($row['data_free']);
                $row['status_style'] = 'text-info';

                $parse['tables'] .= $this->getTemplate()->set(
                    'adm/repair_row_view',
                    array_merge(
                        $row,
                        $this->langs->language
                    )
                );
            }
        } else {
            $parse['display'] = 'none';
            $parse['head'] = $this->getTemplate()->set('adm/repair_result_head_view', $this->langs->language);
            $parse['tables'] = '';
            $parse['np_general'] = '';

            if (isset($_POST['table']) && is_array($_POST['table'])) {
                $result_rows = '';

                foreach ($_POST['table'] as $key => $table) {
                    $parse['row'] = $table;

                    $this->Repair_Model->checkTable($table);
                    $parse['result'] = $this->langs->line('db_check_ok');
                    $result_rows .= $this->getTemplate()->set('adm/repair_result_view', $parse);

                    if (isset($_POST['Optimize']) && $_POST['Optimize'] == 'yes') {
                        $this->Repair_Model->optimizeTable($table);
                        $parse['result'] = $this->langs->line('db_opt');
                        $result_rows .= $this->getTemplate()->set('adm/repair_result_view', $parse);
                    }

                    if (isset($_POST['Repair']) && $_POST['Repair'] == 'yes') {
                        $this->Repair_Model->repairTable($table);
                        $parse['result'] = $this->langs->line('db_rep');
                        $result_rows .= $this->getTemplate()->set('adm/repair_result_view', $parse);
                    }
                }

                $parse['results'] = $result_rows;
            } else {
                Functions::redirect('admin.php?page=repair');
            }
        }

        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/repair_view',
                $parse
            )
        );
    }
}
