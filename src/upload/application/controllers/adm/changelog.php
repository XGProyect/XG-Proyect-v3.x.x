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
                $this->getActionData('edit', $changelog_id)
            )
        );
    }

    /**
     * Get data to build the action add or action edit pages
     *
     * @param string $action
     * @param integer $changelog_id
     * @return array
     */
    private function getActionData(string $action, int $changelog_id = 0): array
    {
        return array_merge(
            $this->langs->language,
            [
                'js_path' => JS_PATH,
                'alert' => '',
                'action' => $action,
                'current_action' => $this->langs->line('ch_' . $action . '_action'),
                'changelog_version' => '',
                'languages' => $this->getAllLanguages($changelog_id),
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
        $data = filter_input_array(INPUT_POST, [
            'action' => [
                'filter' => FILTER_CALLBACK,
                'options' => [$this, 'isValidAction'],
            ],
            'changelog_date' => [
                'filter' => FILTER_CALLBACK,
                'options' => [$this, 'isValidDate'],
            ],
            'changelog_version' => [
                'filter' => FILTER_CALLBACK,
                'options' => [$this, 'isValidVersion'],
            ],
            'changelog_language' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => [
                    'default' => 1,
                    'min_range' => 1,
                ],
            ],
            'text' => [
                'filter' => FILTER_SANITIZE_STRING,
            ], // changelog description
        ]);

        // clean data, remove nulls and false, which didn't pass validations
        $data = array_diff($data, [null, false]);

        //var_dump($data);die();
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

    /**
     * Check if it is a valid action, add or edit
     *
     * @param string|null $action
     * @return string|null
     */
    private function isValidAction(?string $action): ?string
    {
        if (\in_array($action, ['add', 'edit'])) {
            return $action;
        }

        return null;
    }

    /**
     * Check if it is a valid date
     *
     * @param string|null $date
     * @return string|null
     */
    private function isValidDate(?string $date): ?string
    {
        try {
            $datetime = new \DateTime($date);

            return $datetime->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Performs a regular expression check to determine if a valid version was provided
     *
     * @param string|null $version
     * @return boolean
     */
    private function isValidVersion(?string $version): ?string
    {
        preg_match_all(
            '/^(0|[1-9]\d*)\.((0|[1-9]\d*)\.)?(0|[1-9]\d*)(-(0|[1-9]\d*|\d*[a-zA-Z][0-9a-zA-Z]*))?$/',
            $version,
            $matches //,
            //PREG_UNMATCHED_AS_NULL
        );

        if (isset($matches[0][0])) {
            return $matches[0][0];
        }

        return null;
    }
}

/* end of changelog.php */
