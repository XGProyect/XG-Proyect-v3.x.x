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

namespace application\libraries;

use application\core\XGPCore;

/**
 * FunctionsLib Class
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

/**
 * MessagesOptions Class
 *
 * @category Class
 * @package  Libraries
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
final class MessagesOptions
{
    /**
     *
     * @var type
     */
    private $_to;

    /**
     *
     * @var type
     */
    private $_sender;

    /**
     *
     * @var type
     */
    private $_time;

    /**
     *
     * @var type
     */
    private $_type;

    /**
     * 
     * @var type
     */
    private $_from;

    /**
     *
     * @var type
     */
    private $_subject;

    /**
     *
     * @var type
     */
    private $_message_text;

    /**
     *
     * @var type 
     */
    private $_message_format;


    public function getTo()
    {
        return $this->_to;
    }

    public function getSender()
    {
        return $this->_sender;
    }

    public function getTime()
    {
        return $this->_time == '' ? time() : $this->_time;
    }

    public function getType()
    {
        if ($this->_type == '' or !is_object($this->_type)) {

            return MessagesTypes::general;
        }

        return $this->_type;
    }

    public function getFrom()
    {
        return $this->_from;
    }

    public function getSubject()
    {
        return $this->_subject;
    }

    public function getMessageText()
    {
        return $this->_message_text;
    }

    public function getMessageFormat()
    {
        if ($this->_message_format == '') {

            return MessageFormat::simple;
        }

        return $this->_message_format;
    }

    public function setTo($to)
    {
        $this->_to = $to;
    }

    public function setSender($sender)
    {
        $this->_sender = $sender;
    }

    public function setTime($time)
    {
        $this->_time = $time;
    }

    public function setType($type)
    {
        $this->_type = $type;
    }

    public function setFrom($from)
    {
        $this->_from = $from;
    }

    public function setSubject($subject)
    {
        $this->_subject = $subject;
    }

    public function setMessageText($message_text)
    {
        if ($this->_message_format == 1) {

            $this->_message_text    = stripslashes($message_text);
        } else {

            $this->_message_text    = FunctionsLib::formatText($message_text);
        }
    }

    public function setMessageFormat($message_format)
    {
        $this->_message_format = $message_format;
    }
}

/**
 * MessagesTypes Class
 *
 * @category Enumerator
 * @package  Libraries
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
abstract class MessagesTypes
{
    const espio   = 0;
    const combat  = 1;
    const exp     = 2;
    const ally    = 3;
    const user    = 4;
    const general = 5;
}

/**
 * MessagesFormat Class
 *
 * @category Enumerator
 * @package  Libraries
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
abstract class MessagesFormat
{
    const simple    = 0;
    const html      = 1;
}

/* end of Messenger.php */
