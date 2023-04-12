<?php

namespace App\Core\Entity;

use App\Core\Entity;

class BuddyEntity extends Entity
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Return the buddy id
     *
     * @return string
     */
    public function getBuddyId()
    {
        return $this->data['buddy_id'];
    }

    /**
     * Return the buddy sender
     *
     * @return string
     */
    public function getBuddySender()
    {
        return $this->data['buddy_sender'];
    }

    /**
     * Return the buddy receiver
     *
     * @return string
     */
    public function getBuddyReceiver()
    {
        return $this->data['buddy_receiver'];
    }

    /**
     * Return the buddy status
     *
     * @return string
     */
    public function getBuddyStatus()
    {
        return $this->data['buddy_status'];
    }

    /**
     * Return the buddy request text
     *
     * @return string
     */
    public function getRequestText()
    {
        return $this->data['buddy_request_text'];
    }
}
