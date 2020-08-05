<?php

declare (strict_types = 1);

/**
 * Changelog Controller
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
use application\libraries\adm\AdministrationLib;

/**
 * Changelog Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Changelog extends Controller
{
    /**
     * Current user data
     *
     * @var array
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        // load Model
        parent::loadModel('adm/changelog');

        // load Language
        parent::loadLang(['adm/global', 'adm/changelog']);

        // set data
        $this->user = $this->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::authorization($this->user['user_authlevel'], 'edit_users') != 1) {
            AdministrationLib::noAccessMessage($this->langs->line('no_permissions'));
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
        // route to the right page
        $allowed_actions = ['add', 'edit', 'delete'];
        $sub_page = filter_input(INPUT_GET, 'action');
        $changelog_id = filter_input(INPUT_GET, 'changelogId', FILTER_VALIDATE_INT);

        if (isset($sub_page) && isset($changelog_id)) {
            $this->{$sub_page . 'Action'}($changelog_id);
        }

        if (isset($sub_page) && !isset($changelog_id)) {
            $this->{$sub_page . 'Action'}();
        }
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
                'adm/changelog_view',
                array_merge(
                    $this->langs->language,
                    [
                        'changelog' => $this->buildListOfEntries(),
                    ]
                )
            )
        );
    }

    /**
     * Build the list of changelog entries
     *
     * @return array
     */
    private function buildListOfEntries(): array
    {
        $entries = $this->Changelog_Model->getAllItems();
        $entries_list = [];

        foreach ($entries as $entry) {
            $entries_list[] = array_merge(
                $this->langs->language,
                $entry
            );
        }

        return $entries_list;
    }

    /**
     * Show add a new entry page
     *
     * @return void
     */
    private function addAction(): void
    {
        $this->saveAction();

        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/changelog_form_view',
                $this->getActionData('add')
            )
        );
    }

    /**
     * Show edit an existing entry page
     *
     * @param integer $changelog_id
     * @return void
     */
    private function editAction(int $changelog_id): void
    {
        $this->saveAction();

        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/changelog_form_view',
                $this->getActionData('edit')
            )
        );
    }

    /**
     * Get data to build the action add or action edit pages
     *
     * @param string $action
     * @return array
     */
    private function getActionData(string $action): array
    {
        return array_merge(
            $this->langs->language,
            [
                'js_path' => JS_PATH,
                'alert' => '',
                'action' => $action,
                'current_action' => $this->langs->line('ch_' . $action . '_action'),
                'changelog_version' => '',
                'languages' => $this->getAllLanguages(),
            ]
        );
    }

    /**
     * Save action to add/edit a record
     *
     * @param string $source
     * @return void
     */
    private function saveAction(): void
    {
        // post actions
        $data = filter_input_array(INPUT_POST);

        if (isset($data) && $data['action'] == 'add') {

        }

        if (isset($data) && $data['action'] == 'edit') {

        }
    }

    private function deleteAction(int $changelog_id): void
    {

    }

    /**
     * Build the list of languages
     *
     * @param integer $default_language
     * @return array
     */
    private function getAllLanguages(int $default_language = 0): array
    {
        $languages = $this->Changelog_Model->getAllLanguages();
        $list_of_languages = [];

        foreach ($languages as $language) {
            $list_of_languages[] = array_merge(
                $language,
                [
                    'selected' => ($default_language == $language['language_id'] ? 'selected' : ''),
                ]
            );
        }

        return $list_of_languages;
    }
}

/* end of changelog.php */
