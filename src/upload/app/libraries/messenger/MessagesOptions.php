<?php
/**
 * Messenger Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
namespace App\libraries\messenger;

use App\core\enumerators\MessagesEnumerator;
use App\helpers\StringsHelper;

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

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->_to;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->_sender == '' ? 0 : $this->_sender;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->_time == '' ? time() : $this->_time;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        if ($this->_type == '' or !is_object($this->_type)) {
            return MessagesEnumerator::GENERAL;
        }

        return $this->_type;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->_from;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * @return mixed
     */
    public function getMessageText()
    {
        return $this->_message_text;
    }

    /**
     * @return mixed
     */
    public function getMessageFormat()
    {
        if ($this->_message_format == '') {
            return MessageFormat::simple;
        }

        return $this->_message_format;
    }

    /**
     * @param $to
     */
    public function setTo($to)
    {
        $this->_to = $to;
    }

    /**
     * @param $sender
     */
    public function setSender($sender)
    {
        $this->_sender = $sender;
    }

    /**
     * @param $time
     */
    public function setTime($time)
    {
        $this->_time = $time;
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @param $from
     */
    public function setFrom($from)
    {
        $this->_from = $from;
    }

    /**
     * @param $subject
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
    }

    /**
     * @param $message_text
     */
    public function setMessageText($message_text)
    {
        if ($this->_message_format == 1) {
            $this->_message_text = stripslashes($message_text);
        } else {
            $this->_message_text = StringsHelper::escapeString($message_text);
        }
    }

    /**
     * @param $message_format
     */
    public function setMessageFormat($message_format)
    {
        $this->_message_format = $message_format;
    }
}
