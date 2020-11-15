<?php declare (strict_types = 1);

/**
 * Messages Controller
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
use App\core\enumerators\MessagesEnumerator;
use App\libraries\adm\AdministrationLib as Administration;
use App\libraries\TimingLibrary as Timing;

/**
 * Messages Class
 */
class Messages extends BaseController
{
    /**
     * Contains the alert string
     *
     * @var string
     */
    private $alert = '';

    /**
     * Contains a list of results
     *
     * @var array
     */
    private $results = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Model
        parent::loadModel('adm/messages');

        // load Language
        parent::loadLang(['adm/global', 'adm/messages']);
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
        $action = filter_input_array(INPUT_POST);
        $single_delete = filter_input_array(INPUT_GET, [
            'action' => FILTER_SANITIZE_STRING,
            'messageId' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
            ],
        ]);

        if ($action) {
            $filtered_action = array_filter(
                $action,
                function ($value) {
                    return !is_null($value) && $value !== false && $value !== '';
                }
            );

            if (isset($filtered_action['search'])) {
                $this->doSearch($filtered_action);
            }

            if (isset($filtered_action['delete_messages'])) {
                $this->deleteMessages($filtered_action['delete_messages']);
            }
        }

        if (isset($single_delete['action']) == 'delete'
            && isset($single_delete['messageId'])) {
            $this->deleteMessage($single_delete['messageId']);
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
                'adm/messages_view',
                array_merge(
                    $this->langs->language,
                    $this->buildMessageTypeBlock(),
                    [
                        'alert' => $this->alert,
                        'results' => $this->results,
                        'show_search' => $this->results ? '' : 'show',
                        'show_results' => $this->results ? 'show' : '',
                    ]
                )
            )
        );
    }

    /**
     * Execute messages search
     *
     * @return void
     */
    private function doSearch(array $to_search): void
    {
        // build the query, run the query and return the result
        $search_results = $this->Messages_Model->getAllMessagesFiltered($to_search);
        $results_list = [];

        if ($search_results) {
            foreach ($search_results as $result) {
                $results_list[] = array_merge(
                    $this->langs->language,
                    $result,
                    [
                        'message_time' => Timing::formatExtendedDate($result['message_time']),
                        'message_type' => $this->langs->language['mg_types'][$result['message_type']],
                        'message_text' => nl2br($result['message_text']),
                    ]
                );
            }

            $this->results = $results_list;
        } else {
            $this->alert = Administration::saveMessage('warning', $this->langs->line('mg_no_results'));
        }
    }

    /**
     * Delete a single message
     *
     * @param integer $message_id
     * @return void
     */
    private function deleteMessage(int $message_id): void
    {
        $this->Messages_Model->deleteAllMessagesByIds([$message_id]);

        $this->alert = Administration::saveMessage('ok', $this->langs->line('mg_delete_ok'));
    }

    /**
     * Delete multiple messages
     *
     * @param array $messages
     * @return void
     */
    private function deleteMessages(array $messages): void
    {
        $ids = [];

        // build the ID's list to delete, we're going to delete them all in one single query
        foreach ($messages as $message_id => $delete_status) {
            if ($delete_status == 'on' && $message_id > 0 && is_numeric($message_id)) {
                $ids[] = $message_id;
            }
        }

        $this->Messages_Model->deleteAllMessagesByIds($ids);

        $this->alert = Administration::saveMessage('ok', $this->langs->line('mg_delete_ok'));
    }

    /**
     * Build the list of message types
     *
     * @return array
     */
    private function buildMessageTypeBlock(): array
    {
        $options_list = [];
        $message_types = [
            MessagesEnumerator::ESPIO,
            MessagesEnumerator::COMBAT,
            MessagesEnumerator::EXP,
            MessagesEnumerator::ALLY,
            MessagesEnumerator::USER,
            MessagesEnumerator::GENERAL,
        ];

        foreach ($message_types as $type) {
            $options_list[] = [
                'value' => $type,
                'name' => $this->langs->language['mg_types'][$type],
            ];
        }

        return [
            'type_options' => $options_list,
        ];
    }
}
