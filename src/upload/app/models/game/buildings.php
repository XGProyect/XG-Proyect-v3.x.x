<?php
/**
 * Buildings Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
namespace App\models\game;

use App\core\Model;

/**
 * Buildings Class
 */
class Buildings extends Model
{
    /**
     * Insert a new building queue and deduct resources
     *
     * @param array $planet
     * @return void
     */
    public function updatePlanetBuildingQueue(array $planet): void
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` SET
                `planet_b_building` = '" . $planet['planet_b_building'] . "',
                `planet_b_building_id` = '" . $planet['planet_b_building_id'] . "'
            WHERE `planet_id` = '" . $planet['planet_id'] . "';"
        );
    }
}
