<?php
/**
 * XG Proyect
 *
 * Open-source OGame Clon
 *
 * This content is released under the GPL-3.0 License
 *
 * Copyright (c) 2008-2020 XG Proyect
 *
 * @package    XG Proyect
 * @author     XG Proyect Team
 * @copyright  2008-2020 XG Proyect
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0 License
 * @link       https://github.com/XGProyect/
 * @since      Version 3.0.0
 */

namespace App\libraries\messenger;

use App\libraries\Functions;

/**
 * Messenger class
 */
final class Messenger
{
    /**
     * Contains the model
     *
     * @var Messenger
     */
    private $messengerModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        // load model
        $this->messengerModel = Functions::model('libraries/messenger/messenger');
    }

    /**
     * Send a message with the provided options
     *
     * @param \App\libraries\MessagesOptions $options
     */
    public function sendMessage(MessagesOptions $options)
    {
        $this->messengerModel->insertMessage($options);
    }
}
