<?php declare (strict_types = 1);

/**
 * Messenger Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\libraries\messenger;

use App\core\Model;
use App\libraries\messenger\MessagesOptions;

/**
 * Messenger Class
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
