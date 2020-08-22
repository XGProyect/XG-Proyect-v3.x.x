<?php
/**
 * Functions Library
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
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load model
        parent::loadModel('libraries/messenger/messenger');
    }

    /**
     * Send a message with the provided options
     *
     * @param \application\libraries\MessagesOptions $options
     */
    public function sendMessage(MessagesOptions $options)
    {
        $this->Messenger_Model->insertMessage($options);
    }
}

/* end of Messenger.php */
