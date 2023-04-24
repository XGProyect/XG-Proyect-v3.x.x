<?php

namespace App\Models\Game;

use App\Core\Model;

class Shortcuts extends Model
{
    public function updateShortcuts(int $user_id, string $shortcuts): void
    {
        $this->db->query(
            'UPDATE `' . USERS . "` u SET
                u.`user_fleet_shortcuts` = '" . $shortcuts . "'
            WHERE u.`user_id` = '" . $user_id . "'"
        );
    }
}
