<?php

declare (strict_types = 1);

/**
 * Languages Controller
 *
 * PHP Version 7.1+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\controllers\adm;

use application\core\Controller;
use application\libraries\adm\AdministrationLib as Administration;

/**
 * Languages Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Languages extends Controller
{
    /**
     * Current user data
     *
     * @var array
     */
    private $user;

    /**
     * Contains the alert string
     *
     * @var string
     */
    private $alert = '';

    /**
     * Contains the current file
     *
     * @var string
     */
    private $current_file = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/languages']);

        // set data
        $this->user = $this->getUserData();

        // Check if the user is allowed to access
        if ($this->user['user_authlevel'] != 3) {
            Administration::noAccessMessage($this->langs->line('no_permissions'));
        }

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Run an action
     *
     * @return void
     */
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

    /**
     * Set the file that we are going to modify
     *
     * @param string $file
     * @return void
     */
    private function doFileAction(string $file): void
    {
        $this->current_file = $file;
    }

    /**
     * Save the file contents
     *
     * @param string $file_data
     * @return void
     */
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

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
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

    /**
     * Get the language file contents
     *
     * @return array
     */
    private function getContents(): array
    {
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

    /**
     * Get the list of language files
     *
     * @return void
     */
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

/* end of languages.php */
