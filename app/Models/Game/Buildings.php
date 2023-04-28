<?php

namespace App\Models\Game;

use App\Core\Model;

class Buildings extends Model
{
    public function updatePlanetBuildingQueue(array $planet): void
    {
        $this->db->query(
            'UPDATE `' . PLANETS . "` SET
                `planet_b_building` = '" . $planet['planet_b_building'] . "',
                `planet_b_building_id` = '" . $planet['planet_b_building_id'] . "'
            WHERE `planet_id` = '" . $planet['planet_id'] . "';"
        );
    }
}
