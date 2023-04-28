<?php

namespace App\Libraries\Messenger;

use App\Models\Libraries\Messenger\Messenger as MessengerModel;

final class Messenger
{
    private $messengerModel;

    public function __construct()
    {
        $this->messengerModel = new MessengerModel();
    }

    public function sendMessage(MessagesOptions $options): void
    {
        $this->messengerModel->insertMessage($options);
    }
}
