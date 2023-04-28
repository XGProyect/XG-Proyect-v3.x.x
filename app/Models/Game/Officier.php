<?php

namespace App\Models\Game;

use App\Core\Model;

class Officier extends Model
{
    /**
     * Set premium access to the current user
     *
     * @param integer $user_id
     * @param integer $price
     * @param string $officier
     * @param integer $time_to_add
     * @return void
     */
    public function setPremium(int $user_id, int $price, string $officier, int $time_to_add): void
    {
        if ($user_id > 0) {
            $this->db->query(
                'UPDATE `' . PREMIUM . "` SET
                    `premium_dark_matter` = `premium_dark_matter` - '" . $price . "',
                    `" . $officier . "` = '" . $time_to_add . "'
                WHERE `premium_user_id` = '" . $user_id . "';"
            );
        }
    }
}
