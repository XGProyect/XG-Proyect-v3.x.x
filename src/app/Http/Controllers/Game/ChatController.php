<?php

namespace App\Http\Controllers\Game;

use App\Core\BaseController;
use App\Libraries\FormatLib;
use App\Libraries\Functions;
use App\Libraries\Users;
use App\Models\Game\Messages;

class ChatController extends BaseController
{
    public const MODULE_ID = 18;

    private array $_receiver_data = [];
    private array $_message_data = [];
    private Messages $messagesModel;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();

        // load Language
        parent::loadLang(['game/chat']);

        $this->messagesModel = new Messages();
    }

    public function index(): void
    {
        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

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
    private function runAction()
    {
        $write_to = filter_input(INPUT_GET, 'playerId', FILTER_VALIDATE_INT);
        $message_sent = filter_input_array(INPUT_POST);

        if ($write_to) {
            $this->_receiver_data = $this->messagesModel->getHomePlanet($write_to);

            if (!$this->_receiver_data) {
                Functions::redirect('game.php?page=messages');
            }
        }

        $this->_message_data['error_block'] = false;
        $this->_message_data['error_text'] = $this->langs->line('pm_msg_sended');
        $this->_message_data['error_color'] = '#00FF00';

        if ($message_sent) {
            $errors = 0;
            $this->_message_data['error_block'] = true;
            $this->_message_data['subject'] = $message_sent['subject'];
            $this->_message_data['text'] = $message_sent['text'];

            if (!$message_sent['subject']) {
                $errors++;
                $this->_message_data['error_text'] = $this->langs->line('pm_no_subject');
                $this->_message_data['error_color'] = '#FF0000';
            }

            if (!$message_sent['text']) {
                $errors++;
                $this->_message_data['error_text'] = $this->langs->line('pm_no_text');
                $this->_message_data['error_color'] = '#FF0000';
            }

            if ($errors == 0) {
                $this->_message_data['subject'] = '';
                $this->_message_data['text'] = '';

                Functions::sendMessage(
                    $write_to,
                    $this->user['user_id'],
                    '',
                    4,
                    $this->user['user_name'] . ' ' . FormatLib::prettyCoords(
                        $this->user['user_galaxy'],
                        $this->user['user_system'],
                        $this->user['user_planet']
                    ),
                    $message_sent['subject'],
                    $message_sent['text']
                );
            }
        }
    }

    private function buildPage(): void
    {
        $this->page->display(
            $this->template->set(
                'game/chat_view',
                array_merge(
                    $this->langs->language,
                    [
                        'id' => $this->_receiver_data['user_id'],
                        'to' => $this->_receiver_data['user_name'] . ' ' . FormatLib::prettyCoords(
                            $this->_receiver_data['planet_galaxy'],
                            $this->_receiver_data['planet_system'],
                            $this->_receiver_data['planet_planet']
                        ),
                        'subject' => ((!isset($this->_message_data['subject'])) ? $this->langs->line('pm_no_subject') : $this->_message_data['subject']),
                        'text' => ((!isset($this->_message_data['text'])) ? '' : $this->_message_data['text']),
                        'error_text' => ((!isset($this->_message_data['error_text'])) ? '' : $this->_message_data['error_text']),
                        'status_message' => (!$this->_message_data['error_block'] ? [] : ''),
                        '/status_message' => (!$this->_message_data['error_block'] ? [] : ''),
                        'error_color' => ((!isset($this->_message_data['error_color'])) ? '' : $this->_message_data['error_color']),
                        'js_path' => JS_PATH
                    ]
                )
            )
        );
    }
}
