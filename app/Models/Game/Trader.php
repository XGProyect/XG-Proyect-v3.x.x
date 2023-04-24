<?php

namespace App\Models\Game;

use App\Core\Model;

class Trader extends Model
{
    public function refillStorage(int $dark_matter, string $resource, float $amount, int $user_id, int $planet_id): void
    {
        $this->db->query(
            'UPDATE `' . PREMIUM . '` pr, `' . PLANETS . "` p SET
            pr.`premium_dark_matter` = pr.`premium_dark_matter` - '" . $dark_matter . "',
            p.`planet_" . $resource . "` = '" . $amount . "'
            WHERE pr.`premium_user_id` = '" . $user_id . "'
                AND p.`planet_id` = '" . $planet_id . "';"
        );
    }
}
