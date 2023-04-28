<?php

namespace App\Models\Game;

use App\Core\Model;

class Shipyard extends Model
{
    /**
     * Update the planets table, set the items to build and reduce the resources
     *
     * @param array $planet Current planet data
     *
     * @return void
     */
    public function insertItemsToBuild($resources, $shipyard_queue, $planet_id)
    {
        $this->db->query(
            'UPDATE ' . PLANETS . " AS p SET
                p.`planet_b_hangar_id` = CONCAT(p.`planet_b_hangar_id`, '" . $shipyard_queue . "'),
                p.`planet_metal` = '" . $resources['metal'] . "',
                p.`planet_crystal` = '" . $resources['crystal'] . "',
                p.`planet_deuterium` = '" . $resources['deuterium'] . "'
            WHERE p.`planet_id` = '" . $planet_id . "';"
        );
    }
}
