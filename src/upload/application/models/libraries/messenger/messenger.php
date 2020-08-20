<?php

declare (strict_types = 1);

/**
 * Messenger Model
 *
 * PHP Version 7.1+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\models\libraries\messenger;

use application\core\Model;
use application\libraries\messenger\MessagesOptions;

/**
 * Messenger Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Messenger extends Model
{
    /**
     * Insert a new message
     *
     * @param MessagesOptions $options
     * @return void
     */
    public function insertMessage(MessagesOptions $options): void
    {
        $this->db->query(
            "INSERT INTO `" . MESSAGES . "` SET
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

/* end of messenger.php */
