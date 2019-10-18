<?php
/**
 * Chat Controller
 *
 * PHP Version 7+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\controllers\game;

use application\core\Controller;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

use const JS_PATH;
use const MODULE_ID;

/**
 * Chat Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Chat extends Controller
{

    const MODULE_ID = 18;

    /**
     *
     * @var type \Users_library
     */
    private $_user;

    /**
     * Receiver home planet data
     *
     * @var array
     */
    private $_receiver_data = [];

    /**
     * Private message data
     * 
     * @var array
     */
    private $_message_data = [];

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
        $write_to       = filter_input(INPUT_GET, 'playerId', FILTER_VALIDATE_INT);
        $message_sent   = filter_input_array(INPUT_POST);

        if ($write_to) {
            $this->_receiver_data = $this->Messages_Model->getHomePlanet($write_to);

            if (!$this->_receiver_data) {
                FunctionsLib::redirect('game.php?page=messages');
            }
        }

        $this->_message_data['error_block'] = false;
        $this->_message_data['error_text']  = $this->getLang()['mg_msg_sended'];
        $this->_message_data['error_color'] = '#00FF00';
        $this->_message_data['subject']     = $message_sent['subject'];
        $this->_message_data['text']        = $message_sent['text'];

        if ($message_sent) {

            $errors = 0;
            $this->_message_data['error_block'] = true;

            if (!$message_sent['subject']) {

                $errors++;
                $this->_message_data['error_text']  = $this->getLang()['mg_no_subject'];
                $this->_message_data['error_color'] = '#FF0000';
            }

            if (!$message_sent['text']) {

                $errors++;
                $this->_message_data['error_text']  = $this->getLang()['mg_no_text'];
                $this->_message_data['error_color'] = '#FF0000';
            }

            if ($errors == 0) {

                $this->_message_data['subject'] = '';
                $this->_message_data['text']    = '';

                FunctionsLib::sendMessage(
                    $write_to,
                    $this->_user['user_id'],
                    '',
                    4,
                    $this->_user['user_name'] . ' ' . FormatLib::prettyCoords(
                        $this->_user['user_galaxy'], $this->_user['user_system'], $this->_user['user_planet']
                    ),
                    $message_sent['subject'],
                    $message_sent['text']
                );
            }
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
            $this->getTemplate()->set(
                'game/chat_view',
                array_merge(
                    $this->getLang(),
                    [
                        'id'                => $this->_receiver_data['user_id'],
                        'to'                => $this->_receiver_data['user_name'] . ' ' . FormatLib::prettyCoords(
                            $this->_receiver_data['planet_galaxy'], $this->_receiver_data['planet_system'], $this->_receiver_data['planet_planet']
                        ),
                        'subject'           => ((!isset($this->_message_data['subject']) ) ? $this->getLang()['mg_no_subject'] : $this->_message_data['subject']),
                        'text'              => ((!isset($this->_message_data['text']) ) ? '' : $this->_message_data['text']),
                        'error_text'        => ((!isset($this->_message_data['error_text']) ) ? '' : $this->_message_data['error_text']),
                        'status_message'    => (!$this->_message_data['error_block'] ? [] : ''),
                        '/status_message'   => (!$this->_message_data['error_block'] ? [] : ''),
                        'error_color'       => ((!isset($this->_message_data['error_color']) ) ? '' : $this->_message_data['error_color']),
                        'js_path'           => JS_PATH
                    ]
                )
            ) 
        );
    }
}

/* end of chat.php */
