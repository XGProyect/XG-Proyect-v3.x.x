<?php

namespace App\Http\Controllers\Game;

use App\Core\BaseController;
use App\Core\Enumerators\MessagesEnumerator;
use App\Core\Enumerators\SwitchIntEnumerator as SwitchInt;
use App\Helpers\ArraysHelper;
use App\Helpers\UrlHelper;
use App\Libraries\Functions;
use App\Libraries\OfficiersLib;
use App\Libraries\Users;
use App\Models\Game\Messages;

class MessagesController extends BaseController
{
    public const MODULE_ID = 18;

    private array $message_type = [
        MessagesEnumerator::ESPIO => ['type_name' => 'espioopen'],
        MessagesEnumerator::COMBAT => ['type_name' => 'combatopen'],
        MessagesEnumerator::EXP => ['type_name' => 'expopen'],
        MessagesEnumerator::ALLY => ['type_name' => 'allyopen'],
        MessagesEnumerator::USER => ['type_name' => 'useropen'],
        MessagesEnumerator::GENERAL => ['type_name' => 'generalopen'],
    ];
    private Messages $messagesModel;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();

        // load Language
        parent::loadLang(['game/messages']);

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
     * Determine the current page and validate it
     *
     * @return string
     */
    private function getCurrentSection(): string
    {
        if (OfficiersLib::isOfficierActive($this->user['premium_officier_commander'])) {
            return 'premium';
        }

        return 'default';
    }

    /**
     * Run an action
     *
     * @return void
     */
    private function runAction(): void
    {
        $delete = filter_input(INPUT_POST, 'deletemessages');

        if (in_array($delete, ['deleteall', 'deletemarked', 'deleteunmarked', 'deleteallshown'])) {
            $this->doDeleteAction();
        }
    }

    private function buildPage(): void
    {
        $this->page->display(
            $this->{'get' . ucfirst($this->getCurrentSection()) . 'Section'}()
        );
    }

    /**
     * Build the default messages section
     *
     * @return string
     */
    private function getDefaultSection(): string
    {
        // set messages as read
        $this->messagesModel->markAsRead($this->user['user_id']);

        return $this->template->set(
            'game/messages_default_view',
            array_merge(
                $this->langs->language,
                [
                    'message_list' => $this->getMessagesList(
                        $this->messagesModel->getByUserId($this->user['user_id'])
                    ),
                    'operators_list' => $this->getOperatorsAddressBook(),
                ]
            )
        );
    }

    /**
     * Build the premium messages section
     *
     * @return string
     */
    private function getPremiumSection(): string
    {
        // display an specific category of items
        $active = [];
        $messages = [];
        $message_list = [];
        $delete_options = [];
        $data = filter_input_array(INPUT_GET, FILTER_VALIDATE_INT);

        if (isset($data['dsp']) && $data['dsp'] == 1) {
            $get_messages = '';

            foreach ($data as $field => $value) {
                if (ArraysHelper::inMultiArray($field, $this->message_type)) {
                    $type_id = ArraysHelper::multiArraySearch($field, $this->message_type);
                    $get_messages .= $type_id . ',';
                    $active[$type_id] = 1;
                }
            }

            // get list of messages
            $messages = '';
            $message_list = $this->getMessagesList(
                $this->messagesModel->getByUserIdAndType($this->user['user_id'], $get_messages)
            );
            $delete_options = '';

            // set messages as read
            $this->messagesModel->markAsReadByType($this->user['user_id'], $get_messages);
        }

        return $this->template->set(
            'game/messages_premium_view',
            array_merge(
                $this->langs->language,
                [
                    'form_submit' => 'game.php?' . $_SERVER['QUERY_STRING'],
                    'message_type_list' => $this->getMessagesTypesList($active),
                    'messages' => $messages,
                    '/messages' => $messages,
                    'messages_list' => $message_list,
                    'delete_options' => $delete_options,
                    '/delete_options' => $delete_options,
                ],
                $this->getExtraBlocksDisplay()
            )
        );
    }

    /**
     * Return an array with a list of messages
     *
     * @param string $messages The messages
     *
     * @return array
     */
    private function getMessagesList($messages): array
    {
        $messages_list = [];

        if ($messages) {
            foreach ($messages as $message) {
                $messages_list[] = [
                    'message_id' => $message['message_id'],
                    'message_time' => date(
                        strtr(Functions::readConfig('date_format_extended'), ['.Y' => '']),
                        $message['message_time']
                    ),
                    'message_from' => $message['message_from'],
                    'message_subject' => $message['message_subject'],
                    'message_text' => nl2br($message['message_text']),
                    'message_reply' => $this->setMessageReply($message['message_sender']),
                ];
            }
        }

        return $messages_list;
    }

    /**
     * Set the message reply icon
     *
     * @param integer $from
     * @return string
     */
    private function setMessageReply(int $from): string
    {
        if ($from > 0) {
            return UrlHelper::setUrl(
                'game.php?page=chat&playerId=' . $from,
                Functions::setImage(DPATH . '/img/m.gif', $this->langs->line('mg_send_message')),
                $this->langs->line('mg_send_message')
            );
        }

        return '';
    }

    /**
     * Return an array with a list of operators
     *
     * @return array
     */
    private function getOperatorsAddressBook(): array
    {
        $operators = $this->messagesModel->getOperators($this->user['user_id']);
        $operators_list = [];

        if ($operators) {
            foreach ($operators as $operator) {
                $operators_list[] = [
                    'user_name' => $operator['user_name'],
                    'user_email' => $operator['user_email'],
                    'dpath' => DPATH,
                ];
            }
        }

        return $operators_list;
    }

    /**
     * Build the list of message types
     *
     * @return array
     */
    private function getMessagesTypesList(array $active): array
    {
        $messages_types = $this->messagesModel->countMessagesByType($this->user['user_id']);
        $messages_types_list = [];

        if ($messages_types) {
            foreach ($messages_types as $message_type) {
                $this->message_type[$message_type['message_type']]['count'] = $message_type['message_type_count'];
                $this->message_type[$message_type['message_type']]['unread'] = $message_type['unread_count'];

                $messages_types_list[] = [
                    'message_type' => $this->message_type[$message_type['message_type']]['type_name'],
                    'checked' => (isset($active[$message_type['message_type']]) ? 'checked' : ''),
                    'checked_status' => (isset($active[$message_type['message_type']]) ? SwitchInt::on : SwitchInt::off),
                    'message_type_name' => $this->langs->language['mg_type'][$message_type['message_type']],
                    'message_amount' => isset($message_type['message_type_count']) ? $message_type['message_type_count'] : 0,
                    'message_unread' => isset($message_type['unread_count']) ? $message_type['unread_count'] : 0,
                ];
            }
        }

        return $messages_types_list;
    }

    /**
     * Build the friends block to display
     *
     * @return array
     */
    private function getFriendsAddressBook(): array
    {
        $buddies = $this->messagesModel->getFriends($this->user['user_id']);
        $buddies_list = [];

        if ($buddies) {
            foreach ($buddies as $buddy) {
                $buddies_list[] = [
                    'user_name' => $buddy['user_name'],
                    'user_id' => $buddy['user_id'],
                    'dpath' => DPATH,
                ];
            }
        }

        return $buddies_list;
    }

    /**
     * Build the alliance members block to display
     *
     * @return array
     */
    private function getAllinaceAddressBook(): array
    {
        $members = $this->messagesModel->getAllianceMembers($this->user['user_id'], $this->user['user_ally_id']);
        $members_list = [];

        if ($members) {
            foreach ($members as $member) {
                $members_list[] = [
                    'user_name' => $member['user_name'],
                    'user_id' => $member['user_id'],
                    'dpath' => DPATH,
                ];
            }
        }

        return $members_list;
    }

    /**
     * Build the notes block to display
     *
     * @return array
     */
    private function getNotesList(): array
    {
        $notes = $this->messagesModel->getNotes($this->user['user_id']);
        $notes_list = [];

        if ($notes) {
            foreach ($notes as $note) {
                $notes_list[] = [
                    'note_id' => $note['note_id'],
                    'note_color' => ($note['note_priority'] == 0) ? 'lime' : (($note['note_priority'] == 1) ? 'yellow' : 'red'),
                    'note_title' => $note['note_title'],
                ];
            }
        }

        return $notes_list;
    }

    /**
     * Get extra blocks
     *
     * @return array
     */
    private function getExtraBlocksDisplay(): array
    {
        $address_book_notes_counts = $this->messagesModel->countAddressBookAndNotes($this->user['user_id'], $this->user['user_ally_id']);
        $current_extra_block_open = filter_input_array(INPUT_POST, [
            'owncontactsopen' => FILTER_UNSAFE_RAW,
            'ownallyopen' => FILTER_UNSAFE_RAW,
            'gameoperatorsopen' => FILTER_UNSAFE_RAW,
            'noticesopen' => FILTER_UNSAFE_RAW,
        ]);

        $blocks = [
            'owncontactsopen' => [
                'buddy_list' => $this->getFriendsAddressBook(),
            ],
            'ownallyopen' => [
                'members_list' => $this->getAllinaceAddressBook(),
            ],
            'gameoperatorsopen' => [
                'operators_list' => $this->getOperatorsAddressBook(),
            ],
            'noticesopen' => [
                'notes_list' => $this->getNotesList(),
            ],
        ];

        $blocks_set = [
            'owncontactsopen' => '',
            'buddys_count' => $address_book_notes_counts['buddys_count'],
            'buddy_list' => [],
            'ownallyopen' => '',
            'alliance_count' => $address_book_notes_counts['alliance_count'],
            'members_list' => [],
            'gameoperatorsopen' => '',
            'operators_count' => $address_book_notes_counts['operators_count'],
            'operators_list' => [],
            'noticesopen' => '',
            'notes_count' => $address_book_notes_counts['notes_count'],
            'notes_list' => [],
        ];

        if ($current_extra_block_open) {
            foreach ($current_extra_block_open as $key => $value) {
                if ($value == 'on') {
                    $blocks_set = array_merge($blocks_set, $blocks[$key], [$key => 'checked="1"']);
                }
            }
        }

        return $blocks_set;
    }

    /**
     * Execute a delete action
     *
     * @return void
     */
    private function doDeleteAction(): void
    {
        $delete = filter_input(INPUT_POST, 'deletemessages');
        $messages_to_delete = filter_input_array(INPUT_POST);
        $type_to_delete = filter_input_array(INPUT_GET);

        switch ($delete) {
            case 'deleteall':
                $this->messagesModel->deleteAllByOwner($this->user['user_id']);
                break;
            case 'deletemarked':
                foreach ($messages_to_delete as $message => $checked) {
                    if (preg_match('/delmes/i', $message) && $checked == 'on') {
                        $message_id = str_replace('delmes', '', $message);

                        $message_ids[] = $message_id;
                    }
                }

                if (isset($message_ids)) {
                    $this->messagesModel->deleteByOwnerAndIds($this->user['user_id'], join(',', $message_ids));
                }
                break;
            case 'deleteunmarked':
                foreach ($messages_to_delete as $message => $checked) {
                    $message_id = str_replace('showmes', '', $message);
                    $selected = 'delmes' . $message_id;

                    if (preg_match('/showmes/i', $message) && !isset($messages_to_delete[$selected])) {
                        $message_ids[] = $message_id;
                    }
                }

                if (isset($message_ids)) {
                    $this->messagesModel->deleteByOwnerAndIds($this->user['user_id'], join(',', $message_ids));
                }
                break;
            case 'deleteallshown':
                $data = filter_input_array(INPUT_GET, FILTER_VALIDATE_INT);

                if (isset($data['dsp']) && $data['dsp'] == 1) {
                    foreach ($data as $field => $value) {
                        if (ArraysHelper::inMultiArray($field, $this->message_type)) {
                            $type_id = ArraysHelper::multiArraySearch($field, $this->message_type);
                            break;
                        }
                    }

                    if (isset($type_id)) {
                        $this->messagesModel->deleteByOwnerAndMessageType($this->user['user_id'], $type_id);
                    }
                }
                break;
            default:
                break;
        }

        Functions::redirect('game.php?' . strtr($_SERVER['QUERY_STRING'], ['&amp;' => '&']));
    }
}
