<?php declare (strict_types = 1);

/**
 * Changelog Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\controllers\adm;

use App\core\BaseController;
use App\libraries\adm\AdministrationLib as Administration;
use App\libraries\Functions;
use DateTime;
use Exception;

/**
 * Changelog Class
 */
class Changelog extends BaseController
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
        parent::loadModel('adm/changelog');

        // load Language
        parent::loadLang(['adm/global', 'adm/changelog']);
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
                        'alert' => $this->getAlertMessage(),
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
        $entries = $this->Changelog_Model->getAllEntries();
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
     * Get the alert message
     *
     * @return string
     */
    private function getAlertMessage(): string
    {
        $action_type = filter_input(INPUT_GET, 'success');
        $alert = '';

        if ($action_type) {
            $alert = Administration::saveMessage(
                'ok',
                $this->langs->line('ch_action_' . $action_type . '_done')
            );
        }

        return $alert;
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
                array_merge(
                    $this->getActionData('add')
                )
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
        $changelog_lang_id = 0;
        $changelog_version = '';
        $changelog_date = date('Y-m-d');
        $changelog_description = '';

        if ($action == 'edit') {
            if ($result = $this->Changelog_Model->getSingleEntry($changelog_id)) {
                $changelog_lang_id = $result->getChangelogLangId();
                $changelog_version = $result->getChangelogVersion();
                $changelog_date = $result->getChangelogDate();
                $changelog_description = $result->getChangelogDescription();
            } else {
                Functions::redirect('admin.php?page=changelog');
            }
        }

        return array_merge(
            $this->langs->language,
            [
                'js_path' => JS_PATH,
                'action' => $action,
                'changelog_id' => $changelog_id,
                'current_action' => strtr(
                    $this->langs->line('ch_' . $action . '_action'),
                    ['%s' => $changelog_date]
                ),
                'changelog_date' => $changelog_date,
                'changelog_version' => $changelog_version,
                'languages' => $this->getAllLanguages($changelog_lang_id),
                'changelog_description' => $changelog_description,
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
            'changelog_id' => [
                'filter' => FILTER_VALIDATE_INT,
            ],
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

        if ($data) {
            $valid = true;

            foreach ($data as $field => $value) {
                if ($value === false or $value === null) {
                    $valid = false;
                    break;
                }
            }

            if ($valid) {
                if ($data['action'] == 'add') {
                    $this->Changelog_Model->addEntry($data);
                }

                if ($data['action'] == 'edit') {
                    $this->Changelog_Model->updateEntry($data);
                }

                Functions::redirect('admin.php?page=changelog&success=' . $data['action']);
            }
        }
    }

    /**
     * Delete an existing record
     *
     * @param integer $changelog_id
     * @return void
     */
    private function deleteAction(int $changelog_id): void
    {
        $this->Changelog_Model->deleteEntry($changelog_id);

        Functions::redirect('admin.php?page=changelog&success=delete');
    }

    /**
     * Build the list of languages
     *
     * @param integer $default_language
     * @return array
     */
    private function getAllLanguages(int $default_language): array
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
            $datetime = new DateTime($date);

            return $datetime->format('Y-m-d');
        } catch (Exception $e) {
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
            $matches
        );

        if (isset($matches[0][0])) {
            return $matches[0][0];
        }

        return null;
    }
}
