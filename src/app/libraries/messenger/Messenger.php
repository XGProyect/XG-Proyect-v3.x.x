<?php

namespace App\Libraries\Messenger;

use App\Libraries\Functions;

final class Messenger
{
    /**
     * Contains the model
     *
     * @var Messenger
     */
    private $messengerModel;

    public function __construct()
    {
        // load model
        $this->messengerModel = Functions::model('libraries/messenger/messenger');
    }

    /**
     * Send a message with the provided options
     *
     * @param \App\Libraries\MessagesOptions $options
     */
    public function sendMessage(MessagesOptions $options)
    {
        $this->messengerModel->insertMessage($options);
    }
}
