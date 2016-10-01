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

use application\core\XGPCore;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\OfficiersLib;

/**
 * Messages Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Messages extends XGPCore
{
    const MODULE_ID = 18;

    private $langs;
    private $current_user;
    private $have_premium;
    private $message_type = array(
        0 => array('type_name' => 'espioopen'),
        1 => array('type_name' => 'combatopen'),
        2 => array('type_name' => 'expopen'),
        3 => array('type_name' => 'allyopen'),
        4 => array('type_name' => 'useropen'),
        5 => array('type_name' => 'generalopen')
    );

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        $this->langs        = parent::$lang;
        $this->current_user = parent::$users->getUserData();
        $this->have_premium = OfficiersLib::isOfficierActive($this->current_user['premium_officier_commander']);

        // build the page
        $this->buildPage();
    }

    /**
     * method __destruct
     * param
     * return close db connection
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function buildPage()
    {
        // some values by default
        $parse = $this->langs;
        $parse['js_path'] = XGP_ROOT . JS_PATH;

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
            $message_list = parent::$db->query(
                "SELECT *
                FROM `" . MESSAGES . "`
                WHERE `message_receiver` = " . $this->current_user['user_id'] . "
                        AND `message_type` IN (" . rtrim($get_messages, ',') . ")
                ORDER BY `message_time` DESC;"
            );

            // set messages as read
            parent::$db->query(
                "UPDATE `" . MESSAGES . "`
                SET `message_read` = '1'
                WHERE `message_receiver` = " . $this->current_user['user_id'] . "
                        AND `message_type` IN (" . rtrim($get_messages, ',') . ");"
            );
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

                    $OwnerHome = parent::$db->queryFetch(
                        "SELECT u.`user_name`, p.`planet_galaxy`, p.`planet_system`, p.`planet_planet`
                        FROM " . PLANETS . " AS p
                        INNER JOIN " . USERS . " as u ON p.planet_user_id = u.user_id
                        WHERE p.`planet_user_id` = '" . (int) $write_to . "';"
                    );

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
                            parent::$page->getTemplate('messages/messages_error_table'),
                            $parse
                        );
                    }

                    if (!$_POST['text']) {
                        $error++;
                        $parse['error_text'] = $this->langs['mg_no_text'];
                        $parse['error_color'] = '#FF0000';
                        $error_page = parent::$page->parseTemplate(
                            parent::$page->getTemplate('messages/messages_error_table'),
                            $parse
                        );
                    }

                    if ($error == 0) {
                        $parse['error_text']    = $this->langs['mg_msg_sended'];
                        $parse['error_color']   = '#00FF00';

                        $error_page             = parent::$page->parseTemplate(
                            parent::$page->getTemplate('messages/messages_error_table'),
                            $parse
                        );

                        $Owner      = $write_to;
                        $Sender     = $this->current_user['user_id'];
                        $From       = $this->current_user['user_name'] . ' ' . FormatLib::prettyCoords(
                            $this->current_user['user_galaxy'],
                            $this->current_user['user_system'],
                            $this->current_user['user_planet']
                        );
                        $Subject    = $_POST['subject'];
                        $Message    = FunctionsLib::formatText($_POST['text']);

                        FunctionsLib::sendMessage($Owner, $Sender, '', 4, $From, $Subject, $Message);

                        $subject = '';
                        $text = '';
                    }
                }

                $parse['id']                = $write_to;
                $parse['to']                = $OwnerHome['user_name'] . ' ' . FormatLib::prettyCoords(
                    $OwnerHome['planet_galaxy'],
                    $OwnerHome['planet_system'],
                    $OwnerHome['planet_planet']
                );
                $parse['subject']           = (!isset($subject) ) ? $this->langs['mg_no_subject'] : $subject;
                $parse['text']              = $text;
                $parse['status_message']    = $error_page;

                parent::$page->display(
                    parent::$page->parseTemplate(
                        parent::$page->getTemplate('messages/messages_pm_form_view'),
                        $parse
                    )
                );

                break;

            case 'delete':
                if ($to_delete == 'deleteall') {

                    parent::$db->query(
                        "DELETE FROM " . MESSAGES . "
                        WHERE `message_receiver` = '" . $this->current_user['user_id'] . "';"
                    );

                } elseif ($to_delete == 'deletemarked') {

                    foreach ($_POST as $Message => $Answer) {

                        if (preg_match("/delmes/i", $Message) && $Answer == 'on') {

                            $MessId = str_replace("delmes", "", $Message);
                            $MessHere = parent::$db->queryFetch(
                                "SELECT *
                                FROM " . MESSAGES . "
                                WHERE `message_id` = '" . (int) $MessId . "' AND
                                    `message_receiver` = '" . $this->current_user['user_id'] . "';"
                            );

                            if ($MessHere) {

                                parent::$db->query(
                                    "DELETE FROM " . MESSAGES . "
                                    WHERE `message_id` = '" . (int) $MessId . "';"
                                );
                            }
                        }
                    }
                } elseif ($to_delete == 'deleteunmarked') {

                    foreach ($_POST as $Message => $Answer) {

                        $CurMess = preg_match("/showmes/i", $Message);
                        $MessId = str_replace("showmes", "", $Message);
                        $Selected = "delmes" . $MessId;
                        $IsSelected = $_POST[$Selected];

                        if (preg_match("/showmes/i", $Message) && !isset($IsSelected)) {

                            $MessHere = parent::$db->queryFetch(
                                "SELECT *
                                FROM " . MESSAGES . "
                                WHERE `message_id` = '" . (int) $MessId . "' AND
                                    `message_receiver` = '" . $this->current_user['user_id'] . "';"
                            );

                            if ($MessHere) {
                                parent::$db->queryFetch(
                                    "DELETE FROM " . MESSAGES . "
                                    WHERE `message_id` = '" . (int) $MessId . "';"
                                );
                            }
                        }
                    }
                }

                FunctionsLib::redirect('game.php?' . strtr($_SERVER['QUERY_STRING'], ['&amp;' => '&']));

                break;

            default:
                if ($this->have_premium) {

                    // make messages count per type, notes and admins count
                    $this->makeCounts();
                    
                    $parse['form_submit']   = 'game.php?' . $_SERVER['QUERY_STRING'];
                    $type_row_template      = parent::$page->getTemplate('messages/messages_body_premium_row_view');
                    $rows                   = '';
                    
                    while ($messages_list = parent::$db->fetchAssoc($this->_messages_count)) {

                        $this->message_type[$messages_list['message_type']]['count']
                            = $messages_list['message_type_count'];
                        $this->message_type[$messages_list['message_type']]['unread']
                            = $messages_list['unread_count'];
                    }

                    foreach ($this->message_type as $id => $data) {

                        $parse['message_type']      = $data['type_name'];
                        $parse['message_type_name'] = $this->langs['mg_type'][$id];
                        $parse['message_amount']    = isset($data['count']) ? $data['count'] : 0;
                        $parse['message_unread']    = isset($data['unread']) ? $data['unread'] : 0;
                        $parse['checked']           = (isset($active[$id]) ? 'checked' : '');
                        $parse['checked_status']    = (isset($active[$id]) ? 1 : 0);

                        $rows   .= parent::$page->parseTemplate($type_row_template, $parse);
                    }

                    $parse['message_type_rows'] = $rows;
                    $parse['buddys_count']      = $this->_extra_count['buddys_count'];
                    $parse['alliance_count']    = $this->_extra_count['alliance_count'];
                    $parse['operators_count']   = $this->_extra_count['operators_count'];
                    $parse['notes_count']       = $this->_extra_count['notes_count'];
                    
                    $parse['mg_ab_friends']     = '';
                    $parse['mg_ab_members']     = '';
                    $parse['mg_ab_operators']   = '';
                    $parse['mg_notes_rows']     = '';
                    $parse['owncontactsopen']   = '';
                    $parse['ownallyopen']       = '';
                    $parse['gameoperatorsopen'] = '';
                    $parse['noticesopen']       = '';
                    
                    if (isset($_POST['owncontactsopen']) && $_POST['owncontactsopen'] == 'on') {
                        
                        $parse['owncontactsopen']   = 'checked="1"';
                        $parse['mg_ab_friends']     = $this->buildFriendsAddressBook();
                    }
                    
                    if (isset($_POST['ownallyopen']) && $_POST['ownallyopen'] == 'on') {
                        
                        $parse['ownallyopen']       = 'checked="1"';
                        $parse['mg_ab_members']     = $this->buildAllinaceAddressBook();   
                    }
                    
                    if (isset($_POST['gameoperatorsopen']) && $_POST['gameoperatorsopen'] == 'on') {
                        
                        $parse['gameoperatorsopen'] = 'checked="1"';
                        $parse['mg_ab_operators']   = $this->buildOperatorsAddressBook();
                    }
                    
                    if (isset($_POST['noticesopen']) && $_POST['noticesopen'] == 'on') {
                        
                        $parse['noticesopen']   = 'checked="1"';
                        $parse['mg_notes_rows'] = $this->buildNotes();
                    }
                    
                    $parse['message_list']      = isset($message_list) ? $this->loadMessages($message_list) : '';
                    $parse['delete_options']    = isset($_GET['dsp']) ? $this->loadDeleteBox() : '';
                    
                } else {
                    // get list of messages
                    $message_list = parent::$db->query(
                        "SELECT *
                        FROM `" . MESSAGES . "`
                        WHERE `message_receiver` = " . $this->current_user['user_id'] . " 
                        ORDER BY `message_time` DESC;"
                    );
                    
                    // set messages as read
                    parent::$db->query(
                        "UPDATE `" . MESSAGES . "`
                        SET `message_read` = '1'
                        WHERE `message_receiver` = " . $this->current_user['user_id'] . ";"
                    );

                    $single_message_template    = parent::$page->getTemplate('messages/messages_list_row_view');
                    $list_of_messages           = '';

                    while ($message = parent::$db->fetchArray($message_list)) {

                        $message['message_text']    = nl2br($message['message_text']);
                        $message['message_time']    = date(
                            strtr(FunctionsLib::readConfig('date_format_extended'),['.Y' => '']),
                            $message['message_time']
                        );

                        $list_of_messages   .= parent::$page->parseTemplate($single_message_template, $message);
                    }

                    $parse['message_list']      = $list_of_messages;
                    $parse['show_operators']    = $this->buildOperatorsBlock();
                }

                parent::$page->display(
                    parent::$page->parseTemplate($this->setDefaultTemplate(), $parse)
                );

                break;
        }
    }

    /**
     * loadMessages
     *
     * @param
     *
     * @return
     */
    private function loadMessages($messages)
    {
        $single_message_template    = parent::$page->getTemplate('messages/messages_list_row_view');
        $list_of_messages           = '';

        while ($message = parent::$db->fetchArray($messages)) {

            $message['message_text']    = nl2br($message['message_text']);
            $message['message_time']    = date(
                strtr(FunctionsLib::readConfig('date_format_extended'),['.Y' => '']),
                $message['message_time']
            );
            $list_of_messages           .= parent::$page->parseTemplate($single_message_template, $message);
        }

        $parse                  = $this->langs;
        $parse['message_list']  = $list_of_messages;

        return parent::$page->parseTemplate(
            parent::$page->getTemplate('messages/messages_list_container_view'),
            $parse
        );
    }

    /**
     * loadDeleteBox
     *
     * @return void
     */
    private function loadDeleteBox()
    {
        return parent::$page->parseTemplate(
            parent::$page->getTemplate('messages/messages_delete_options_view'),
            $this->langs
        );
    }

    /**
     * makeCounts
     *
     * @return void
     */
    private function makeCounts()
    {
        $this->_messages_count = parent::$db->query(
            "SELECT 
                `message_type`,
                COUNT(`message_type`) AS message_type_count,
                SUM(`message_read` = 0) AS unread_count
            FROM " . MESSAGES . "
            WHERE `message_receiver` = '" . $this->current_user['user_id'] . "'
            GROUP BY `message_type`"
        );

        $this->_extra_count = parent::$db->queryFetch(
            "SELECT
                ( SELECT COUNT(`user_id`)
                    FROM `" . USERS . "`
                    WHERE `user_ally_id` = '" . $this->current_user['user_ally_id'] . "' 
                        AND `user_ally_id` <> 0
                        AND `user_id` <> '" . $this->current_user['user_id'] . "'
                 ) AS alliance_count,

                 ( SELECT COUNT(`buddy_id`)
                    FROM `" . BUDDY . "`
                    WHERE `buddy_sender` = '" . $this->current_user['user_id'] . "' 
                        OR `buddy_receiver` = '" . $this->current_user['user_id'] . "'
                 ) AS buddys_count,

                 ( SELECT COUNT(`note_id`)
                    FROM `" . NOTES . "`
                    WHERE `note_owner` = '" . $this->current_user['user_id'] . "'
                 ) AS notes_count,

                 ( SELECT COUNT(`user_id`)
                    FROM " . USERS . "
                    WHERE user_authlevel <> 0
                        AND `user_id` <> '" . $this->current_user['user_id'] . "'
                 ) AS operators_count"
        );
    }

    /**
     * setDefaultTemplate
     *
     * @return string
     */
    private function setDefaultTemplate()
    {
        if ($this->have_premium) {

            return parent::$page->getTemplate('messages/messages_body_premium_view');
        } else {

            return parent::$page->getTemplate('messages/messages_body_common_view');
        }
    }
    
    /**
     * Build the friends block to display
     * 
     * @return string
     */
    private function buildFriendsAddressBook()
    {
        $list_of_friends  = '';
        $friends_list     = parent::$db->query(
            "SELECT 
                u.`user_id`,
                u.`user_name`,
                u.`user_email`
            FROM " . BUDDY . " b
            LEFT JOIN " . USERS . " u ON u.user_id = IF(`buddy_sender` = '" . $this->current_user['user_id'] . "', `buddy_receiver`, `buddy_sender`) 
            WHERE `buddy_sender`='" . $this->current_user['user_id'] . "' 
                OR `buddy_receiver`='" . $this->current_user['user_id'] . "'"
        );

        while ($friends = parent::$db->fetchArray($friends_list)) {

            $friends['dpath']   = DPATH;
            $list_of_friends  .= parent::$page->get('messages/messages_ab_user_row_view')->parse($friends);
        }
        
        return $list_of_friends;
    }
    
    /**
     * Build the alliance members block to display
     * 
     * @return string
     */
    private function buildAllinaceAddressBook()
    {
        $list_of_members  = '';
        $members_list     = parent::$db->query(
            "SELECT `user_id`, `user_name`, `user_email` 
            FROM " . USERS . " 
            WHERE user_ally_id = '" . $this->current_user['user_ally_id'] . "'
                AND `user_id` <> '" . $this->current_user['user_id'] . "';"
        );

        while ($members = parent::$db->fetchArray($members_list)) {

            $members['dpath']   = DPATH;
            $list_of_members  .= parent::$page->get('messages/messages_ab_user_row_view')->parse($members);
        }
        
        return $list_of_members;
    }
    
    /**
     * Build the operators block to display
     * 
     * @return string
     */
    private function buildOperatorsAddressBook()
    {
        $list_of_operators  = '';
        $operators_list     = parent::$db->query(
            "SELECT `user_name`, `user_email` 
            FROM " . USERS . " 
            WHERE user_authlevel > '0'
                AND `user_id` <> '" . $this->current_user['user_id'] . "';"
        );

        while ($operator = parent::$db->fetchArray($operators_list)) {

            $operator['dpath']   = DPATH;
            $list_of_operators  .= parent::$page->get('messages/messages_ab_adm_row_view')->parse($operator);
        }
        
        return $list_of_operators;
    }
    
    /**
     * Build the notes block to display
     * 
     * @return string
     */
    private function buildNotes()
    {
        $list_of_notes  = '';
        $notes_list     = parent::$db->query(
            "SELECT `note_id`, `note_priority`, `note_title`
            FROM `" . NOTES . "`
            WHERE `note_owner` = '" . $this->current_user['user_id'] . "';"
        );

        while ($notes = parent::$db->fetchArray($notes_list)) {

            $notes['dpath'] = DPATH;
            $notes['color'] = ($notes['note_priority'] == 0) ? 'lime' : (($notes['note_priority'] == 1) ? 'yellow' : 'red');
            $list_of_notes .= parent::$page->get('messages/messages_notes_row_view')->parse($notes);
        }
        
        return $list_of_notes;
    }
}

/* end of messages.php */
