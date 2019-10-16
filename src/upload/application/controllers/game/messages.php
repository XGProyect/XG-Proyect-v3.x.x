<?php
/**
 * Messages Controller
 *
 * PHP Version 5.5+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\controllers\game;

use application\core\Controller;
use application\core\enumerators\MessagesEnumerator;
use application\core\enumerators\SwitchIntEnumerator as SwitchInt;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\OfficiersLib;

use const DPATH;

/**
 * Messages Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Messages extends Controller
{

    const MODULE_ID = 18;

    private $message_type = [
        MessagesEnumerator::espio   => ['type_name' => 'espioopen'],
        MessagesEnumerator::combat  => ['type_name' => 'combatopen'],
        MessagesEnumerator::exp     => ['type_name' => 'expopen'],
        MessagesEnumerator::ally    => ['type_name' => 'allyopen'],
        MessagesEnumerator::user    => ['type_name' => 'useropen'],
        MessagesEnumerator::general => ['type_name' => 'generalopen']
    ];

    /**
     *
     * @var type \Users_library
     */
    private $_user;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/messages');

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();

        // init a new buddy object
        //$this->setUpAlliances();

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();

        // build the page
        $this->buildPage();
    }

    /**
     * Determine the current page and validate it
     * 
     * @return string
     */
    private function getCurrentSection()
    {
        if (!OfficiersLib::isOfficierActive($this->_user['premium_officier_commander'])) {

            return 'premium';
        }

        return 'default';
    }

    /**
     * Run an action
     * 
     * @return void
     */
    private function runAction()
    {
        $mode = filter_input(INPUT_GET, 'mode');

        if (in_array($mode, ['write', 'delete'])) {
            
            $this->{'do' . ucfirst($mode) . 'Action'}();
        }
    }

    /**
     * Build the page
     * 
     * @return void
     */
    private function buildPage()
    {
        parent::$page->display(
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
        $this->Messages_Model->markAsRead($this->_user['user_id']);

        return $this->getTemplate()->set(
            'messages/messages_default_view',
            array_merge(
                $this->getLang(),
                [
                    'message_list' => $this->getListOfMessages(),
                    'operators_list' => $this->buildOperatorsAddressBook()
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
        $active         = [];
        $messages       = [];
        $message_list   = [];
        $delete_options = [];
        $data   = filter_input_array(INPUT_GET, FILTER_VALIDATE_INT);
        
        if (isset($data['dsp']) && $data['dsp'] == 1) {
            
            $get_messages = '';

            foreach ($data as $field => $value) {

                if (FunctionsLib::inMultiarray($field, $this->message_type)) {

                    $type_id            = FunctionsLib::recursiveArraySearch($field, $this->message_type);
                    $get_messages      .= $type_id . ',';
                    $active[$type_id]   = 1;
                }
            }

            // get list of messages
            $messages       = '';
            $message_list   = $this->Messages_Model->getByUserIdAndType($this->_user['user_id'], $get_messages);
            $delete_options = '';

            // set messages as read
            $this->Messages_Model->markAsReadByType($this->_user['user_id'], $get_messages);
        }

        return $this->getTemplate()->set(
            'messages/messages_premium_view',
            array_merge(
                $this->getLang(),
                [
                    'form_submit'       => 'game.php?' . $_SERVER['QUERY_STRING'],
                    'message_type_list' => $this->getMessagesTypesList($active),
                    'messages'          => $messages,
                    '/messages'          => $messages,
                    'messages_list'     => $message_list,
                    'delete_options'    => $delete_options,
                    '/delete_options'    => $delete_options
                ],
                $this->getExtraBlocksDisplay()
            )
        );
    }

    /**
     * Return an array with a list of messages
     * 
     * @return array
     */
    private function getMessagesList(): array
    {
        $messages       = $this->Messages_Model->getByUserId($this->_user['user_id']);
        $messages_list  = [];

        if ($messages) {

            foreach($messages as $message) {

                $messages_list[] = [
                    'message_id' => $message['message_id'],
                    'message_time' => date(
                        strtr(FunctionsLib::readConfig('date_format_extended'), ['.Y' => '']), $message['message_time']
                    ),
                    'message_from' => $message['message_from'],
                    'message_subject' => $message['message_subject'],
                    'message_text' => nl2br($message['message_text'])
                ];
            }
        }

        return $messages_list;
    }

    /**
     * Return an array with a list of operators
     * 
     * @return array
     */
    private function getOperatorsAddressBook(): array
    {
        $operators = $this->Messages_Model->getOperators($this->_user['user_id']);
        $operators_list = [];
        
        if ($operators) {

            foreach($operators as $operator) {

                $operators_list[] = [
                    'user_name' => $operator['user_name'],
                    'user_email' => $operator['user_email'],
                    'dpath' => DPATH
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
    private function getMessagesTypesList($active): array
    {
        $messages_types         = $this->Messages_Model->countMessagesByType($this->_user['user_id']);
        $messages_types_list    = [];

        if ($messages_types) {
            
            foreach ($messages_types as $message_type) {

                $this->message_type[$message_type['message_type']]['count'] = $message_type['message_type_count'];
                $this->message_type[$message_type['message_type']]['unread'] = $message_type['unread_count'];

                $messages_types_list[] = [
                    'message_type'      => $this->message_type[$message_type['message_type']]['type_name'],
                    'checked'           => (isset($active[$message_type['message_type']]) ? 'checked' : ''),
                    'checked_status'    => (isset($active[$message_type['message_type']]) ? SwitchInt::on : SwitchInt::off),
                    'message_type_name' => $this->getLang()['mg_type'][$message_type['message_type']],
                    'message_amount'    => isset($message_type['message_type_count']) ? $message_type['message_type_count'] : 0,
                    'message_unread'    => isset($message_type['unread_count']) ? $message_type['unread_count'] : 0
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
        $buddies        = $this->Messages_Model->getFriends($this->_user['user_id']);
        $buddies_list   = [];

        if ($buddies) {

            foreach($buddies as $buddy) {

                $buddies_list[] = [
                    'user_name' => $buddy['user_name'],
                    'user_id' => $buddy['user_id'],
                    'dpath' => DPATH
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
        $members        = $this->Messages_Model->getAllianceMembers($this->_user['user_id'], $this->_user['user_ally_id']);
        $members_list   = [];
        
        if ($members) {

            foreach($members as $member) {

                $members_list[] = [
                    'user_name' => $buddy['user_name'],
                    'user_id' => $buddy['user_id'],
                    'dpath' => DPATH
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
        $notes      = $this->Messages_Model->getNotes($this->_user['user_id']);
        $notes_list = [];
        
        if ($notes) {

            foreach($notes as $note) {

                $notes_list[] = [
                    'note_id'       => $note['note_id'],
                    'note_color'    => ($note['note_priority'] == 0) ? 'lime' : (($note['note_priority'] == 1) ? 'yellow' : 'red'),
                    'note_title'    => $note['note_title']
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
        $address_book_notes_counts  = $this->Messages_Model->countAddressBookAndNotes($this->_user['user_id'], $this->_user['user_ally_id']);
        $current_extra_block_open   = filter_input_array(INPUT_POST, [
            'owncontactsopen' => FILTER_SANITIZE_STRING,
            'ownallyopen' => FILTER_SANITIZE_STRING,
            'gameoperatorsopen' => FILTER_SANITIZE_STRING,
            'noticesopen' => FILTER_SANITIZE_STRING
        ]);

        $blocks = [
            'owncontactsopen' => [
                'buddy_list'     => $this->getFriendsAddressBook()
            ],
            'ownallyopen' => [
                'members_list'  => $this->getAllinaceAddressBook()
            ],
            'gameoperatorsopen' => [
                'operators_list'=> $this->getOperatorsAddressBook()
            ],
            'noticesopen' => [
                'notes_list'    => $this->getNotesList()
            ]
        ];
        
        $blocks_set = [
            'owncontactsopen' => '',
            'buddys_count'  => $address_book_notes_counts['buddys_count'],
            'buddy_list' => [],
            'ownallyopen' => '',
            'alliance_count' => $address_book_notes_counts['alliance_count'],
            'members_list' => [],
            'gameoperatorsopen' => '', 
            'operators_count' => $address_book_notes_counts['operators_count'],
            'operators_list' => [],
            'noticesopen' => '',
            'notes_count' => $address_book_notes_counts['notes_count'],
            'notes_list' => []
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

    private function doWriteAction()
    {

    }

    private function doDeleteAction()
    {
        
    }

        /*
        // some values by default
        $parse = $this->langs;
        $parse['js_path'] = JS_PATH;

        // display an specific category of items
        if (isset($_GET['dsp']) && $_GET['dsp'] == 1 && $this->have_premium) {
            $mode = '';
            $get_messages = '';

            foreach ($_GET as $field => $value) {

                if (FunctionsLib::inMultiarray($field, $this->message_type)) {

                    $type_id = FunctionsLib::recursiveArraySearch($field, $this->message_type);
                    $get_messages .= $type_id . ',';
                    $active[$type_id] = 1;
                }
            }

            // get list of messages
            $message_list = $this->Messages_Model->getByUserIdAndType($this->_user['user_id'], $get_messages);

            // set messages as read
            $this->Messages_Model->markAsReadByType($this->_user['user_id'], $get_messages);
        } else {

            $mode = isset($_GET['mode']) ? $_GET['mode'] : null;
        }

        // to delete something
        $to_delete = isset($_POST['deletemessages']) ? $_POST['deletemessages'] : null;

        if (isset($to_delete)) {
            $mode = "delete";
        }

        $write_to = isset($_GET['id']) ? (int) $_GET['id'] : null;

        switch ($mode) {
            case 'write':
                $text = '';
                $error_page = '';

                if (!is_numeric($write_to)) {

                    FunctionsLib::redirect('game.php?page=messages');
                } else {

                    $OwnerHome = $this->Messages_Model->getHomePlanet($write_to);

                    if (!$OwnerHome) {
                        FunctionsLib::redirect('game.php?page=messages');
                    }
                }

                if ($_POST) {
                    $error = 0;

                    if (!$_POST['subject']) {
                        $error++;
                        $parse['error_text'] = $this->langs['mg_no_subject'];
                        $parse['error_color'] = '#FF0000';
                        $error_page = parent::$page->parseTemplate(
                            parent::$page->getTemplate('messages/messages_error_table'), $parse
                        );
                    }

                    if (!$_POST['text']) {
                        $error++;
                        $parse['error_text'] = $this->langs['mg_no_text'];
                        $parse['error_color'] = '#FF0000';
                        $error_page = parent::$page->parseTemplate(
                            parent::$page->getTemplate('messages/messages_error_table'), $parse
                        );
                    }

                    if ($error == 0) {
                        $parse['error_text'] = $this->langs['mg_msg_sended'];
                        $parse['error_color'] = '#00FF00';

                        $error_page = parent::$page->parseTemplate(
                            parent::$page->getTemplate('messages/messages_error_table'), $parse
                        );

                        $Owner = $write_to;
                        $Sender = $this->_user['user_id'];
                        $From = $this->_user['user_name'] . ' ' . FormatLib::prettyCoords(
                                $this->_user['user_galaxy'], $this->_user['user_system'], $this->_user['user_planet']
                        );
                        $Subject = $_POST['subject'];
                        $Message = $_POST['text'];

                        FunctionsLib::sendMessage($Owner, $Sender, '', 4, $From, $Subject, $Message);

                        $subject = '';
                        $text = '';
                    }
                }

                $parse['id'] = $write_to;
                $parse['to'] = $OwnerHome['user_name'] . ' ' . FormatLib::prettyCoords(
                        $OwnerHome['planet_galaxy'], $OwnerHome['planet_system'], $OwnerHome['planet_planet']
                );
                $parse['subject'] = (!isset($subject) ) ? $this->langs['mg_no_subject'] : $subject;
                $parse['text'] = $text;
                $parse['status_message'] = $error_page;

                parent::$page->display(
                    parent::$page->parseTemplate(
                        parent::$page->getTemplate('messages/messages_pm_form_view'), $parse
                    )
                );

                break;

            case 'delete':
                if ($to_delete == 'deleteall') {

                    $this->Messages_Model->deleteAllByOwner($this->_user['user_id']);
                } elseif ($to_delete == 'deletemarked') {

                    foreach ($_POST as $Message => $Answer) {

                        if (preg_match("/delmes/i", $Message) && $Answer == 'on') {

                            $MessId = str_replace("delmes", "", $Message);

                            $this->Messages_Model->deleteByOwnerAndId($this->_user['user_id'], $MessId);
                        }
                    }
                } elseif ($to_delete == 'deleteunmarked') {

                    foreach ($_POST as $Message => $Answer) {

                        $MessId = str_replace("showmes", "", $Message);
                        $Selected = "delmes" . $MessId;
                        $IsSelected = $_POST[$Selected];

                        if (preg_match("/showmes/i", $Message) && !isset($IsSelected)) {

                            $this->Messages_Model->deleteByOwnerAndId($this->_user['user_id'], $MessId);
                        }
                    }
                }

                FunctionsLib::redirect('game.php?' . strtr($_SERVER['QUERY_STRING'], ['&amp;' => '&']));

                break;

            default:
                if ($this->have_premium) {

                    // make messages count per type, notes and admins count
                    $this->makeCounts();

                    $parse['form_submit'] = 'game.php?' . $_SERVER['QUERY_STRING'];
                    $type_row_template = parent::$page->getTemplate('messages/messages_body_premium_row_view');
                    $rows = '';

                    while ($messages_list = $this->_db->fetchAssoc($this->_messages_count)) {

                        $this->message_type[$messages_list['message_type']]['count'] = $messages_list['message_type_count'];
                        $this->message_type[$messages_list['message_type']]['unread'] = $messages_list['unread_count'];
                    }

                    foreach ($this->message_type as $id => $data) {

                        $parse['message_type'] = $data['type_name'];
                        $parse['message_type_name'] = $this->langs['mg_type'][$id];
                        $parse['message_amount'] = isset($data['count']) ? $data['count'] : 0;
                        $parse['message_unread'] = isset($data['unread']) ? $data['unread'] : 0;
                        $parse['checked'] = (isset($active[$id]) ? 'checked' : '');
                        $parse['checked_status'] = (isset($active[$id]) ? 1 : 0);

                        $rows .= parent::$page->parseTemplate($type_row_template, $parse);
                    }

                    $parse['message_type_rows'] = $rows;
                    $parse['buddys_count'] = $this->_extra_count['buddys_count'];
                    $parse['alliance_count'] = $this->_extra_count['alliance_count'];
                    $parse['operators_count'] = $this->_extra_count['operators_count'];
                    $parse['notes_count'] = $this->_extra_count['notes_count'];

                    $parse['mg_ab_friends'] = '';
                    $parse['mg_ab_members'] = '';
                    $parse['mg_ab_operators'] = '';
                    $parse['mg_notes_rows'] = '';
                    $parse['owncontactsopen'] = '';
                    $parse['ownallyopen'] = '';
                    $parse['gameoperatorsopen'] = '';
                    $parse['noticesopen'] = '';

                    if (isset($_POST['owncontactsopen']) && $_POST['owncontactsopen'] == 'on') {

                        $parse['owncontactsopen'] = 'checked="1"';
                        $parse['mg_ab_friends'] = $this->buildFriendsAddressBook();
                    }

                    if (isset($_POST['ownallyopen']) && $_POST['ownallyopen'] == 'on') {

                        $parse['ownallyopen'] = 'checked="1"';
                        $parse['mg_ab_members'] = $this->buildAllinaceAddressBook();
                    }

                    if (isset($_POST['gameoperatorsopen']) && $_POST['gameoperatorsopen'] == 'on') {

                        $parse['gameoperatorsopen'] = 'checked="1"';
                        $parse['mg_ab_operators'] = $this->buildOperatorsAddressBook();
                    }

                    if (isset($_POST['noticesopen']) && $_POST['noticesopen'] == 'on') {

                        $parse['noticesopen'] = 'checked="1"';
                        $parse['mg_notes_rows'] = $this->buildNotes();
                    }

                    $parse['message_list'] = isset($message_list) ? $this->loadMessages($message_list) : '';
                    $parse['delete_options'] = isset($_GET['dsp']) ? $this->loadDeleteBox() : '';
                }

                parent::$page->display(
                    parent::$page->parseTemplate($this->setDefaultTemplate(), $parse)
                );

                break;
        }
    }*/
}

/* end of messages.php */
