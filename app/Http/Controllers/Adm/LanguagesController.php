<?php

declare(strict_types=1);

namespace App\Http\Controllers\Adm;

use App\Core\BaseController;
use App\Libraries\Adm\AdministrationLib as Administration;

class LanguagesController extends BaseController
{
    private string $alert = '';
    private string $current_file = '';

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/languages']);
    }

    public function index(): void
    {
        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    private function runAction(): void
    {
        $action = filter_input_array(INPUT_POST);

        if ($action) {
            if (isset($action['file'])) {
                $this->doFileAction($action['file']);
            }

            if (isset($action['save']) && $action['save'] != '') {
                $this->doSaveAction($action['save']);
            }
        }
    }

    private function doFileAction(string $file): void
    {
        $this->current_file = $file;
    }

    private function doSaveAction(string $file_data): void
    {
        // get the file
        $file = XGP_ROOT . LANG_PATH . DIRECTORY_SEPARATOR . $this->current_file;

        // open the file
        $fs = @fopen($file, 'w');

        if ($fs && $file_data != '') {
            fwrite($fs, $file_data);

            fclose($fs);
        }

        $this->alert = Administration::saveMessage('ok', $this->langs->line('le_all_ok_message'));
    }

    private function buildPage(): void
    {
        $this->page->displayAdmin(
            $this->template->set(
                'adm/languages_view',
                array_merge(
                    $this->langs->language,
                    $this->getFiles(),
                    $this->getContents(),
                    [
                        'edit_file' => $this->current_file,
                        'alert' => $this->alert ?? '',
                    ]
                )
            )
        );
    }

    private function getContents(): array
    {
        if (empty($this->current_file)) {
            return [
                'contents' => $contents ?? '',
            ];
        }

        $file = XGP_ROOT . LANG_PATH . DIRECTORY_SEPARATOR . $this->current_file;

        // open the file
        $fs = @fopen($file, 'a+');
        $contents = '';

        if ($fs) {
            while (!feof($fs)) {
                $contents .= fgets($fs, 1024);
            }

            fclose($fs);
        }

        if (!$contents && $this->current_file != '') {
            $this->alert = Administration::saveMessage('error', $this->langs->line('le_all_error_reading'));
        }

        return [
            'contents' => $contents ?? '',
        ];
    }

    private function getFiles(): array
    {
        chdir(XGP_ROOT . LANG_PATH);

        $langs_files = glob('{,*/,*/*/,*/*/*/}*.php', GLOB_BRACE);
        $lang_options = [];

        foreach ($langs_files as $file) {
            $lang_options[] = [
                'lang_file' => $file,
                'selected' => ($this->current_file == $file) ? 'selected = selected' : '',
            ];
        }

        return ['language_files' => $lang_options];
    }
}
