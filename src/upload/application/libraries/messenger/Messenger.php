<?php
/**
 * Functions Library
 *
 * PHP Version 5.5+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */

namespace application\libraries\messenger;

use application\core\XGPCore;

/**
 * Messenger Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
final class Messenger extends XGPCore
{
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Send a message with the provided options
     *
     * @param \application\libraries\MessagesOptions $options
     */
    public function sendMessage(MessagesOptions $options)
    {
        // TODO: call a model instead of this
        parent::$db->query(
            "INSERT INTO " . MESSAGES . " SET
            `message_receiver` = '" . $options->getTo() . "',
            `message_sender` = '" . $options->getSender() . "',
            `message_time` = '" . $options->getTime() . "',
            `message_type` = '" . $options->getType() . "',
            `message_from` = '" . $options->getFrom() . "',
            `message_subject` = '" . $options->getSubject() . "',
            `message_text` 	= '" . $options->getMessageText() . "';"
        );
    }
}

/* end of Messenger.php */
