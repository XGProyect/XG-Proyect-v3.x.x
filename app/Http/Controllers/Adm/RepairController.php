<?php

namespace App\Http\Controllers\Adm;

use App\Core\BaseController;
use App\Libraries\Adm\AdministrationLib as Administration;
use App\Libraries\FormatLib;
use App\Libraries\Functions;
use App\Models\Adm\Repair;

class RepairController extends BaseController
{
    private Repair $repairModel;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/repair']);

        $this->repairModel = new Repair();
    }

    public function index(): void
    {
        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

        // build the page
        $this->buildPage();
    }

    private function buildPage(): void
    {
        $parse = $this->langs->language;
        $parse['alert'] = '';

        if (!$_POST) {
            $tables = $this->repairModel->getAllTables();

            $parse['display'] = 'block';
            $parse['head'] = $this->template->set('adm/repair_row_head_view', $this->langs->language);
            $parse['tables'] = '';
            $parse['results'] = '';

            foreach ($tables as $row) {
                $row['row'] = $row['table_name'];
                $row['data'] = FormatLib::prettyBytes($row['data_length']);
                $row['index'] = FormatLib::prettyBytes($row['index_length']);
                $row['overhead'] = FormatLib::prettyBytes($row['data_free']);
                $row['status_style'] = 'text-info';

                $parse['tables'] .= $this->template->set(
                    'adm/repair_row_view',
                    array_merge(
                        $row,
                        $this->langs->language
                    )
                );
            }
        } else {
            $parse['display'] = 'none';
            $parse['head'] = $this->template->set('adm/repair_result_head_view', $this->langs->language);
            $parse['tables'] = '';

            if (isset($_POST['table']) && is_array($_POST['table'])) {
                $result_rows = '';

                foreach ($_POST['table'] as $key => $table) {
                    $parse['row'] = $table;

                    $this->repairModel->checkTable($table);
                    $parse['result'] = $this->langs->line('db_check_ok');
                    $result_rows .= $this->template->set('adm/repair_result_view', $parse);

                    if (isset($_POST['Optimize']) && $_POST['Optimize'] == 'yes') {
                        $this->repairModel->optimizeTable($table);
                        $parse['result'] = $this->langs->line('db_opt');
                        $result_rows .= $this->template->set('adm/repair_result_view', $parse);
                    }

                    if (isset($_POST['Repair']) && $_POST['Repair'] == 'yes') {
                        $this->repairModel->repairTable($table);
                        $parse['result'] = $this->langs->line('db_rep');
                        $result_rows .= $this->template->set('adm/repair_result_view', $parse);
                    }
                }

                $parse['results'] = $result_rows;
            } else {
                Functions::redirect('admin.php?page=repair');
            }
        }

        $this->page->displayAdmin(
            $this->template->set(
                'adm/repair_view',
                $parse
            )
        );
    }
}
